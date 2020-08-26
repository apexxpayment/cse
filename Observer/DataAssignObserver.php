<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse 
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;

use Magento\Payment\Model\InfoInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
     /**
     * @param Observer $observer
     * @return void
     */
    const xCardNum = 'cc_number';
    const xCVV = 'cc_cid';
    const cc_exp_month = 'cc_exp_month';
    const cc_exp_year = 'cc_exp_year';
    const enc_val = 'enc_val' ;
    const maskedCardNumber = 'maskedCardNumber' ;

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::xCardNum,
        self::xCVV,
        self::cc_exp_month,
        self::cc_exp_year,
        self::enc_val,
        self::maskedCardNumber,
        "is_active_payment_token_enabler"
    ];

    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}

