# MyanPay PHP Package
MyanPay integration for PHP with easy.


## Install
You can install this package in two ways.

## via Composer PHP (Recommended)

Require this package via terminal

```bash
composer require heinzawhtet/myanpay
```

Or just require this package in composer.json and run `composer update`

```json
{
    "require": {
        "heinzawhtet/myanpay": "dev-master"
    }
}
```

## Quick Start
First set your MyanPay API credentials and request to server.
```php

	$items = [
		[
			'number' 	=> 'P001',
			'name' 		=> 'A pecial Birthday', 
			'ammount'	=> '1000',
			'quantity'	=> '1',
			'desc'		=> 'Hi Hi'
		],
		[
			'number' 	=> 'P002',
			'name' 		=> 'Spray oses', 
			'ammount'	=> '1000',
			'quantity'	=> '1',
			'desc'		=> 'Hi Hi'
		],
	];

	$pay = new Heinzawhtet\Myanpay\Myanpay;

	$pay->setApiUsername('_junio7482443442_myanpayAPI');
	$pay->setApiPassword('Z687FL3W036I06D0');
	$pay->setApiSignature('b98qL7734zo0Fw50mFP4u55p583bkLz44ZY524EMe5rO7hfdzlWq5AN2rx8d');
	$pay->setDev(true); // if you are on development

	$pay->setHeaderImg('YOUR_BRAND_IMG_PATH');
	$pay->setCustomerServiceNumber('09 12345678');
	$pay->setBrandName('YOUR_STORE_NAME');

	$pay->setReturnUrl('http://localhost:1200/return'); // Return Url when payment is ready to complete
	$pay->setCancelUrl('http://localhost:1200/cancel'); // Cancel Url when payment is canceled

	return $pay->purchase(array( 'amount' => '2000' , 'items' => $items));

```

And then complete the purchase

```php
	$pay = new Heinzawhtet\Myanpay\Myanpay;

	$pay->setApiUsername('_junio7482443442_myanpayAPI');
	$pay->setApiPassword('Z687FL3W036I06D0');
	$pay->setApiSignature('b98qL7734zo0Fw50mFP4u55p583bkLz44ZY524EMe5rO7hfdzlWq5AN2rx8d');
	
	$pay->setDev(true);

	$com = $pay->completePurchase(); // complete the purchase
	$detail = $pay->fetchDetail(); // fetch payment details such as buyer's email or shipping address
```

More detail documentation, comming soon.