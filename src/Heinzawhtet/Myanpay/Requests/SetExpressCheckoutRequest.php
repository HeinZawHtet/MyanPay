<?php namespace Heinzawhtet\Myanpay\Requests;

class SetExpressCheckoutRequest {
	public function initialize($data)
	{
		$param = [
            "Method" => $this->method,
            "version" => $this->version,
            "apiusername" => $this->apiUsername,
            "apipassword" => $this->apiPassword,
            "apisignature" => $this->apiSignature,
            "paymentaction" => $this->paymentAction,
            "returnUrl" => $this->returnUrl,
            "cancelUrl" => $this->cancelUrl,
            "PaymentRequest_ItemTotalAmt" => $data['amount'],
            "paymentRequest_Amt" => $data['amount'],
            "paymentaction" => 'Sale'
        ];

        $param = array_merge($param, $this->setItems($data['items']));

        $curl = new Curl($this->checkoutUrl);

        $curl->options = array(
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $param,
        );

        if($errno = curl_errno($curl)) {
            $message = curl_strerror($errno);
            throw new CurlErrorException("CURL error : $message");
        }
        
        $getResult = $curl->make();

        parse_str($getResult, $result); // Convert query string to array

        if (empty($result)) {
            throw new EmptyResponseException('No Response returned'); // need to fix
        }

        if ($result['Ack'] == 'fail') {
            throw new AckFailException($result['LONGMESSAGE0']); // need to fix
        }

        $this->session->set('token', $result['Token']);
        $this->session->set('paymentAmmount', $param['paymentRequest_Amt']);

        return Redirect::create($this->loginUrl . urldecode($result['Token']));
	}
}