
#include <Servo.h>
#include <DHT.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecure.h>

// ==================================================
// WIFI/API CONFIGURATION
// ==================================================

const char* WIFI_SSID = "realmeC51";
const char* WIFI_PASSWORD = "realme51";

// LIVE Q-MATE RENDER SERVER
const char* API_BASE_URL = "https://q-mate.onrender.com/api";

const unsigned long COMMAND_INTERVAL = 2000;
const unsigned long SENSOR_INTERVAL = 10000;

unsigned long lastCommandCheck = 0;
unsigned long lastSensorUpload = 0;

// ==================================================
// PIN CONFIGURATION
// ==================================================

#define SERVO_PIN D5
#define DHT_PIN   D6
#define DHT_TYPE  DHT22

#define TRIG_PIN  D7
#define ECHO_PIN  D2

// ==================================================
// SERVO POSITION
// ==================================================

#define SERVO_CLOSED 0
#define SERVO_OPEN   180

// ==================================================
// FEED LEVEL RANGE
// ==================================================

// 1.5 ruler = 45 cm
#define FEED_MAX_DISTANCE 45.0

// Minimum practical distance of HC-SR04
#define FEED_MIN_DISTANCE 2.0

// ==================================================
// OBJECTS
// ==================================================

Servo myServo;
DHT dht(DHT_PIN, DHT_TYPE);

// ==================================================
// CURRENT SERVO POSITION
// ==================================================

int servoPosition = SERVO_CLOSED;

// ==================================================
// WIFI CONNECTION
// ==================================================

void connectWiFi() {

  if (WiFi.status() == WL_CONNECTED) {
    return;
  }

  Serial.print("Connecting to WiFi");

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  unsigned long startedAt = millis();

  while (
    WiFi.status() != WL_CONNECTED &&
    millis() - startedAt < 30000
  ) {

    delay(500);
    Serial.print(".");
  }

  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {

    Serial.println("WiFi connected.");

    Serial.print("Wemos IP: ");
    Serial.println(WiFi.localIP());

    Serial.println("Q-MATE API:");
    Serial.println(API_BASE_URL);

  } else {

    Serial.println(
      "WiFi connection failed; retrying later."
    );
  }
}

// ==================================================
// BUILD API URL
// ==================================================

String apiUrl(const char* path) {

  return String(API_BASE_URL) + path;
}

// ==================================================
// GET INTEGER JSON VALUE
// ==================================================

bool getJsonValue(
  const String& body,
  const char* key,
  int& value
) {

  String marker =
    String("\"") + key + "\":";

  int start = body.indexOf(marker);

  if (start < 0) {
    return false;
  }

  start += marker.length();

  while (
    start < (int)body.length() &&
    body[start] == ' '
  ) {

    start++;
  }

  int end = start;

  while (
    end < (int)body.length() &&
    isDigit(body[end])
  ) {

    end++;
  }

  if (end == start) {
    return false;
  }

  value =
    body.substring(start, end).toInt();

  return true;
}

// ==================================================
// GET PENDING FEEDER COMMAND
// ==================================================

bool getPendingCommand(
  int& commandId,
  int& duration
) {

  WiFiClientSecure client;

  // Render uses HTTPS.
  // This allows the ESP8266 to connect to the
  // live Q-MATE Render server.
  client.setInsecure();

  HTTPClient http;

  String url =
    apiUrl("/feeder/pending-command");

  Serial.print("Checking Q-MATE command: ");
  Serial.println(url);

  if (!http.begin(client, url)) {

    Serial.println(
      "API connection setup failed."
    );

    return false;
  }

  int status = http.GET();

  String body =
    status > 0
      ? http.getString()
      : "";

  http.end();

  if (
    status != HTTP_CODE_OK ||
    body.indexOf("\"has_command\":true") < 0
  ) {

    return false;
  }

  return
    getJsonValue(
      body,
      "command_id",
      commandId
    ) &&
    getJsonValue(
      body,
      "duration",
      duration
    );
}

// ==================================================
// REPORT COMMAND DONE
// ==================================================

void reportCommandDone(
  int commandId
) {

  WiFiClientSecure client;

  client.setInsecure();

  HTTPClient http;

  String url =
    apiUrl("/feeder/command-done");

  if (!http.begin(client, url)) {

    Serial.println(
      "Unable to report completed command."
    );

    return;
  }

  http.addHeader(
    "Content-Type",
    "application/json"
  );

  String payload =
    String("{\"command_id\":") +
    commandId +
    "}";

  int status =
    http.POST(payload);

  Serial.print(
    "Command completion response: "
  );

  Serial.println(status);

  http.end();
}

