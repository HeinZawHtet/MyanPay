<?php namespace Heinzawhtet\Myanpay\Requests;

use Heinzawhtet\Myanpay\Exceptions\AckFailException;
use Heinzawhtet\Myanpay\Exceptions\EmptyResponseException;
use Symfony\Component\HttpFoundation\Session\Session;

class GetExpressCheckoutRequest extends AbstractRequest {

    /**
     * SetExpressCheckout Endpoint
     *
     * @var string
     * @access protected
     */
    protected $endpoint = "https://www.myanpay.com.mm/Personal/ExpressCheckout/GetExpressCheckoutRequestHandler.aspx";

    /**
     * SetExpressCheckout Endpoint for development
     *
     * @var string
     * @access protected
     */
    protected $developmentEndpoint = "https://www.myanpay-virtualbox.com/Personal/ExpressCheckout/GetExpressCheckoutRequestHandler.aspx";

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
     * 
     * @return object Redirect
     */
    public function initialize($obj)
    {
        $param = [
            "Method" => 'GetExpressCheckout',
            "version" => parent::API_VERSION,
            "apiusername" => $obj->getApiUsername(),
            "apipassword" => $obj->getApiPassword(),
            "apisignature" => $obj->getApiSignature(),
            'TOKEN' => $this->session->get('token')
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