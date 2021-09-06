<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */ 
namespace Apexx\Cse\Gateway\Request;
use Magento\Payment\Gateway\ConfigInterface;
use Apexx\Cse\Helper\Data as configHelper;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Magento\Checkout\Model\Session As CheckoutSession;

class AuthorizationRequest implements BuilderInterface
{

     /**
     * @var CseHelper
     */
    protected  $configHelper;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;
           /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

     /**
     * AuthorizationRequest constructor.
     * @param ConfigInterface $config
     * @param configHelper $configHelper
     * @param Order $order
     */

    public function __construct(
        ConfigInterface $config,
        ApexxBaseHelper $apexxBaseHelper,
        configHelper $configHelper,
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
        $payment = $paymentDO->getPayment();
        $billing = $order->getBillingAddress();
        $amount = $buildSubject['amount']*100;

        $request = [
          //  "account" => "1db380005b524103bf323f9ef63ae1cf",
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
            "three_ds"=>[
                "three_ds_required"=> $this->configHelper->getThreeDsRequired()
            ]
        ];

        
        return $request ;
    }
}