// ==================================================
// CALCULATE FEED LEVEL
// ==================================================

int calculateFeedLevel() {

  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);

  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);

  digitalWrite(TRIG_PIN, LOW);

  long duration =
    pulseIn(
      ECHO_PIN,
      HIGH,
      5000
    );

  if (duration == 0) {

    return 0;
  }

  float distance =
    duration * 0.0343 / 2;

  if (distance <= FEED_MIN_DISTANCE) {

    return 100;
  }

  if (distance >= FEED_MAX_DISTANCE) {

    return 0;
  }

  int level =
    (
      (FEED_MAX_DISTANCE - distance) /
      (FEED_MAX_DISTANCE - FEED_MIN_DISTANCE)
    ) * 100;

  return constrain(level, 0, 100);
}

// ==================================================
// UPLOAD TEMPERATURE + HUMIDITY + FOOD LEVEL
// ==================================================

void uploadSensors() {

  float humidity =
    dht.readHumidity();

  float temperature =
    dht.readTemperature();

  int level =
    calculateFeedLevel();

  if (
    isnan(humidity) ||
    isnan(temperature)
  ) {

    Serial.println(
      "Skipping upload: DHT22 read failed."
    );

    return;
  }

  // ==================================================
  // UPLOAD TEMPERATURE / HUMIDITY
  // ==================================================

  WiFiClientSecure client;

  client.setInsecure();

  HTTPClient http;

  if (
    !http.begin(
      client,
      apiUrl("/sensor-data")
    )
  ) {

    Serial.println(
      "Sensor API connection setup failed."
    );

    return;
  }

  http.addHeader(
    "Content-Type",
    "application/json"
  );

  String sensorPayload =
    String("{\"temperature\":") +
    String(temperature, 2) +
    ",\"humidity\":" +
    String(humidity, 2) +
    "}";

  int sensorStatus =
    http.POST(sensorPayload);

  http.end();

  // ==================================================
  // UPLOAD FOOD LEVEL
  // ==================================================

  if (
    !http.begin(
      client,
      apiUrl("/food-level")
    )
  ) {

    Serial.println(
      "Food-level API connection setup failed."
    );

    return;
  }

  http.addHeader(
    "Content-Type",
    "application/json"
  );

  String levelPayload =
    String("{\"level\":") +
    level +
    ",\"source\":\"ultrasonic\"}";

  int levelStatus =
    http.POST(levelPayload);

  http.end();

  // ==================================================
  // DISPLAY RESULT
  // ==================================================

  Serial.print(
    "Uploaded sensors. DHT="
  );

  Serial.print(sensorStatus);

  Serial.print(
    ", food level="
  );

  Serial.println(levelStatus);
}

// ==================================================
// PROCESS WIFI
// ==================================================

void processWiFi() {

  if (WiFi.status() != WL_CONNECTED) {

    connectWiFi();

    return;
  }

  unsigned long now =
    millis();

  // ==================================================
  // CHECK FEEDER COMMAND
  // ==================================================

  if (
    now - lastCommandCheck >=
    COMMAND_INTERVAL
  ) {

    lastCommandCheck = now;

    int commandId = 0;
    int duration = 0;

    if (
      getPendingCommand(
        commandId,
        duration
      )
    ) {

      Serial.print(
        "Remote feed command received: "
      );

      Serial.print(duration);

      Serial.println(
        " seconds"
      );

      // OPEN
      myServo.write(
        SERVO_OPEN
      );

      servoPosition =
        SERVO_OPEN;

      // KEEP OPEN FOR COMMAND DURATION
      delay(
        (unsigned long)duration * 1000
      );

      // CLOSE
      myServo.write(
        SERVO_CLOSED
      );

      servoPosition =
        SERVO_CLOSED;

      // TELL Q-MATE THAT FEEDING IS DONE
      reportCommandDone(
        commandId
      );
    }
  }

  // ==================================================
  // SENSOR UPLOAD
  // ==================================================

  if (
    now - lastSensorUpload >=
    SENSOR_INTERVAL
  ) {

    lastSensorUpload = now;

    uploadSensors();
  }
}

