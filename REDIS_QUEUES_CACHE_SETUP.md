# Redis, Queues និង Caching Setup

## ស្ថានភាព Configuration បច្ចុប្បន្ន (Current Configuration Status)

### ✅ Caching - **ENABLED (Database)**

**Default Driver:** Database

**Configuration File:** `config/cache.php`

**Current Store:** Database Cache Table

**Status:** កំពុងដំណើរការធម្មតា (Active & Working)

**ការពន្យល់**

បច្ចុប្បន្ន Application របស់អ្នកកំពុងប្រើ **Database Cache** ដើម្បីរក្សាទុកទិន្នន័យដែលត្រូវបានប្រើញឹកញាប់។ វាជួយកាត់បន្ថយការស្នើសុំ (Query) ទៅកាន់ Database រាល់ពេលដែល User ចូលមើលទំព័រ។

---

### ✅ Queues - **ENABLED (Database)**

**Default Driver:** Database

**Configuration File:** `config/queue.php`

**Current Connection:** Database Queue Table

**Status:** កំពុងដំណើរការធម្មតា

**ការពន្យល់**

Queue ត្រូវបានកំណត់ឱ្យប្រើ Database ដើម្បីរក្សាទុក Job ដែលត្រូវដំណើរការនៅពេលក្រោយ។ ទោះជាយ៉ាងណា បច្ចុប្បន្ន Application របស់អ្នកមិនទាន់មាន Job ដែលត្រូវបាន Dispatch នៅឡើយទេ។

---

### ⚠️ Redis - **Configured but Not Active**

**Status**

Redis ត្រូវបាន Configure រួចហើយក្នុង `cache.php` និង `queue.php` ប៉ុន្តែមិនទាន់កំណត់ឱ្យប្រើជា Default Driver នៅឡើយ។

**Ready to Use:** បាទ/ចាស។ គ្រាន់តែផ្លាស់ប្តូរ Environment Variables ក្នុង `.env` ប៉ុណ្ណោះ។

---

# Current Implementation

## Caching Implementation

បច្ចុប្បន្ន Application ប្រើ **Database Cache** ដើម្បី Cache ទិន្នន័យ Navbar។

ឧទាហរណ៍

```php
Cache::remember('nav.categories.v2', 3600, function () {
    ...
});
```

### តើ `Cache::remember()` ធ្វើអ្វី?

វាដំណើរការដូចខាងក្រោម៖

1. ពិនិត្យមើលថា Key `nav.categories.v2` មានក្នុង Cache ឬអត់។
2. បើមាន → Return ទិន្នន័យពី Cache ភ្លាមៗ។
3. បើមិនមាន → Query Database។
4. Save លទ្ធផលទៅ Cache រយៈពេល **3600 Seconds (1 Hour)**។
5. User បន្ទាប់នឹងទទួលទិន្នន័យពី Cache ជំនួស Database។

---

### Flow

```
Browser
     │
     ▼
Laravel
     │
Check Cache
     │
 ┌───────────────┐
 │ Cache Exists? │
 └──────┬────────┘
        │
   Yes  │  No
        │
        ▼
 Return Cache
        │
        ▼
     Finish

(No)

Database
     │
     ▼
Save to Cache
     │
     ▼
Return Data
```

---

## អត្ថប្រយោជន៍ (Benefits)

* កាត់បន្ថយ Database Query
* Navbar Load លឿនជាងមុន
* Cache ជា Plain Array ដើម្បីជៀសវាង Serialization Issues
* Cache មានសុពលភាព 1 ម៉ោង ដែលជាតុល្យភាពល្អរវាង Performance និង Fresh Data

---

# Queue Implementation

Application ត្រូវបាន Configure ឱ្យប្រើ Database Queue។

បច្ចុប្បន្ន មិនទាន់មាន Job ត្រូវបាន Dispatch នៅឡើយ។

Laravel គាំទ្រ Features ដូចជា៖

* Job Batching
* Failed Jobs
* Retry Mechanism
* Delayed Jobs
* Queue Priorities

---

# របៀប Enable Redis

## Step 1 - Install Redis

### macOS

```bash
brew install redis
brew services start redis
```

### Ubuntu / Debian

```bash
sudo apt-get install redis-server
sudo systemctl start redis-server
```

### Production Server

* ពិនិត្យថា Hosting គាំទ្រ Redis ឬអត់
* Shared Hosting ភាគច្រើនមិនគាំទ្រ Redis
* VPS ឬ Cloud Server ភាគច្រើនគាំទ្រ Redis

---

## Step 2 - Install Laravel Redis Package

```bash
composer require predis/predis
```

---

## Step 3 - Configure Environment Variables

បន្ថែមក្នុង `.env`

```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
REDIS_CACHE_CONNECTION=cache

QUEUE_CONNECTION=redis
REDIS_QUEUE_CONNECTION=default
```

---

## Step 4 - Configure Redis Connection

ពិនិត្យក្នុង

```
config/database.php
```

