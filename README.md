shortuuid-php
=============

[![Build Status](https://travis-ci.org/PyYoshi/shortuuid-php.svg)](https://travis-ci.org/PyYoshi/shortuuid-php)
[![Build Status](https://img.shields.io/packagist/v/PyYoshi/shortuuid-php.svg)](https://packagist.org/packages/pyyoshi/shortuuid-php)

The PHP implementation of [shortuuid](https://github.com/stochastic-technologies/shortuuid).

# Installation

```bash
composer require pyyoshi/shortuuid-php
```

or add this library in your ``composer.json``

```json
{
    "require": {
        "pyyoshi/shortuuid-php": "*"
    }
}
```

# Usage

import

```php
require dirname(__DIR__) . "/vendor/autoload.php";
require_once 'ShortUUID/Autoloader.php';
\ShortUUID\Autoloader::register();
```

You can then generate a short UUID:

```php
use ShortUUID\ShortUUID;

$su = new ShortUUID();
$su->uuid();
=> "rkQdp5ikpXjraCsrSaysaT"
```

If you prefer a version 5 UUID, you can pass a name (DNS or URL) to the call and it will be used as a namespace (uuid.NAMESPACE_DNS or uuid.NAMESPACE_URL) for the resulting UUID:

```php
use ShortUUID\ShortUUID;

$su = new ShortUUID();
$su->uuid('example.com');
=> "wpsWLdLt9nscn2jbTD3uxe"
$su->uuid('http://www.example.com/');
=> "VSPugLzk4dD4WC7yfAQUzn"
```

To see the alphabet that is being used to generate new UUIDs:

```php
use ShortUUID\ShortUUID;

$su = new ShortUUID();
$su->getAlphabet();
=> "23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz"
```

If you want to use your own alphabet to generate UUIDs, use setAlphabet():

```php
use ShortUUID\ShortUUID;

$su = new ShortUUID();
$su->setAlphabet('aaaaabcdefgh1230123');
$su->uuid();
=> "ee120aeh2h3bb010fdfedef2c03efcf3h1ca"
```

``shortuuid-php`` will automatically sort and remove duplicates from your alphabet to ensure consistency:

```php
use ShortUUID\ShortUUID;

$su = new ShortUUID();
$su->setAlphabet('aaaaabcdefgh1230123');
$su->getAlphabet();
=> "0123abcdefgh"
```

If the default 22 digits are too long for you, you can get shorter IDs by just truncating the string to the desired length. The IDs won't be universally unique any longer, but the probability of a collision will still be very low.

To serialize existing UUIDs, use encode() and decode():

```php
use Ramsey\Uuid\Uuid;
use ShortUUID\ShortUUID;

$u = Uuid::uuid4();
$su = new ShortUUID();
$s = $su->encode($u);

$su->decode($s) == $u;
=> true
```

# License

``shortuuid-php`` is distributed under the BSD license.
