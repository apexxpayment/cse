<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Apexx\Cse\Helper\Data as configHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;

class RefundRequest implements BuilderInterface
{

    /**
     * @var CseHelper
     */
    protected  $configHelper;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    public function __construct(SubjectReader $subjectReader,
        configHelper $configHelper,
        ApexxBaseHelper $apexxBaseHelper
    )
    {
        $this->subjectReader = $subjectReader;
        $this->configHelper = $configHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
     public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();

        // Send Parameters to Paypal Payment Client
        $order = $paymentDO->getOrder();
        $amount = $buildSubject['amount'];

        //Get last transaction id for authorization
        $lastTransId = $this->apexxBaseHelper->getHostedPayTxnId($order->getId());

        if ($lastTransId != '') {
            $requestData = [
                "transactionId" => $lastTransId,
                "amount" => ($amount * 100),
                "reason" => time()."-".$order->getOrderIncrementId(),
                "capture_id" => $orderPayment->getParentTransactionId()
            ];
        } else {
            $requestData = [
                "transactionId" => $orderPayment->getParentTransactionId(),
                "amount" => ($amount * 100),
                "reason" => time()."-".$order->getOrderIncrementId()
            ];
        }

        return $requestData;
    }
}
