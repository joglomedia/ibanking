IBanking - Internet Banking
=====

Internet Banking client wrapper, useful to check internet banking statements (cek mutasi online) using PHP script.

## Documentation

The documentation is currently under construction.

You can read here:

## Installation

### Composer

Add _ibank_ library in to your composer.json or create a new composer.json file:

```js
{
    "require": {
        "joglomedia/ibanking": "dev-master"
    }
}
```

Then, tell composer to download the library by running the command:

``` bash
$ php composer.phar install
```

Composer will generate the autoloader file automatically. So you only have to include this.
Typically its located in the _vendor_ directory and its called _autoload.php_

```php
<?php
include('vendor/autoload.php');
```

## Basic Usage

This library is using the PSR-4 standard: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4.md.
So you can use any autoloader which fits into this standard.
The tests directory contains an example bootstrap file.

```php
<?php
namespace MasEDI\CekMutasiDemo

use IBanking\IBanking as IBanking;
use IBanking\IBParser\SampleBankParser as SBParser;

$credentials = [
	'corpid'	=> '',
	'username'	=> 'namauser',
	'password'	=> 'katasandi',
	'account'	=> 'nomor_rekening',
];

$ibank = new IBanking(new SBParser, $credentials);

$loggedin = $ibank->login();

var_dump($loggedin);
echo("\r\n");

$balance = $ibank->getBalance();
var_dump($balance);
echo("\r\n");

$mutasi = $ibank->getStatements('24/7/2017', '29/7/2017', 'credit');
var_dump($mutasi);
echo("\r\n");

var_dump($ibank->isLoggedin($session=true));

$ibank->logout();
```

For some very simple examples go to the _samples_ directory and have a look at the sample files.

## Contribution
Please send your PR on the Github repository to help improve this script.

## DONATION
**[Buy Me a Bottle of Milk](https://paypal.me/masedi)**

(c) 2017
