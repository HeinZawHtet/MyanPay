<?php namespace Heinzawhtet\Myanpay;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;
use Symfony\Component\HttpFoundation\Session\Session;
use Heinzawhtet\Myanpay\Exceptions\AckFailException;
use Heinzawhtet\Myanpay\Exceptions\EmptyResponseException;

class Myanpay extends Gateway {

    public $method = 'SetExpressCheckout';
    public $version = '1.1';
    public $paymentAction = 'Sale';

    public $apiUsername;
    public $apiPassword;
    public $apiSignature;

    public $headerImg;
    public $brandName;
    public $customerServiceNumber;

    public $returnUrl;
    public $cancelUrl;

    protected $env;

    protected $checkoutUrl = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/ExpressCheckoutRequestHandler.aspx";
    protected $getCheckoutUrl = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/GetExpressCheckoutRequestHandler.aspx";
    protected $doCheckoutUrl = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/DoExpressCheckOutRequestHandler.aspx";
    protected $loginUrl = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/ExpressCheckoutLogin.aspx?cmd=express-checkout&token=";

    protected $session;
    protected $redirect;


    public function __construct(Request $request = null, Session $session = null)
    {
        $this->request = $request ?: Request::createFromGlobals();
        $this->session = $session ?: new Session;
    }

    public function purchase($data)
    {
        return $this->authorize($data);
    }

    public function authorize($data)
    {
        $request = Heinzawhtet\Request\SetExpressCheckoutRequest;
        return $request->initialize($data);
    }


    /**
     * Set items into MyanPay format
     *
     * @return void
     * @author 
     **/
    public function setItems($items)
    {
        foreach ($items as $key => $item) {
            $param['paymentRequest_ItemNumber'.$key] = $item['number'];
            $param['paymentRequest_ItemName'.$key] = $item['name'];
            $param['paymentRequest_ItemAmt'.$key] = $item['ammount'];
            $param['paymentRequest_ItemQty'.$key] = $item['quantity'];
            $param['paymentRequest_ItemDesc'.$key] = $item['desc'];
        }

        return $param;
    }

    public function setParams($data)
    {

    }

    public function fetchDetail()
    {
        $param = [
            "Method" => 'GetExpressCheckout',
            "version" => $this->version,
            "apiusername" => $this->apiUsername,
            "apipassword" => $this->apiPassword,
            "apisignature" => $this->apiSignature,
            'TOKEN' => $this->session->get('token')
        ];

        $curl = new Curl($this->getCheckoutUrl);

        $curl->options = array(
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $param,
        );

        $getResult = $curl->make();
        
        parse_str($getResult, $result);
        
        return $result;
    }

    public function completePurchase()
    {
        $param = [
            "Method" => 'DoExpressCheckout',
            "version" => $this->version,
            "apiusername" => $this->apiUsername,
            "apipassword" => $this->apiPassword,
            "apisignature" => $this->apiSignature,
            "paymentaction" => 'Sale',
            'TOKEN' => $this->session->get('token'),
            'PayerId' => $this->request->query->get('payerId'),
            'PaymentRequest_Amt' => $this->session->get('paymentAmmount'),
        ];

        $curl = new Curl($this->doCheckoutUrl);

        $curl->options = array(
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $param,
        );

        $getResult = $curl->make();

        parse_str($getResult, $result);


        if($result['Ack'] == 'fail') {
            throw new AckFailException($result['LONGMESSAGE0']);
        }

        return $result;
    }

    protected function redirectToLogin()
    {
        return new Redirect;
    }


    /**
     * Gets the value of method.
     *
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the value of method.
     *
     * @param mixed $method the method
     *
     * @return self
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Gets the value of version.
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the value of version.
     *
     * @param mixed $version the version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Gets the value of paymentAction.
     *
     * @return mixed
     */
    public function getPaymentAction()
    {
        return $this->paymentAction;
    }

    /**
     * Sets the value of paymentAction.
     *
     * @param mixed $paymentAction the payment action
     *
     * @return self
     */
    public function setPaymentAction($paymentAction)
    {
        $this->paymentAction = $paymentAction;

        return $this;
    }

    /**
     * Gets the value of apiUsername.
     *
     * @return mixed
     */
    public function getApiUsername()
    {
        return $this->apiUsername;
    }

    /**
     * Sets the value of apiUsername.
     *
     * @param mixed $apiUsername the api username
     *
     * @return self
     */
    public function setApiUsername($apiUsername)
    {
        $this->apiUsername = $apiUsername;

        return $this;
    }

