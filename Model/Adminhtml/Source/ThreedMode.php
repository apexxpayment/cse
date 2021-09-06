<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Model\Adminhtml\Source;

/**
 * Class ThreedMode
 * @package Apexx\Cse\Model\Adminhtml\Source
 */
class ThreedMode
{
    public function toOptionArray()
    {
        return [
                    ['value' => 'sca', 'label' => __('sca (sca)')],
                    ['value' => 'frictionless', 'label' => __('frictionless (frictionless)')],
        ];
    }
}
