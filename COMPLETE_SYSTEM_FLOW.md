# 🐦 SQUIFM - SMART QUAIL FARM MANAGEMENT SYSTEM
## COMPLETE SYSTEM FLOW DOCUMENTATION

---

## 📋 TABLE OF CONTENTS
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Core Modules](#core-modules)
4. [Database Structure](#database-structure)
5. [Hardware Integration](#hardware-integration)
6. [User Flow](#user-flow)
7. [API Endpoints](#api-endpoints)
8. [Background Processes](#background-processes)

---

## 🎯 SYSTEM OVERVIEW

**SQUIFM** ay isang Smart Quail Farm Management System na may mga sumusunod na features:

### Main Features:
1. **Automated Feeding System** - Scheduled at manual feeding gamit ang servo motor
2. **Temperature & Humidity Monitoring** - Real-time monitoring gamit ang DHT22 sensor
3. **Quail Breed Detection** - AI-powered breed classification gamit ang TensorFlow/Keras
4. **Inventory Management** - Egg collection, sales, at stock tracking
5. **Analytics Dashboard** - Comprehensive farm analytics at reports
6. **Order Management** - Customer orders at GCash payment tracking
7. **Multi-language Support** - English at Tagalog

### Technology Stack:
- **Backend**: Laravel 11 (PHP)
- **Frontend**: Blade Templates, JavaScript, Tailwind CSS
- **Database**: MySQL
- **Hardware**: Arduino/WEMOS D1 (ESP8266), DHT22 Sensor, Servo Motor
- **Python Scripts**: Hardware bridge para sa serial communication
- **AI/ML**: TensorFlow/Keras para sa quail breed detection

---

## 🏗️ ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                        │
│  (Dashboard, Feeder, Inventory, Analytics, Detection)       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    LARAVEL APPLICATION                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Controllers  │  │   Models     │  │   Services   │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└────────┬────────────────────┬────────────────┬─────────────┘
         │                    │                │
         ▼                    ▼                ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│  MySQL Database │  │  Python Scripts │  │  Hardware       │
│  - Users        │  │  - servo_bridge │  │  - WEMOS D1     │
│  - Schedules    │  │  - dht22_bridge │  │  - DHT22 Sensor │
│  - Feed History │  │  - ML Model     │  │  - Servo Motor  │
│  - Sensors      │  └─────────────────┘  └─────────────────┘
│  - Breeds       │
│  - Orders       │
└─────────────────┘
```

---

## 🔧 CORE MODULES

### 1. AUTHENTICATION MODULE
**Location**: `app/Http/Controllers/Auth/`

**Flow**:
```
User → Login Page → LoginController
                    ↓
              Validate Credentials
                    ↓
              Create Session
                    ↓
              Redirect to Dashboard
```

**Files**:
- `LoginController.php` - Login logic
- `RegisterController.php` - Registration logic
- `ForgotPasswordController.php` - Password reset

**Routes**:
- `GET /login` - Show login form
- `POST /login` - Process login
- `GET /register` - Show registration form
- `POST /register` - Process registration
- `POST /logout` - Logout user

---

### 2. FEEDING SYSTEM MODULE
**Location**: `app/Http/Controllers/FeedingScheduleController.php`

**Flow ng Scheduled Feeding**:
```
1. User creates schedule → FeedingSchedule table
2. Cron/Scheduler checks every minute
3. autoCheckSchedules() method runs
4. Kung match ang time at day:
   ↓
5. Update status to 'feeding_now'
6. Write servo_command.json
7. servo_bridge.py detects command
8. Send TRIGGER command to WEMOS
9. Servo opens → dispense food → servo closes
10. Update status to 'done_feeding'
11. Record to FeedHistory
12. Send SMS notification (optional)
```

**Flow ng Manual Feeding**:
```
1. User clicks "Feed Now" button
2. manualFeed() method
3. Create ManualFeed record
4. Write servo_command.json with type='manual'
5. servo_bridge.py executes
6. Servo dispenses food
7. Update status to 'done_feeding'
8. Record to FeedHistory
```

**Key Files**:
- `FeedingScheduleController.php` - Main controller
- `ManualFeed.php` - Model para sa manual feeds
- `FeedingSchedule.php` - Model para sa schedules
- `FeedHistory.php` - Model para sa history
- `servo_bridge.py` - Python script na nag-control ng servo

**Database Tables**:
- `feeding_schedules` - Scheduled feeding times
- `manual_feeds` - Manual feeding records
- `feed_history` - Complete feeding history

**Important Methods**:
- `autoCheckSchedules()` - Checks schedules every minute
- `manualFeed()` - Triggers manual feeding
- `triggerManualFeeding()` - Writes servo command
- `store()` - Creates new schedule

---

### 3. TEMPERATURE & HUMIDITY MONITORING
**Location**: `app/Http/Controllers/TemperatureHumidityController.php`

**Flow**:
```
1. dht22_bridge.py runs continuously
2. Every 30 seconds:
   ↓
3. Send SENSOR command to WEMOS
4. WEMOS reads DHT22 sensor
5. Returns temperature & humidity
6. Python script parses data
7. POST to /api/sensor-data
8. Save to sensor_readings table
9. Dashboard polls /api/sensor-data/latest
10. Display real-time data
```

**Key Files**:
- `TemperatureHumidityController.php` - Controller
- `SensorDataController.php` - API controller
- `SensorReading.php` - Model
- `dht22_bridge.py` - Python bridge script
- `servo_dht22_combined.ino` - Arduino code

**API Endpoints**:
- `POST /api/sensor-data` - Store sensor reading
- `GET /api/sensor-data/latest` - Get latest reading
- `GET /api/sensor-data/history` - Get historical data
- `GET /api/sensor-data/average` - Get average readings

**Database Table**:
- `sensor_readings` - Temperature & humidity records

---

### 4. QUAIL BREED DETECTION MODULE
**Location**: `app/Http/Controllers/QuailDetectionController.php`

**Flow**:
```
1. User uploads quail image
2. classifyBreed() method
3. Save image temporarily
4. Call classify_quail_breed.py
5. Python loads TensorFlow model
6. Predict breed from image
7. Return prediction + confidence
8. Save to DetectionHistory
9. Display breed information
```

**ML Model Details**:
- **Model**: TensorFlow/Keras CNN
- **Location**: `public/models/quail_breed_model.keras`
- **Metadata**: `public/models/model_metadata.json`
- **Training Script**: `train_quail_model.py`
- **Dataset**: `datasets/quail_images/`

**Supported Breeds**:
1. Japanese Quail (Coturnix japonica)
2. Taiwan Crossbreed
3. Pharaoh Quail


**Key Files**:
- `QuailDetectionController.php` - Controller
- `DetectionHistory.php` - Model para sa detection records
- `QuailBreed.php` - Model para sa breed information
- `classify_quail_breed.py` - ML classification script
- `train_quail_model.py` - Model training script

**Database Tables**:
- `detection_histories` - Detection records
- `quail_breeds` - Breed information
- `feed_items` - Compatible feeds per breed

---

### 5. INVENTORY MANAGEMENT MODULE
**Location**: Multiple controllers

**Components**:

#### A. Egg Collection
**Controller**: `EggCollectionController.php`

**Flow**:
```
1. User records egg collection
2. Input: total_eggs, good_eggs, cracked_eggs
3. Save to egg_collections table
4. Update inventory count
5. Calculate quality rate
6. Display in analytics
```

**Database Table**: `egg_collections`

#### B. Sales Management
**Controller**: `SalesController.php`

**Flow**:
```
1. User records sale
2. Input: product, quantity, price
3. Save to sales table
4. Update product stock
5. Calculate revenue
6. Display in analytics
```

**Database Table**: `sales`

#### C. Product Management
**Controller**: `ProductController.php`

**Products**:
- Fresh Quail Eggs (₱80/tray)
- Live Quail (₱180/piece)
- Dressed Quail (₱250/piece)

**Database Table**: `products`

---

### 6. ORDER MANAGEMENT MODULE
**Location**: `ContactSupportController.php`

**Flow**:
```
1. Customer fills order form
2. Upload GCash payment proof
3. Save to contact_messages table
4. Admin views orders in dashboard
5. Update order status (pending/done/cancelled)
6. Send confirmation
```

**Order Statuses**:
- `pending` - Waiting for processing
- `done` - Completed
- `cancelled` - Cancelled

**Database Table**: `contact_messages`

**GCash Proof Storage**: `public/uploads/gcash/`

---

### 7. ANALYTICS MODULE
**Location**: `AnalyticsController.php`

**Metrics Tracked**:

#### Production Metrics:
- Total eggs collected
- Good eggs vs cracked eggs
- Quality rate (%)
- Eggs per day average

#### Sales Metrics:
- Total revenue
- Revenue by product
- Orders completed
- Best selling product
- Top customers

#### Financial Metrics:
- Total expenses (feed, labor, utilities)
- Net profit
- Profit margin (%)
- Cost per egg
- Profit per egg

#### Operational Metrics:
- Active days
- Average orders per day
- Year-to-date growth
- Monthly comparisons

**Charts**:
- Daily orders trend
- Daily egg collection
- Monthly revenue vs production
- Product sales distribution

---

### 8. FARM SETTINGS MODULE
**Location**: `SettingsController.php`

**Settings Stored**:
- Farm name, address, contact
- Live quail count
- Dead quail count
- Current quail breed
- Feeding times (morning, afternoon, evening)
- Feed brand preference

**Database Table**: `farm_settings`

---

## 💾 DATABASE STRUCTURE

### Core Tables:

#### 1. users
```sql
- id
- name
- email
- phone
- password
- remember_token
- created_at, updated_at
```

#### 2. feeding_schedules
```sql
- id
- time (HH:MM:SS)
- days (JSON array)
- amount (seconds)
- cage_number
- enabled (boolean)
- status (idle/feeding_now/done_feeding)
- last_feed_at
- feeding_started_at
- created_at, updated_at
```

#### 3. manual_feeds
```sql
- id
- duration (seconds)
- cage_number
- days (JSON array)
- status
- started_at
- completed_at
- created_at, updated_at
```

#### 4. feed_history
```sql
- id
- feed_type (scheduled/manual)
- feed_brand
- schedule_id (nullable)
- manual_feed_id (nullable)
- duration
- cage_number
- days (JSON array)
- status (completed/failed)
- fed_at
- created_at, updated_at
```

#### 5. sensor_readings
```sql
- id
- temperature (float)
- humidity (float)
- recorded_at
- created_at, updated_at
```

#### 6. quail_breeds
```sql
- id
- name
- scientific_name
- description
- color_markings
- size_category
- egg_production_rate
- mature_weight
- maturity_age
- optimal_temperature
- optimal_humidity
- care_requirements (JSON)
- recommended_feeds (JSON)
- common_diseases (JSON)
- image_url
- is_active
- created_at, updated_at
```

#### 7. detection_histories
```sql
- id
- user_id
- quail_breed_id
- detection_type (quail/human/unknown)
- confidence (0-1)
- location
- detection_notes
- image_path
- created_at, updated_at
```

#### 8. egg_collections
```sql
- id
- total_eggs
- good_eggs
- cracked_eggs
- egg_size (small/medium/large)
- feed_brand
- cage_number
- notes
- created_at, updated_at
```

#### 9. sales
```sql
- id
- product_id
- quantity
- unit_price
- total_price
- customer_name
- customer_contact
- sale_date
- notes
- created_at, updated_at
```

#### 10. products
```sql
- id
- name
- description
- unit
- price
- stock
- is_active
- created_at, updated_at
```

#### 11. contact_messages
```sql
- id
- fullname
- email
- subject
- message
- phone
- location
- gcash_proof (image path)
- status (pending/done/cancelled)
- read (boolean)
- created_at, updated_at
```

#### 12. farm_settings
```sql
- id
- key
- value
- created_at, updated_at
```

---

## 🔌 HARDWARE INTEGRATION

### Hardware Components:

#### 1. WEMOS D1 R1 (ESP8266)
**Purpose**: Main microcontroller
**Connections**:
- DHT22 sensor (D4 pin)
- Servo motor (D1 pin)
- USB to computer (COM4)

**Firmware**: `scripts/servo_dht22_combined.ino`

**Commands**:
- `TRIGGER <seconds>` - Open servo for X seconds
- `SENSOR` - Read temperature & humidity
- `STATUS` - Get device status

#### 2. DHT22 Sensor
**Purpose**: Temperature & humidity monitoring
**Specifications**:
- Temperature range: -40°C to 80°C
- Humidity range: 0% to 100%
- Accuracy: ±0.5°C, ±2% RH

#### 3. Servo Motor
**Purpose**: Automated feeding mechanism
**Specifications**:
- Type: SG90 or similar
- Operating voltage: 4.8V - 6V
- Rotation: 0° to 180°

### Python Bridge Scripts:

#### 1. servo_bridge.py
**Purpose**: Watches for feeding commands and controls servo

**Flow**:
```
1. Run continuously in background
2. Watch servo_command.json file
3. When file appears:
   - Read command (duration, cage, type)
   - Open serial connection to WEMOS
   - Send TRIGGER command
   - For scheduled: Create animation_trigger.json
   - Wait for completion
   - Delete animation file
   - Write result to servo_result.json
4. Repeat
```

**Start Command**: `pythonw servo_bridge.py`

#### 2. dht22_bridge.py
**Purpose**: Reads sensor data and posts to API

**Flow**:
```
1. Run continuously in background
2. Every 30 seconds:
   - Check if servo is active (serial.lock)
   - If not, open serial connection
   - Send SENSOR command to WEMOS
   - Parse temperature & humidity
   - POST to /api/sensor-data
   - Close serial connection
3. Repeat
```

**Start Command**: `python dht22_bridge.py`

### Serial Communication:
- **Port**: COM4 (configurable in .env)
- **Baud Rate**: 115200
- **Protocol**: Simple text commands
- **Lock File**: `storage/logs/serial.lock` prevents conflicts

---

## 👤 USER FLOW

### 1. Login Flow
```
1. Visit http://localhost:8000
2. Click "Login"
3. Enter email & password
4. Submit → Validate → Create session
5. Redirect to Dashboard
```

### 2. Dashboard Flow
```
1. View farm overview
   - Live quail count
   - Current breed
   - Recent activities
2. Quick actions:
   - Manual feed
   - View analytics
   - Check temperature
3. Real-time updates:
   - Feeding status
   - Sensor readings
   - Order notifications
```

### 3. Feeding Flow

#### Scheduled Feeding:
```
1. Go to Feeder page
2. Click "Add Schedule"
3. Select date/time, duration, cage
4. Save schedule
5. System auto-triggers at scheduled time
6. View in feed history
```

#### Manual Feeding:
```
1. Go to Feeder page
2. Click "Feed Now"
3. Select duration & cage
4. Confirm
5. Watch animation (if scheduled)
6. Feeding completes
7. View in feed history
```

### 4. Temperature Monitoring Flow
```
1. Go to Temperature & Humidity page
2. View current readings
3. View 24-hour chart
4. Compare with optimal ranges
5. View Philippine weather
6. Export data (optional)
```

### 5. Quail Detection Flow
```
1. Go to Quail Detection page
2. Upload quail image
3. AI analyzes image
4. View predicted breed
5. View breed information
6. View compatible feeds
7. Save detection to history
```

### 6. Inventory Flow
```
1. Go to Inventory page
2. Record egg collection:
   - Total eggs
   - Good eggs
   - Cracked eggs
   - Egg size
3. Record sales:
   - Product
   - Quantity
   - Price
4. View inventory summary
5. Export reports
```

### 7. Analytics Flow
```
1. Go to Analytics page
2. Select date range
3. View metrics:
   - Production
   - Sales
   - Revenue
   - Expenses
   - Profit
4. View charts:
   - Daily trends
   - Monthly comparison
   - Product distribution
5. Export reports (CSV/PDF)
```

### 8. Order Management Flow
```
1. Customer visits order page
2. Fill order form:
   - Product
   - Quantity
   - Contact info
   - Location
3. Upload GCash proof
4. Submit order
5. Admin views in dashboard
6. Update order status
7. Customer receives confirmation
```

---

## 🌐 API ENDPOINTS

### Authentication
- `POST /login` - Login
- `POST /register` - Register
- `POST /logout` - Logout

### Feeding System
- `GET /api/feeder/status` - Get feeding status
- `POST /feeder/feed` - Manual feed
- `GET /api/feeder/history` - Get feed history
- `GET /api/feeder/animation` - Get animation status
- `GET /api/feeder/feeding-notification` - Get notification
- `POST /api/feeder/confirm-feeding` - Confirm feeding

### Sensor Data
- `POST /api/sensor-data` - Store sensor reading
- `GET /api/sensor-data/latest` - Get latest reading
- `GET /api/sensor-data/history` - Get historical data
- `GET /api/sensor-data/average` - Get average readings
- `GET /api/weather/philippines` - Get Philippine weather

### Quail Detection
- `POST /quail-detection/classify` - Classify breed
- `POST /quail-detection/save` - Save detection
- `GET /quail-detection/history` - Get detection history
- `GET /quail-detection/stats` - Get detection stats
- `GET /quail-detection/breed/{id}` - Get breed info

### Analytics
- `GET /api/analytics` - Get analytics data
- `GET /api/sales` - Get sales data
- `GET /api/egg-collections` - Get egg collections

### Products
- `GET /api/products` - Get all products
- `POST /products/{id}/price` - Update price
- `POST /products/{id}/stock` - Update stock

### Orders
- `POST /order` - Create order
- `POST /orders/{id}/status` - Update order status
- `POST /orders/{id}/delete` - Delete order

### Breeds
- `GET /api/breeds` - Get all breeds
- `GET /api/breeds/current` - Get current breed
- `POST /api/breeds/set-current` - Set current breed

---

## ⚙️ BACKGROUND PROCESSES

### 1. Feeding Scheduler
**File**: `FeedingScheduleController::autoCheckSchedules()`

**Trigger**: Cron job every minute

**Process**:
```
1. Get current time (HH:MM)
2. Get current day (mon/tue/wed/etc)
3. Query enabled schedules matching time & day
4. For each match:
   - Update status to 'feeding_now'
   - Write servo_command.json
   - Trigger background process to mark done
5. Return triggered schedules
```

**Setup**:
```bash
# Add to Windows Task Scheduler
php artisan schedule:run
```

### 2. Servo Bridge
**File**: `scripts/servo_bridge.py`

**Trigger**: Runs continuously

**Process**:
```
1. Watch servo_command.json
2. When file appears:
   - Parse command
   - Open serial to WEMOS
   - Send TRIGGER command
   - Create animation file (if scheduled)
   - Wait for completion
   - Delete animation file
   - Write result
3. Loop
```

**Start**:
```bash
pythonw servo_bridge.py
```

### 3. DHT22 Bridge
**File**: `scripts/dht22_bridge.py`

**Trigger**: Runs continuously

**Process**:
```
1. Every 30 seconds:
   - Check serial lock
   - Open serial to WEMOS
   - Send SENSOR command
   - Parse response
   - POST to API
   - Close serial
2. Loop
```

**Start**:
```bash
python dht22_bridge.py
```

### 4. Stale Cleanup
**Location**: Various controllers

**Purpose**: Clean up stale records

**Process**:
```
1. Find feeding_now older than 5 minutes
2. Update to idle
3. Find pending commands older than 5 minutes
4. Update to failed
```

---

## 🔐 SECURITY FEATURES

### 1. Authentication
- Password hashing (bcrypt)
- Session management
- CSRF protection
- Remember me token

### 2. Authorization
- Middleware protection
- Route guards
- User role checking

### 3. Input Validation
- Request validation
- SQL injection prevention
- XSS protection
- File upload validation

### 4. Data Protection
- Environment variables (.env)
- Secure file storage
- Database encryption (optional)

---

## 🚀 DEPLOYMENT CHECKLIST

### 1. Environment Setup
- [ ] Install PHP 8.2+
- [ ] Install MySQL
- [ ] Install Composer
- [ ] Install Python 3.x
- [ ] Install pyserial (`pip install pyserial`)

### 2. Application Setup
- [ ] Clone repository
- [ ] Copy .env.example to .env
- [ ] Set database credentials
- [ ] Set serial port (COM4)
- [ ] Run `composer install`
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan db:seed`

### 3. Hardware Setup
- [ ] Connect WEMOS D1 to computer
- [ ] Upload Arduino firmware
- [ ] Connect DHT22 sensor
- [ ] Connect servo motor
- [ ] Test serial communication

### 4. Background Processes
- [ ] Start servo_bridge.py
- [ ] Start dht22_bridge.py
- [ ] Setup task scheduler for cron

### 5. Testing
- [ ] Test login/register
- [ ] Test manual feeding
- [ ] Test scheduled feeding
- [ ] Test sensor readings
- [ ] Test quail detection
- [ ] Test order creation

---

## 📞 SUPPORT & MAINTENANCE

### Common Issues:

#### 1. Servo not working
**Solution**:
- Check COM port in .env
- Verify servo_bridge.py is running
- Check serial connection
- Test with `test_servo.bat`

#### 2. Sensor not reading
**Solution**:
- Check DHT22 connection
- Verify dht22_bridge.py is running
- Check serial lock file
- Test with `test_wemos_simple.py`

#### 3. Schedule not triggering
**Solution**:
- Check cron job is running
- Verify schedule is enabled
- Check time format (HH:MM)
- Check day selection

#### 4. ML model not working
**Solution**:
- Verify model file exists
- Check Python dependencies
- Test with sample image
- Check file permissions

---

## 📝 NOTES

### Important Files:
- `.env` - Environment configuration
- `routes/web.php` - Web routes
- `routes/api.php` - API routes
- `config/app.php` - App configuration
- `storage/logs/laravel.log` - Application logs

### Batch Files:
- `START_EVERYTHING.bat` - Start all services
- `start_servo_bridge.bat` - Start servo bridge
- `start_dht22_bridge.bat` - Start DHT22 bridge
- `test_servo.bat` - Test servo
- `clear-cache.bat` - Clear Laravel cache

### Documentation Files:
- `README.md` - Project overview
- `FEEDER_SETUP_GUIDE.md` - Feeder setup
- `SERVO_SYSTEM_GUIDE.md` - Servo system guide
- `ML_TRAINING_GUIDE.md` - ML model training
- `COMPLETE_SYSTEM_FLOW.md` - This file

---

## 🎓 LEARNING RESOURCES

### Laravel:
- https://laravel.com/docs
- https://laracasts.com

### Arduino/ESP8266:
- https://arduino.cc/reference
- https://arduino-esp8266.readthedocs.io

### TensorFlow:
- https://tensorflow.org/tutorials
- https://keras.io/guides

### Python Serial:
- https://pyserial.readthedocs.io

---

## 📊 SYSTEM STATISTICS

### Code Statistics:
- **Controllers**: 20+
- **Models**: 15+
- **Migrations**: 35+
- **Routes**: 100+
- **Views**: 30+
- **Python Scripts**: 10+
- **Arduino Sketches**: 5+

### Database:
- **Tables**: 15+
- **Relationships**: Many-to-many, One-to-many
- **Indexes**: Optimized for queries

### Features:
- **Modules**: 8 major modules
- **API Endpoints**: 50+
- **Background Processes**: 3
- **Hardware Devices**: 3

---

## 🏆 CREDITS

**Developed by**: CAPSTONE SQUIFM Team
**Technology**: Laravel, Python, Arduino, TensorFlow
**Purpose**: Smart Quail Farm Management

---

**Last Updated**: 2025
**Version**: 1.0
**Status**: Production Ready ✅