    /**
     * Gets the value of apiPassword.
     *
     * @return mixed
     */
    public function getApiPassword()
    {
        return $this->apiPassword;
    }

    /**
     * Sets the value of apiPassword.
     *
     * @param mixed $apiPassword the api password
     *
     * @return self
     */
    public function setApiPassword($apiPassword)
    {
        $this->apiPassword = $apiPassword;

        return $this;
    }

    /**
     * Gets the value of apiSignature.
     *
     * @return mixed
     */
    public function getApiSignature()
    {
        return $this->apiSignature;
    }

    /**
     * Sets the value of apiSignature.
     *
     * @param mixed $apiSignature the api signature
     *
     * @return self
     */
    public function setApiSignature($apiSignature)
    {
        $this->apiSignature = $apiSignature;

        return $this;
    }

    /**
     * Gets the value of returnUrl.
     *
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Sets the value of returnUrl.
     *
     * @param mixed $returnUrl the return url
     *
     * @return self
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /**
     * Gets the value of cancelUrl.
     *
     * @return mixed
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * Sets the value of cancelUrl.
     *
     * @param mixed $cancelUrl the cancel url
     *
     * @return self
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * Gets the value of env.
     *
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Sets the value of env.
     *
     * @param mixed $env the env
     *
     * @return self
     */
    protected function setEnv($env)
    {
        $this->env = $env;

        return $this;
    }

    /**
     * Gets the value of checkoutUrl.
     *
     * @return mixed
     */
    public function getCheckoutUrl()
    {
        return $this->checkoutUrl;
    }

    /**
     * Sets the value of checkoutUrl.
     *
     * @param mixed $checkoutUrl the checkout url
     *
     * @return self
     */
    protected function setCheckoutUrl($checkoutUrl)
    {
        $this->checkoutUrl = $checkoutUrl;

        return $this;
    }

    /**
     * Gets the value of getCheckoutUrl.
     *
     * @return mixed
     */
    public function getGetCheckoutUrl()
    {
        return $this->getCheckoutUrl;
    }

    /**
     * Sets the value of getCheckoutUrl.
     *
     * @param mixed $getCheckoutUrl the get checkout url
     *
     * @return self
     */
    protected function setGetCheckoutUrl($getCheckoutUrl)
    {
        $this->getCheckoutUrl = $getCheckoutUrl;

        return $this;
    }

    /**
     * Gets the value of doCheckoutUrl.
     *
     * @return mixed
     */
    public function getDoCheckoutUrl()
    {
        return $this->doCheckoutUrl;
    }

    /**
     * Sets the value of doCheckoutUrl.
     *
     * @param mixed $doCheckoutUrl the do checkout url
     *
     * @return self
     */
    protected function setDoCheckoutUrl($doCheckoutUrl)
    {
        $this->doCheckoutUrl = $doCheckoutUrl;

        return $this;
    }

    /**
     * Gets the value of loginUrl.
     *
     * @return mixed
     */
    public function getLoginUrl()
    {
        return $this->loginUrl;
    }

    /**
     * Sets the value of loginUrl.
     *
     * @param mixed $loginUrl the login url
     *
     * @return self
     */
    protected function setLoginUrl($loginUrl)
    {
        $this->loginUrl = $loginUrl;

        return $this;
    }

    /**
     * Gets the value of headerImg.
     *
     * @return mixed
     */
    public function getHeaderImg()
    {
        return $this->headerImg;
    }

    /**
     * Sets the value of headerImg.
     *
     * @param mixed $headerImg the header img
     *
     * @return self
     */
    public function setHeaderImg($headerImg)
    {
        $this->headerImg = $headerImg;

        return $this;
    }

    /**
     * Gets the value of brandName.
     *
     * @return mixed
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * Sets the value of brandName.
     *
     * @param mixed $brandName the brand name
     *
     * @return self
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * Gets the value of customerServiceNumber.
     *
     * @return mixed
     */
    public function getCustomerServiceNumber()
    {
        return $this->customerServiceNumber;
    }

    /**
     * Sets the value of customerServiceNumber.
     *
     * @param mixed $customerServiceNumber the customer service number
     *
     * @return self
     */
    public function setCustomerServiceNumber($customerServiceNumber)
    {
        $this->customerServiceNumber = $customerServiceNumber;

        return $this;
    }
}