ថាមាន Redis Connection ត្រឹមត្រូវ។

---

## Step 5 - Test Redis Connection

```bash
php artisan tinker
```

```php
Redis::set('test','Hello Redis');

Redis::get('test');
```

បើ Return

```
Hello Redis
```

មានន័យថា Redis ដំណើរការត្រឹមត្រូវ។

---

## Step 6 - Clear Cache

```bash
php artisan cache:clear

php artisan config:clear

php artisan queue:restart
```

---

# Performance Comparison

## Database Cache

### Advantages

* Setup ងាយស្រួល
* មិនចាំបាច់ Install Software បន្ថែម
* ល្អសម្រាប់ Shared Hosting
* ល្អសម្រាប់ Free Hosting

### Disadvantages

* ល្បឿនអាស្រ័យលើ Database
* Performance ទាបជាង Redis

---

## Redis Cache

### Advantages

* ល្បឿនលឿនខ្លាំង (In-Memory)
* Scalability ខ្ពស់
* សាកសមសម្រាប់ Website ដែលមាន Traffic ច្រើន
* ល្អសម្រាប់ Queue និង Session

### Disadvantages

* ត្រូវការ Redis Server
* Shared Hosting ភាគច្រើនមិនគាំទ្រ

---

# Queue Worker

បើប្រើ Redis Queue

Development

```bash
php artisan queue:work redis
```

Production

ប្រើ Supervisor

```ini
command=php artisan queue:work redis
```

Queue Worker មានតួនាទីយក Job ពី Queue មកដំណើរការដោយស្វ័យប្រវត្តិ។

---

# តើ Queue ប្រើសម្រាប់អ្វី?

ឧទាហរណ៍ User Order Product

បើគ្មាន Queue

```
Save Order
↓

Send Email
↓

Generate Invoice
↓

Notify Admin
↓

Return Response
```

User ត្រូវរង់ចាំរហូតដល់ការងារទាំងអស់ចប់។

---

បើមាន Queue

```
Save Order
↓

Return Response Immediately

↓

Queue

↓

Send Email

↓

Generate Invoice

↓

Notify Admin
```

User ទទួល Response ភ្លាមៗ ខណៈ Queue Worker បន្តការងារនៅ Background។

---

# Monitoring Cache

## Clear Cache

```bash
php artisan cache:clear
```

---

## Clear Specific Cache

```bash
php artisan cache:forget nav.categories.v2
```

---

## Clear Config

```bash
php artisan config:clear
```

---

## Clear View

```bash
php artisan view:clear
```

---

# Troubleshooting

## Redis Connection

```bash
redis-cli ping
```

បើឃើញ

```
PONG
```

មានន័យថា Redis Server ដំណើរការល្អ។

---

## Test Cache

```php
Cache::put('test','value',60);

Cache::get('test');
```

បើ Return

```
value
```

មានន័យថា Cache ដំណើរការត្រឹមត្រូវ។

---

## Queue

មើល Failed Jobs

```bash
php artisan queue:failed
```

Retry

```bash
php artisan queue:retry all
```

Clear Failed Jobs

```bash
php artisan queue:flush
```

---

# អនុសាសន៍ (Recommendations)

## សម្រាប់ Hosting បច្ចុប្បន្ន (Free / Shared Hosting)

សូមបន្តប្រើ **Database Cache** ព្រោះ៖

* មិនត្រូវការ Redis Server
* ដំណើរការល្អជាមួយ Hosting បច្ចុប្បន្ន
* គ្រប់គ្រាន់សម្រាប់ Traffic បច្ចុប្បន្ន
* បាន Configure និងដំណើរការរួចរាល់

---

## សម្រាប់អនាគត (VPS / Cloud / Dedicated Server)

ពេល Website មាន User និង Traffic កើនឡើង គួរផ្លាស់ប្តូរទៅ Redis ព្រោះ៖

* Performance ល្អជាង
* Queue ដំណើរការលឿនជាង
* Scalability ខ្ពស់
* គាំទ្រ Real-time Features
* សមស្របសម្រាប់ Website ធំ

---

# សេចក្តីសន្និដ្ឋាន

## Current Status

| Feature | Status        | Driver     |
| ------- | ------------- | ---------- |
| Caching | ✅ Active      | Database   |
| Queues  | ✅ Active      | Database   |
| Redis   | ⚠️ Configured | Not Active |

---

## Recommendation

សម្រាប់ Project បច្ចុប្បន្ន និង Free Hosting របស់អ្នក ការប្រើ **Database Cache** និង **Database Queue** គឺជាជម្រើសល្អបំផុត។

នៅពេលអ្នកផ្លាស់ទៅ VPS ឬ Cloud Server សូមប្តូរទៅ **Redis** ដើម្បីទទួលបាន៖

* ល្បឿនខ្ពស់ជាង
* Scalability ល្អជាង
* Queue Processing មានប្រសិទ្ធភាពជាង
* អាចគាំទ្រ User និង Traffic ច្រើនបានល្អ។
