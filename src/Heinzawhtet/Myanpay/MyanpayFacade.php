<?php 
namespace Heinzawhtet\Myanpay;

use Illuminate\Support\Facades\Facade;

class MyanpayFacade extends Facade {

    protected static function getFacadeAccessor() { 
    	return 'myanpay'; 
    }

}