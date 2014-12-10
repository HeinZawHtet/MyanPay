<?php namespace Heinzawhtet\Myanpay\Requests;

use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;
use Heinzawhtet\Myanpay\Exceptions\AckFailException;
use Heinzawhtet\Myanpay\Exceptions\EmptyResponseException;
use Symfony\Component\HttpFoundation\Session\Session;

class SetExpressCheckoutRequest extends AbstractRequest {

    /**
     * SetExpressCheckout Endpoint
     *
     * @var string
     * @access protected
     */
    protected $endpoint = "https://www.myanpay.com.mm/Personal/ExpressCheckout/ExpressCheckoutRequestHandler.aspx";

    /**
     * SetExpressCheckout Endpoint for development
     *
     * @var string
     * @access protected
     */
    protected $developmentEndpoint = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/ExpressCheckoutRequestHandler.aspx";

    /**
     * Login Endpoint
     *
     * @var string
     * @access protected
     */
    protected $loginUrl = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/ExpressCheckoutLogin.aspx?cmd=express-checkout&token=";

     /**
     * Login Endpoint for development
     *
     * @var string
     * @access protected
     */
    protected $developmentLoginUrl = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/ExpressCheckoutLogin.aspx?cmd=express-checkout&token=";

     /**
     * Session
     *
     * @var object
     * @access protected
     */
    protected $session;

    public function __construct(Session $session = null) {
        $this->session = $session ?: new Session;
    }


     /**
     * Send request to endpoints & receive data
     *
     * @param object $obj
     * @param array $data
     * 
     * @return object Redirect
     */
    public function initialize($obj, $data)
    {
        $param = [
            "Method" => 'SetExpressCheckout',
            "version" => parent::API_VERSION,
            "apiusername" => $obj->getApiUsername(),
            "apipassword" => $obj->getApiPassword(),
            "apisignature" => $obj->getApiSignature(),
            "paymentaction" => 'Sale',
            "returnUrl" => $obj->getReturnUrl(),
            "cancelUrl" => $obj->getCancelUrl(),
            "noShipping" => $obj->getNoShipping(),
            "PaymentRequest_ItemTotalAmt" => $data['amount'],
            "paymentRequest_Amt" => $data['amount'],
            "paymentaction" => 'Sale'
        ];

        $param = array_merge($param, $obj->setItems($data['items']));

        $result = $this->sendRequest($this->getEndpoint($obj->getDev()), $param);

        if (empty($result)) {
            throw new EmptyResponseException('No Response returned');
        }

        if ($result['Ack'] == 'fail') {
            throw new AckFailException($result['LONGMESSAGE0']);
        }

        $this->session->set('token', $result['Token']);
        $this->session->set('paymentAmmount', $param['paymentRequest_Amt']);

        return Redirect::create($this->getLoginEndpoint($obj->getDev()) . urldecode($result['Token']));
    }


    /**
     * Send request & receive data
     *
     * @param string $env
     * 
     * @return boolean
     */
    public function getLoginEndpoint($env)
    {
        return $env ? $this->developmentLoginUrl : $this->loginUrl;
    }
}