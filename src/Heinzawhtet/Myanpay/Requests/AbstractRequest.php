<?php namespace Heinzawhtet\Myanpay\Requests;

use Heinzawhtet\Myanpay\Exceptions\CurlErrorException;

class AbstractRequest {

	const API_VERSION = '1.1';

	/**
     * Curl request to giving url
     *
     * @param string $url
     * @param array $param
     * 
     * @return array
     */
	public function sendRequest($url, $param)
	{
		$curl = curl_init($url);

		curl_setopt_array($curl, array(
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $param,
        ));

		$getResult = curl_exec($curl);

		if($errno = curl_errno($curl)) {
            $message = curl_strerror($errno);
            throw new CurlErrorException("CURL error : $message");
        }

		curl_close($curl);

		parse_str($getResult, $result);

		return $result;
	}

	/**
     * Set endpoint url depending upon env set by developer
     *
     * @param string $env
     * 
     * @return boolean
     */
	public function getEndpoint($env)
    {
        return $env ? $this->developmentEndpoint : $this->endpoint;
    }
}