<?php

namespace Apexx\Cse\Api;

interface ApexxCseOrderPaymentStatusInterface
{
    /**
     * @param string $orderId
     * @return string
     */
    public function getOrderPaymentStatus($orderId);
}
