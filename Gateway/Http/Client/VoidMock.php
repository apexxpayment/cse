<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\HTTP\Client\Curl;
use Apexx\Cse\Helper\Data as CseHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class ClientMock
 * @package Apexx\Cse\Gateway\Http\Client
 */
class VoidMock implements ClientInterface
{
    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var CseHelper
     */
    protected  $cseHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * VoidMock constructor.
     * @param Curl $curl
     * @param CseHelper $cseHelper
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param CustomLogger $customLogger
     */
    public function __construct(
        Curl $curl,
        CseHelper $cseHelper,
        ApexxBaseHelper $apexxBaseHelper,
        CustomLogger $customLogger
    ) {
        $this->curlClient = $curl;
        $this->CseHelper = $cseHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->customLogger = $customLogger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        $apiType = $this->apexxBaseHelper->getApiType();
        if ($apiType == 'Atomic') {
            $url = $this->apexxBaseHelper->getApiEndpoint().'cancel/payment/'.$request['transaction_id'];
        } else {
            // Set cancel url
            $url = $this->apexxBaseHelper->getApiEndpoint().$request['transaction_id'].'/cancel';
        }

        //Set parameters for curl
        unset($request['transaction_id']);
        $resultCode = json_encode( $request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('CSE Void Request:', $request);
        $this->customLogger->debug('CSE Void Response:', $responseResult);

        return $responseResult;
    }
}
