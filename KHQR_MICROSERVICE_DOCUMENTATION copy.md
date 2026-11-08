# ការពន្យល់អំពី KHQR Microservice និង Railway Deployment

**KHQR Microservice គឺជា Service មួយដែលត្រូវបានបង្កើតដោយប្រើ Python Flask ដើម្បី Generate KHQR (Khmer QR) សម្រាប់ប្រព័ន្ធទូទាត់ប្រាក់ Bakong របស់ National Bank of Cambodia (NBC)។ Microservice នេះដំណើរការដាច់ដោយឡែកពី Laravel Application ហើយទំនាក់ទំនងគ្នាតាមរយៈ RESTful API។ នៅក្នុង Laravel មានតួនាទីគ្រប់គ្រង Website ដូចជា Product, Cart, Checkout និង Order ខណៈដែល Python Microservice មានតួនាទី Generate KHQR ប៉ុណ្ណោះ។ មូលហេតុដែលប្រើ Microservice គឺដោយសារ Bakong SDK ត្រូវបានអភិវឌ្ឍសម្រាប់ Python ដូច្នេះការប្រើ Python ជាមួយ SDK ផ្ទាល់មានភាពងាយស្រួល ស្ថេរភាព និងងាយស្រួលក្នុងការថែទាំ។ ប្រសិនបើមានការកែប្រែ KHQR Standard ឬ Bakong SDK នៅពេលអនាគត គ្រាន់តែកែប្រែ Microservice ប៉ុណ្ណោះ ដោយមិនប៉ះពាល់ដល់ Laravel Website។**

**ដើម្បីឱ្យ Microservice អាចដំណើរការបានជាបន្តបន្ទាប់ និងអាចប្រើបានពីគ្រប់ទីកន្លែង វាត្រូវបាន Deploy ទៅលើ Railway Cloud Platform។ Railway មានតួនាទីជា Cloud Hosting សម្រាប់ Python Flask Application ដោយផ្តល់ Public URL, HTTPS (SSL Certificate) និង Environment Variables ដោយស្វ័យប្រវត្តិ។ បន្ទាប់ពី Deploy រួច Laravel នឹងរក្សាទុក URL របស់ Microservice ក្នុង `.env` ហើយរាល់ពេលមានការទូទាត់ Laravel នឹងផ្ញើ HTTP Request ទៅ Railway ដើម្បីឱ្យ Microservice Generate KHQR ហើយផ្ញើ Response ត្រឡប់មកវិញ។ ការប្រើ Railway ធ្វើឱ្យមិនចាំបាច់បើកកុំព្យូទ័រផ្ទាល់ ឬដំណើរការ Python Server ដោយខ្លួនឯង ព្រោះ Microservice ដំណើរការ 24/7 នៅលើ Cloud។**

---

# ដំណើរការ (Process)


*Customer*
    │
    ▼
*Laravel Website*
*(Product → Cart → Checkout)*
    │
*Create Order*
    │
    ▼
*BakongKHQRService*
    │
*HTTP POST Request*
    │
    ▼
*Railway Cloud*
*Python Flask Microservice*
    │
    ▼
*Bakong SDK*
    │
*Generate KHQR*
    │
    ▼
*Response*
(QR String + MD5 + Deep Link)
    │
    ▼
*Laravel Website*
    │
*Generate QR Image*
    │
    ▼
*Payment Page*
    │
    ▼
*Customer Scan QR*
    │
    ▼
*Bakong Mobile Banking*

---

# របៀបដែល System ដំណើរការ

1. Customer ជ្រើសរើសផលិតផល និងចុច Checkout។
2. Laravel បង្កើត Order និងរក្សាទុកក្នុង Database។
3. Laravel ប្រើ BakongKHQRService ផ្ញើ HTTP POST Request ទៅ Python Microservice ដែល Deploy នៅ Railway។
4. Request មានព័ត៌មានដូចជា Amount, Currency, Bill Number, Merchant ID, Merchant Name និង Account ID។
5. Python Flask ទទួល Request ហើយប្រើ Bakong SDK ដើម្បី Generate KHQR String។
6. Microservice បង្កើត MD5 Hash និង Deep Link រួចផ្ញើ Response ត្រឡប់ទៅ Laravel។
7. Laravel បង្កើត QR Code Image ពី QR String ហើយបង្ហាញនៅលើ Payment Page។
8. Customer អាចស្កេន QR Code ឬចុច Deep Link ដើម្បីបើក Banking App និងបញ្ចប់ការទូទាត់។

