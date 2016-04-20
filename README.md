[![StyleCI](https://styleci.io/repos/56254078/shield)](https://styleci.io/repos/56254078)
[![Build Status](https://travis-ci.org/ehtasham89/laravel-apiauth-driver.svg?branch=master)](https://travis-ci.org/ehtasham89/laravel-apiauth-driver)
# laravel-apiauth-driver
A RESTful APi authenication driver for Laravel 4.2

## Installation Guide:
**Following lines in your laravel composer.json file require array:**
`"ehtasham89/laravel-apiauth-driver": "dev-master"`
**Palce following service provider links in app/config/app.php providers array:**
```
'Ehtasham89\LvApiAuth\LvApiAuthServiceProvider',
'Ehtasham89\LvApiAuth\Reminders\ReminderServiceProvider',
```
**Comment the following Service provider in app/config/app.php providers array:**
`//'Illuminate\Auth\Reminders\ReminderServiceProvider',`
Change the driver name in app/config/auth.php with **'driver' => 'lvapiauth',**
## Api Configuration
**Run following command via composer:**
`php artisan config:publish ehtasham89/laravel-apiauth-driver`
It will create copy of config.php file in `app/config/packages/ehtasham89/laravel-apiauth-driver/`
Add your api's endpoints in `app/config/packages/ehtasham89/laravel-apiauth-driver/config.php`

