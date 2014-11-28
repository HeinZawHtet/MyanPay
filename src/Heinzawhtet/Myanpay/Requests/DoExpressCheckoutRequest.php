<?php namespace Heinzawhtet\Myanpay\Requests;

use Symfony\Component\HttpFoundation\Request;
use Heinzawhtet\Myanpay\Exceptions\AckFailException;
use Heinzawhtet\Myanpay\Exceptions\EmptyResponseException;
use Symfony\Component\HttpFoundation\Session\Session;

class DoExpressCheckoutRequest extends AbstractRequest {

    /**
     * SetExpressCheckout Endpoint
     *
     * @var string
     * @access protected
     */
    protected $endpoint = "https://www.myanpay.com.mm/Personal/ExpressCheckout/DoExpressCheckOutRequestHandler.aspx";

    /**
     * SetExpressCheckout Endpoint for development
     *
     * @var string
     * @access protected
     */
    protected $developmentEndpoint = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/DoExpressCheckOutRequestHandler.aspx";

    /**
     * Session
     *
     * @var object
     * @access protected
     */
    protected $session;

    /**
     * Request object
     *
     * @var object
     * @access protected
     */
    protected $request;

    public function __construct(Request $request = null, Session $session = null) {
        $this->request = $request ?: Request::createFromGlobals();
        $this->session = $session ?: new Session;
    }

    /**
     * Send request to endpoints & receive data
     *
     * @param object $obj
     * 
     * @return object Redirect
     */
    public function initialize($obj)
    {
        $param = [
            "Method" => 'DoExpressCheckout',
            "version" => parent::API_VERSION,
            "apiusername" => $obj->getApiUsername(),
            "apipassword" => $obj->getApiPassword(),
            "apisignature" => $obj->getApiSignature(),
            "paymentaction" => 'Sale',
            'TOKEN' => $this->session->get('token'),
            'PayerId' => $this->request->query->get('payerId'),
            'PaymentRequest_Amt' => $this->session->get('paymentAmmount'),
        ];

        $result = $this->sendRequest($this->getEndpoint($obj->getDev()), $param);


        if (empty($result)) {
            throw new EmptyResponseException('No Response returned');
        }

        if ($result['Ack'] == 'fail') {
            throw new AckFailException($result['LONGMESSAGE0']);
        }

        return $result;
    }
}