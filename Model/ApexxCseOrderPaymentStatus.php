<?php


namespace Apexx\Cse\Model;

//use \Apexx\Cse\Model\Payment;

class ApexxCseOrderPaymentStatus
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * AdyenOrderPaymentStatus constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param string $orderId
     * @return bool|string
     */
    public function getOrderPaymentStatus($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();


        if ($payment->getMethod() === Payment::CODE) {
            $additionalInformation = $payment->getAdditionalInformation();

            $additionInfo = json_encode($additionalInformation);

            return $additionInfo;
        }
        return true;
    }
}