// ==================================================
// SETUP
// ==================================================

void setup() {

  Serial.begin(115200);

  delay(1000);

  // ==================================================
  // SERVO
  // ==================================================

  myServo.attach(
    SERVO_PIN
  );

  myServo.write(
    SERVO_CLOSED
  );

  servoPosition =
    SERVO_CLOSED;

  // ==================================================
  // DHT22
  // ==================================================

  dht.begin();

  // ==================================================
  // ULTRASONIC
  // ==================================================

  pinMode(
    TRIG_PIN,
    OUTPUT
  );

  pinMode(
    ECHO_PIN,
    INPUT
  );

  digitalWrite(
    TRIG_PIN,
    LOW
  );

  // ==================================================
  // WIFI
  // ==================================================

  connectWiFi();

  // ==================================================
  // START MESSAGE
  // ==================================================

  Serial.println();

  Serial.println(
    "================================="
  );

  Serial.println(
    " WEMOS D1 R1 SYSTEM STARTED"
  );

  Serial.println(
    "================================="
  );

  Serial.println(
    "Q-MATE HOST: RENDER"
  );

  Serial.println(
    "API:"
  );

  Serial.println(
    API_BASE_URL
  );

  Serial.println(
    "================================="
  );

  Serial.println(
    "Commands:"
  );

  Serial.println(
    "OPEN"
  );

  Serial.println(
    "CLOSE"
  );

  Serial.println(
    "TEMP"
  );

  Serial.println(
    "DISTANCE"
  );

  Serial.println(
    "STATUS"
  );

  Serial.println(
    "================================="
  );

  Serial.println();
}

// ==================================================
// LOOP
// ==================================================

void loop() {

  // ==================================================
  // USB COMMANDS
  // ==================================================

  if (Serial.available() > 0) {

    String command =
      Serial.readStringUntil('\n');

    command.trim();

    command.toUpperCase();

    // ==================================================
    // OPEN SERVO
    // ==================================================

    if (command == "OPEN") {

      myServo.write(
        SERVO_OPEN
      );

      servoPosition =
        SERVO_OPEN;

      Serial.println(
        "SERVO: OPEN"
      );

      Serial.println(
        "POSITION: 180 degrees"
      );
    }

    // ==================================================
    // CLOSE SERVO
    // ==================================================

    else if (command == "CLOSE") {

      myServo.write(
        SERVO_CLOSED
      );

      servoPosition =
        SERVO_CLOSED;

      Serial.println(
        "SERVO: CLOSED"
      );

      Serial.println(
        "POSITION: 0 degrees"
      );
    }

    // ==================================================
    // TEMPERATURE + HUMIDITY
    // ==================================================

    else if (command == "TEMP") {

      readTemperatureHumidity();
    }

    // ==================================================
    // FEED LEVEL
    // ==================================================

    else if (command == "DISTANCE") {

      readDistance();
    }

    // ==================================================
    // STATUS
    // ==================================================

    else if (command == "STATUS") {

      showStatus();
    }

    // ==================================================
    // UNKNOWN COMMAND
    // ==================================================

    else if (command.length() > 0) {

      Serial.println(
        "ERROR: Unknown command."
      );

      Serial.println(
        "Use: OPEN, CLOSE, TEMP, DISTANCE, STATUS"
      );
    }
  }

  // ==================================================
  // WIFI PROCESSING
  // ==================================================

  processWiFi();
}

// ==================================================
// READ TEMPERATURE + HUMIDITY
// ==================================================

void readTemperatureHumidity() {

  float humidity =
    dht.readHumidity();

  float temperature =
    dht.readTemperature();

  if (
    isnan(humidity) ||
    isnan(temperature)
  ) {

    Serial.println(
      "DHT22 ERROR: Failed to read sensor."
    );

    return;
  }

  Serial.println(
    "---------------------------------"
  );

  Serial.print(
    "Temperature: "
  );

  Serial.print(
    temperature
  );

  Serial.println(
    " °C"
  );

  Serial.print(
    "Humidity: "
  );

  Serial.print(
    humidity
  );

  Serial.println(
    " %"
  );

  Serial.println(
    "---------------------------------"
  );
}

// ==================================================
// READ FEED LEVEL
// ==================================================

