<?php 

/**
 * A PHP Package for Payment with MyanPay
 *
 * @author Hein Zaw Htet <heinzawhtet.com>
 * @see http://www.heinzawhtet.com/myanpay
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2014 Hein Zaw Htet
 */

namespace Heinzawhtet\Myanpay;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;
use Symfony\Component\HttpFoundation\Session\Session;
use Heinzawhtet\Myanpay\Exceptions\AckFailException;
use Heinzawhtet\Myanpay\Exceptions\EmptyResponseException;

class Myanpay {

    const API_VERSION = '1.1';

    /**
     * Payment Action
     *
     * @var String
     * @access private
     */
    private $paymentAction = 'Sale';

    /**
     * API Username
     *
     * @var String
     * @access public
     */
    public $apiUsername;

    /**
     * API Password
     *
     * @var String
     * @access public
     */
    public $apiPassword;

    /**
     * API Signature
     *
     * @var String
     * @access public
     */
    public $apiSignature;

    /**
     * Header Image Url of Seller
     *
     * @var String
     * @access public
     */
    public $headerImg;

    /**
     * Brand Name of Seller
     *
     * @var String
     * @access public
     */
    public $brandName;

    /**
     * Customer Service Number of Seller
     *
     * @var Int
     * @access public
     */
    public $customerServiceNumber;

    /**
     * Url to return when payment was successful
     *
     * @var String
     * @access public
     */
    public $returnUrl;

    /**
     * Url to return when payment was cancel :'(
     *
     * @var String
     * @access public
     */
    public $cancelUrl;

    /**
     * Get shipping info from MyanPay
     *
     * @var Boolean
     * @access public
     */
    public $noShipping = false;

    /**
     * Check environment
     *
     * @var Boolean
     * @access public
     */
    public $dev;

    /**
     * Session object
     *
     * @var Object
     * @access protected
     */
    protected $session;

    /**
     * Redirect Object
     *
     * @var Object
     * @access protected
     */
    protected $redirect;


    public function __construct(Session $session = null)
    {
        $this->session = $session ?: new Session;
    }

    /**
     * Send data & request nesccessry credential from endpoint such as Token
     *
     * @param array $data
     *
     * @return \Heinzawhtet\Myanpay\Requests\SetExpressCheckoutRequest
     */
    public function purchase($data)
    {
        return $this->authorize($data);
    }


    /**
     * Get authorization from server
     *
     * @param array $data
     *
     * @return Redirect
     */
    public function authorize($data)
    {
        return $this->createRequest('\Heinzawhtet\Myanpay\Requests\SetExpressCheckoutRequest', $data);
    }

    /**
     * Completing a payment
     *
     * @return array
     */
    public function completePurchase()
    {
        return $this->createRequest('\Heinzawhtet\Myanpay\Requests\GetExpressCheckoutRequest');
    }

    /**
     * Fetch payment informations
     *
     * @return array
     */
    public function fetchDetail()
    {
        return $this->createRequest('\Heinzawhtet\Myanpay\Requests\DoExpressCheckoutRequest');
    }

    /** 
    * Create a instance and load method
    *
    * @param Object $class
    * @param Array $data
    *
    * @return array
    */
    public function createRequest($class, $data = null)
    {
        $obj = new $class;
        if (isset($data)) {
            return $obj->initialize($this, $data);
        }
        return $obj->initialize($this);
    }

    /**
     * Set items into MyanPay format
     *
     * @param array $items
     *
     * @return array
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

    /**
     * Gets the value of payment action.
     *
     * @return mixed
     */
    public function getPaymentAction()
    {
        return $this->paymentAction;
    }

    /**
     * Sets the value of version.
     *
     * @param mixed $version the version
     *
     * @return self
     */
    public function setVersion($paymentAction)
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
     * Gets the value of noShipping.
     *
     * @return mixed
     */
    public function getNoShipping()
    {
        return $this->noShipping;
    }

    /**
     * Sets the value of noShipping.
     *
     * @param mixed $noShipping the cancel url
     *
     * @return self
     */
    public function setNoShipping($noShipping)
    {
        $this->noShipping = $noShipping;

        return $this;
    }

    /**
     * Gets the value of env.
     *
     * @return mixed
     */
    public function getDev()
    {
        return $this->dev;
    }

    /**
     * Sets the value of env.
     *
     * @param mixed $env the env
     *
     * @return self
     */
    public function setDev($dev)
    {
        $this->dev = $dev;

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