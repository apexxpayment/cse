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
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Cse\Helper\Data as CseHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class ClientMock
 * @package Apexx\Cse\Gateway\Http\Client
 */
class AuthorizeMock implements ClientInterface
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
     * AuthorizeMock constructor.
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
        $this->cseHelper = $cseHelper;
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
        $apiType = $this->apexxBaseHelper->getApiType();
        if ($apiType == 'Atomic') {
            $url = $this->apexxBaseHelper->getApiEndpoint().'payment';
        } else {
            $url = $this->apexxBaseHelper->getApiEndpoint().'payment/direct';
        }

        $resultCode = json_encode($transferObject->getBody());

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);

        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('CSE Authorize Request:', $transferObject->getBody());
        $this->customLogger->debug('CSE Authorize Response:', $responseResult);

        return $responseResult;
    }
}
