import serial, time

ser = serial.Serial('COM3', 115200, timeout=5)
print("Port opened. Waiting for WEMOS...")
time.sleep(6)
ser.reset_input_buffer()
ser.write(b'SENSOR\n')
ser.flush()
print("SENSOR command sent. Waiting for response...")
time.sleep(3)
output = []
while ser.in_waiting:
    output.append(ser.readline().decode('utf-8', errors='ignore').strip())
ser.close()

if output:
    for line in output:
        print("GOT:", line)
else:
    print("NO RESPONSE from WEMOS")
