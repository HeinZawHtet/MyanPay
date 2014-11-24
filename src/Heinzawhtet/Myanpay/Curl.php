<?php namespace Heinzawhtet\Myanpay;

class Curl {

	public $request;

	public function __construct($url)
	{
		$this->request = curl_init($url);
	}

	public function make()
	{
        curl_setopt_array($this->request, $this->options);
		return curl_exec($this->request);
	}

	public function __destruct()
    {
        curl_close($this->request);
    }
}