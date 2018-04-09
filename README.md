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

Publish the config file

 ```
 php artisan vendor:publish
 ```

Edit the `config\onethirtyone\backpacksetup.php` to add default roles and permissions for setup.

Run the setup command

 ```
 php artisan onethirtyone:backpack-setup
 ```