---

# File សំខាន់ៗ

## Laravel Project

| File                                          | តួនាទី                                         |
| --------------------------------------------- | ---------------------------------------------- |
| `.env`                                        | រក្សាទុក `KHQR_MICROSERVICE_URL` និង `API_KEY` |
| `app/Services/BakongKHQRService.php`          | ផ្ញើ HTTP Request ទៅ Railway Microservice      |
| `app/Http/Controllers/CheckoutController.php` | ហៅ Service និងទទួល Response                    |
| `resources/views/web/payment/index.blade.php` | បង្ហាញ QR Code និង Deep Link                   |
| `routes/web.php`                              | Route សម្រាប់ Payment                          |

---

## Python Microservice

| File                     | តួនាទី                                  |
| ------------------------ | --------------------------------------- |
| `app.py`                 | Main Flask Application និង API Endpoint |
| `requirements.txt`       | Package ដែលត្រូវ Install                |
| `.env`                   | Merchant Information និង API Key        |
| `Procfile`               | កំណត់ Command សម្រាប់ Railway Deploy    |
| `railway.json` *(បើមាន)* | Railway Configuration                   |

---

# Railway Deployment

Railway គឺជា Cloud Platform ដែលប្រើសម្រាប់ Deploy Python Microservice។

### Deployment Process

1. បង្កើត Project នៅ Railway។
2. Connect GitHub Repository (`khqr-microservice`)។
3. Railway អាន `requirements.txt` និង Install Packages ដោយស្វ័យប្រវត្តិ។
4. Railway ប្រើ `Procfile` ឬ Start Command ដើម្បី Run Flask Application។
5. កំណត់ Environment Variables ដូចជា:

   * `BAKONG_MERCHANT_ID`
   * `BAKONG_ACCOUNT_ID`
   * `BAKONG_API_KEY`
6. Railway បង្កើត Public URL ឧទាហរណ៍៖

   ```
   https://khqr-microservice-production.up.railway.app
   ```
7. Laravel ប្រើ URL នេះនៅក្នុង `.env` ដើម្បីផ្ញើ API Request។

---

# ហេតុអ្វីត្រូវប្រើ Railway?

* Deploy Python Application បានយ៉ាងងាយស្រួល។
* ផ្តល់ Public API URL សម្រាប់ Laravel ហៅបានគ្រប់ទីកន្លែង។
* មាន HTTPS (SSL Certificate) ដោយស្វ័យប្រវត្តិ។
* គ្រប់គ្រង Environment Variables ដោយសុវត្ថិភាព។
* Auto Deploy នៅពេល Push Code ថ្មីទៅ GitHub។
* អាច Monitor Logs និង Deployment Status បាន។
* Microservice ដំណើរការ 24/7 ដោយមិនចាំបាច់បើកកុំព្យូទ័រផ្ទាល់។

---

# ចំណុចសំខាន់ៗ

* Laravel គ្រប់គ្រង Website និង Payment Flow។
* Python Flask គ្រប់គ្រងការបង្កើត KHQR ដោយប្រើ Bakong SDK។
* Railway ជា Cloud Hosting សម្រាប់ Microservice និងផ្តល់ Public API។
* RESTful API ជាមធ្យោបាយទំនាក់ទំនងរវាង Laravel និង Python។
* Deep Link អនុញ្ញាតឱ្យអ្នកប្រើទូរស័ព្ទបើក Banking App ដោយផ្ទាល់។
* API Key ត្រូវបានប្រើដើម្បីការពារ Microservice ពីការចូលប្រើដោយគ្មានសិទ្ធិ។
* ការបំបែកជា Microservice ធ្វើឱ្យប្រព័ន្ធមានភាពងាយស្រួលក្នុងការអភិវឌ្ឍ ថែទាំ ពង្រីក និងអាចប្រើឡើងវិញជាមួយ Website ឬ Mobile Application ផ្សេងៗបាន។
