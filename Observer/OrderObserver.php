<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Apexx\Cse\Helper\Data as configHelper;


/**
 * Class OrderObserver
 * @package Apexx\Cse\Observer
 */

class OrderObserver extends AbstractDataAssignObserver
{
    /**
     * @var configHelper
     */
    protected  $configHelper;

    /**
     * @param Observer $observer
     */

    public function __construct(
        configHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $eventName = $observer->getEvent()->getName();
        $method = $order->getPayment()->getMethod();
        $paymentmode = $this->configHelper->getPaymentAction();
        if ($method == 'cse_gateway' && $paymentmode == 'false') {
            switch ($eventName) {
                case 'sales_order_place_after':
                    $this->updateOrderState($observer);

                    break;
            }
        }
    }

    /**
     * @param $observer
     */
    public function updateOrderState($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $order->setStatus('authorised');
        //$order->setIsNotified(false);
        $order->save();
    }
}
