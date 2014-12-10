<?php

use Heinzawhtet\Myanpay\Myanpay;
use \Heinzawhtet\Myanpay\Requests\SetExpressCheckoutRequest;
use Symfony\Component\HttpFoundation\Session\Session;

class MyanpayTest extends PHPUnit_Framework_TestCase {
	public function testAuthorize()
	{
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

		$data = array(
			'amount' => '10.00',
			'currency' => 'MMK',
		);

		$mock = $this->getMock('Myanpay', array(
				'getApiUsername',
				'getApiPassword',
				'getApiSignature',
				'getDev',
				'getHeaderImg',
				'getCustomerServiceNumber',
				'getBrandName',
				'getReturnUrl',
				'getCancelUrl',
				'getNoShipping'
			));

		$mock->expects($this->any())
			->method('getApiUsername')
			->will($this->returnValue('_junio7482443442_myanpayAPI' ));

		$mock->expects($this->any())
			->method('getApiPassword')
			->will($this->returnValue('Z687FL3W036I06D0' ));

		$mock->expects($this->any())
			->method('getApiSignature')
			->will($this->returnValue('b98qL7734zo0Fw50mFP4u55p583bkLz44ZY524EMe5rO7hfdzlWq5AN2rx8d' ));

		$mock->expects($this->any())
			->method('getDev')
			->will($this->returnValue('true'));

		$mock->expects($this->any())
			->method('getHeaderImg')
			->will($this->returnValue('http://placehold.it/150x50'));

		$mock->expects($this->any())
			->method('getCustomerServiceNumber')
			->will($this->returnValue('123456789'));

		$mock->expects($this->any())
			->method('getBrandName')
			->will($this->returnValue('Test Store'));

		$mock->expects($this->any())
			->method('getReturnUrl')
			->will($this->returnValue('http://localhost:1200/return'));

		$mock->expects($this->any())
			->method('getCancelUrl')
			->will($this->returnValue('http://localhost:1200/cancel'));

		$mock->expects($this->any())
			->method('getNoShipping')
			->will($this->returnValue(true));

		$obj = new SetExpressCheckoutRequest;
		return $obj->initialize($mock, $data);

	}

	public function createRequest($class, $data = null)
    {
        $obj = new $class;
        if (isset($data)) {
            return $obj->initialize($this, $data);
        }
        return $obj->initialize($this);
    }
}