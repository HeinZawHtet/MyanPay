<?php namespace Heinzawhtet\Myanpay;

class Session {

	public function __construct()
	{
		session_start();
	}

	public function set($key, $value)
	{
        return $_SESSION[$key] = $value;
	}

	public function get($key)
	{
		return $_SESSION[$key];
	}

	public function has($key)
	{
		return ! is_null($this->get($key));
	}
}