void readDistance() {

  digitalWrite(
    TRIG_PIN,
    LOW
  );

  delayMicroseconds(2);

  digitalWrite(
    TRIG_PIN,
    HIGH
  );

  delayMicroseconds(10);

  digitalWrite(
    TRIG_PIN,
    LOW
  );

  long duration =
    pulseIn(
      ECHO_PIN,
      HIGH,
      5000
    );

  // ==================================================
  // NO OBJECT / NO ECHO
  // ==================================================

  if (duration == 0) {

    Serial.println(
      "---------------------------------"
    );

    Serial.println(
      "FEED LEVEL: 0%"
    );

    Serial.println(
      "---------------------------------"
    );

    return;
  }

  // ==================================================
  // CALCULATE DISTANCE INTERNALLY
  // ==================================================

  float distance =
    duration * 0.0343 / 2;

  int feedLevel;

  // ==================================================
  // VERY CLOSE = 100%
  // ==================================================

  if (
    distance <=
    FEED_MIN_DISTANCE
  ) {

    feedLevel = 100;
  }

  // ==================================================
  // BEYOND 45 CM = 0%
  // ==================================================

  else if (
    distance >=
    FEED_MAX_DISTANCE
  ) {

    feedLevel = 0;
  }

  // ==================================================
  // BETWEEN 2 CM AND 45 CM
  // ==================================================

  else {

    feedLevel =
      (
        (FEED_MAX_DISTANCE - distance) /
        (FEED_MAX_DISTANCE - FEED_MIN_DISTANCE)
      ) * 100;

    feedLevel =
      constrain(
        feedLevel,
        0,
        100
      );
  }

  // ==================================================
  // DISPLAY ONLY PERCENTAGE
  // ==================================================

  Serial.println(
    "---------------------------------"
  );

  Serial.print(
    "FEED LEVEL: "
  );

  Serial.print(
    feedLevel
  );

  Serial.println(
    "%"
  );

  Serial.println(
    "---------------------------------"
  );
}

// ==================================================
// SHOW ALL STATUS
// ==================================================

void showStatus() {

  Serial.println();

  Serial.println(
    "================================="
  );

  Serial.println(
    "           SYSTEM STATUS"
  );

  Serial.println(
    "================================="
  );

  // ==================================================
  // SERVO
  // ==================================================

  Serial.print(
    "Servo Position: "
  );

  Serial.print(
    servoPosition
  );

  Serial.println(
    " degrees"
  );

  if (
    servoPosition ==
    SERVO_OPEN
  ) {

    Serial.println(
      "Servo Status: OPEN"
    );

  } else {

    Serial.println(
      "Servo Status: CLOSED"
    );
  }

  // ==================================================
  // DHT22
  // ==================================================

  float humidity =
    dht.readHumidity();

  float temperature =
    dht.readTemperature();

  if (
    isnan(humidity) ||
    isnan(temperature)
  ) {

    Serial.println(
      "DHT22: ERROR"
    );

  } else {

    Serial.print(
      "Temperature: "
    );

    Serial.print(
      temperature
    );

    Serial.println(
      " °C"
    );

    Serial.print(
      "Humidity: "
    );

    Serial.print(
      humidity
    );

    Serial.println(
      " %"
    );
  }

  // ==================================================
  // FEED LEVEL
  // ==================================================

  digitalWrite(
    TRIG_PIN,
    LOW
  );

  delayMicroseconds(2);

  digitalWrite(
    TRIG_PIN,
    HIGH
  );

  delayMicroseconds(10);

  digitalWrite(
    TRIG_PIN,
    LOW
  );

  long duration =
    pulseIn(
      ECHO_PIN,
      HIGH,
      5000
    );

  int feedLevel = 0;

  if (duration != 0) {

    float distance =
      duration * 0.0343 / 2;

    if (
      distance <=
      FEED_MIN_DISTANCE
    ) {

      feedLevel = 100;

    } else if (
      distance >=
      FEED_MAX_DISTANCE
    ) {

      feedLevel = 0;

    } else {

      feedLevel =
        (
          (FEED_MAX_DISTANCE - distance) /
          (FEED_MAX_DISTANCE - FEED_MIN_DISTANCE)
        ) * 100;

      feedLevel =
        constrain(
          feedLevel,
          0,
          100
        );
    }
  }

  Serial.print(
    "Feed Level: "
  );

  Serial.print(
    feedLevel
  );

  Serial.println(
    "%"
  );

  Serial.println(
    "================================="
  );

  Serial.println();
}