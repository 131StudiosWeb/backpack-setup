# onethirtyone/backpacksetup
A simple setup process for Laravel Backpack

The setup script will add an **Access Admin Panel** permission 
 and an **Administrator** role and will then prompt for an administrator
 user account.
 
 ## Installation
 
 Install using composer
 
 ```$xslt
composer require onethirtyone/backpacksetup
```

Laravel >5.5 there is nothing more you need to do

Laravel 5.4  The the following to your `config/app.php`

```$xslt
...
onethirtyone\backpacksetup\BackpackSetupServiceProvider::class
...
```

Run `php artisan backpack:setup` to setup Laravel Backpack



