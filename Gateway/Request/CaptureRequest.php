<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Apexx\Cse\Helper\Data as ConfigHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Magento\Checkout\Model\Session As CheckoutSession;

class CaptureRequest implements BuilderInterface
{
    /**
     * @var ConfigHelper
     */
    protected  $configHelper;
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
        /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;
             /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * CaptureRequest constructor.
     * @param ConfigInterface $config
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigInterface $config,
        ApexxBaseHelper $apexxBaseHelper,
        ConfigHelper $configHelper,
        CheckoutSession $checkoutSession
    ) {
        $this->config = $config;
        $this->configHelper = $configHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
   public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */

        $paymentDO = $buildSubject['payment'];

        $order = $paymentDO->getOrder();

        $address = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        $amount = $buildSubject['amount']*100;
        // echo "<pre>";
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

         // $order = $payment->getOrder();
         if($payment->getLastTransId())
        {
        $request = [
            "transactionId" => $payment->getParentTransactionId()
                ?: $payment->getLastTransId(),
          "amount" => $amount ,
          "capture_reference" => "Capture".$order->getOrderIncrementId()
                ];
            }
        else {
       $request = [
           // "account" => "1db380005b524103bf323f9ef63ae1cf",
            "organisation" => $this->apexxBaseHelper->getOrganizationId(),
            "currency"=> $this->checkoutSession->getQuote()->getQuoteCurrencyCode(),
            "amount"=> $amount,
                "capture_now"=> $this->configHelper->getPaymentAction(),
                "card" => [
                    "encrypted_data" => $payment->getAdditionalInformation("enc_val")
                ],
                "billing_address" => [
                    "first_name" => $billing->getFirstname(),
                    "last_name" => $billing->getLastname(),
                    "email" => $billing->getEmail(),
                    "address" => $billing->getStreetLine1().''.$billing->getStreetLine2(),
                    "city" => $billing->getCity(),
                    "state" => $billing->getRegionCode(),
                    "postal_code" => $billing->getPostcode(),
                    "country" => $billing->getCountryId()
                ],
                "customer_ip"=> $order->getRemoteIp(),
                "dynamic_descriptor" => $this->configHelper->getDynamicDescriptor(),
                "merchant_reference" => $this->apexxBaseHelper->getStoreCode().$order->getOrderIncrementId(),
                "recurring_type"=> $this->configHelper->getRecurringType(),
                "user_agent"=> $this->apexxBaseHelper->getUserAgent(),
                "webhook_transaction_update" => $this->configHelper->webhookUpdateUrl(),
                "three_ds"=>[
                "three_ds_required"=> $this->configHelper->getThreeDsRequired()
                ]
            ];
        }

        return $request ;

    }
}
