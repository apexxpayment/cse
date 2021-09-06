<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Model\Adminhtml\Source;

/**
 * Class CaptureMode
 * @package Apexx\Cse\Model\Adminhtml\Source
 */
class CaptureMode
{
    /**
     * Different payment actions.
     */
    const ACTION_AUTHORIZE = 'authorize';

    const ACTION_AUTHORIZE_CAPTURE = 'authorize_capture';

    public function toOptionArray()
    {
        return [
                    [
                        'value' => self::ACTION_AUTHORIZE_CAPTURE,
                        'label' => __('Yes')
                    ],
                    [
                        'value' => self::ACTION_AUTHORIZE,
                        'label' => __('No')
                    ],
        ];
    }
}
