# 📱 PAANO I-OPEN SA PHONE (LOCAL NETWORK)

## ✅ REQUIREMENTS:
1. PC at Phone ay dapat naka-connect sa SAME WiFi network
2. XAMPP MySQL ay dapat running
3. Firewall ay dapat allow ang port 8000

---

## 🚀 STEP-BY-STEP GUIDE:

### STEP 1: I-start ang MySQL
1. Buksan ang XAMPP Control Panel
2. I-click ang "Start" sa MySQL

### STEP 2: I-check ang IP Address
1. Double-click ang `get_ip.bat`
2. Copy ang IPv4 Address (example: 192.168.1.27)

### STEP 3: I-start ang Laravel Server
1. Double-click ang `start_for_phone.bat`
2. Makikita mo ang IP address at port
3. Example: http://192.168.1.27:8000

### STEP 4: I-open sa Phone
1. Siguraduhing naka-connect ang phone sa SAME WiFi
2. Buksan ang browser sa phone (Chrome, Safari, etc.)
3. I-type ang URL: http://192.168.1.27:8000
   (Replace ang 192.168.1.27 ng YOUR IP address)

---

## 🔥 KUNG MAY ERROR:

### Error: "Connection refused" o "Can't reach this page"

**SOLUTION 1: I-allow sa Windows Firewall**
1. Open Windows Defender Firewall
2. Click "Allow an app through firewall"
3. Click "Change settings"
4. Click "Allow another app"
5. Browse at piliin: C:\xampp\php\php.exe
6. I-check ang "Private" at "Public"
7. Click OK

**SOLUTION 2: Manually add firewall rule**
Run this command as Administrator sa Command Prompt:
```
netsh advfirewall firewall add rule name="Laravel Server" dir=in action=allow protocol=TCP localport=8000
```

**SOLUTION 3: I-check kung same WiFi**
- PC at Phone ay dapat naka-connect sa SAME WiFi network
- Hindi pwede kung naka-mobile data ang phone

---

## 📝 IMPORTANT NOTES:

1. **Hindi ito permanent hosting** - Kapag nag-close ka ng `start_for_phone.bat`, hindi na ma-access sa phone

2. **Local network lang** - Pwede lang ito sa devices na naka-connect sa same WiFi. Hindi ma-access sa ibang lugar.

3. **IP Address ay pwedeng magbago** - Kapag nag-restart ang router o PC, pwedeng magbago ang IP address. I-check ulit gamit ang `get_ip.bat`

4. **Para sa permanent hosting**, kailangan mo ng:
   - Web hosting service (like AWS, DigitalOcean, Heroku)
   - Domain name
   - SSL certificate

---

## 🎯 QUICK COMMANDS:

### Para makita ang IP address:
```
get_ip.bat
```

### Para i-start ang server for phone:
```
start_for_phone.bat
```

### Para i-stop ang server:
Press `Ctrl + C` sa command prompt window

---

## 🔧 TROUBLESHOOTING:

### Phone: "This site can't be reached"
- Check kung same WiFi ang PC at Phone
- Check kung running ang `start_for_phone.bat`
- Check kung tama ang IP address

### Phone: "Connection timeout"
- I-disable muna ang Windows Firewall (temporarily)
- Kung gumana, i-add ang firewall rule (see Solution 1 above)

### Camera hindi gumagana sa phone
- Kailangan ng HTTPS para sa camera access
- Local network (HTTP) ay hindi pwede ang camera sa most browsers
- Alternative: Mag-upload na lang ng image instead of using camera

---

## 💡 TIPS:

1. **Para mas mabilis**, i-bookmark ang URL sa phone browser
2. **Para sa camera feature**, mas maganda gumamit ng PC/laptop
3. **Para sa testing**, pwede mong i-try muna sa PC browser: http://localhost:8000

---

## 📞 NEED HELP?

Kung may problema pa rin:
1. Check kung running ang MySQL sa XAMPP
2. Check kung naka-same WiFi ang PC at Phone
3. Try i-restart ang router
4. Try i-restart ang PC
5. Try different browser sa phone

---

ENJOY! 🎉
