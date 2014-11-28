<?php

use Heinzawhtet\Myanpay\Myanpay;

class MyanpayTest extends PHPUnit_Framework_TestCase {
	public function testAuthorize()
	{
		$items = array(
			array (
				'number' 	=> 'P001',
				'name' 		=> 'A pecial Birthday', 
				'ammount'	=> '1000',
				'quantity'	=> '1',
				'desc'		=> 'Hi Hi'
			),
			array (
				'number' 	=> 'P002',
				'name' 		=> 'Spray oses', 
				'ammount'	=> '1000',
				'quantity'	=> '1',
				'desc'		=> 'Hi Hi'
			),
		);

		$data = array( 'amount' => '2000' , 'items' => $items);

		$request = new \Heinzawhtet\Myanpay\Requests\SetExpressCheckoutRequest;
		$request->initialize($this, $data);
		$this->assertInstanceOf('\Heinzawhtet\Myanpay\Requests\SetExpressCheckoutRequest', $request);
	}

	public function testGetApiUsername()
	{
        return $this->apiUsername;
	}
}