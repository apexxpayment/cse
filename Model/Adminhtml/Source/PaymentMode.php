<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Model\Adminhtml\Source;

/**
 * Class PaymentMode
 * @package Apexx\Cse\Model\Adminhtml\Source
 */
class PaymentMode
{
    public function toOptionArray()
    {
        return [
                    ['value' => 'TEST', 'label' => __('Test')],
                    ['value' => 'LIVE', 'label' => __('Live')],
        ];
    }
}
