Apolearn-API-PHP-CLIENT
=======================

Simple PHP client for Apolearn API 1.0

[![READ THE DOCUMENTATION HERE](https://app.swaggerhub.com/apis/apolearn/Apolearn/1.0.0)](https://app.swaggerhub.com/apis/apolearn/Apolearn/1.0.0)

Installation
------------

**Normally:** If you *don't* use composer, don't worry - just include TwitterAPIExchange.php in your application.

```php
require_once('lib/ApolearnAPIClient.php');
```

How To Use
----------

#### Call the class with your API credential ####

```php

$instance_url = 'https://yourcompany.apolearn.com';
$public_key = 'YOUR_PUBLIC_KEY';
$private_key = 'YOUR_PRIVATE_KEY';

$apolearn = new ApolearnAPIClient($instance_url, $public_key, $private_key);
```

#### Login with your user admin credential ####
```php

$token = $apolearn->login($username, $password);

```

#### perform an API Call ####
```php

$users = $apolearn->getUsers();

```