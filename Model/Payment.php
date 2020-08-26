<?php
namespace Apexx\Cse\Model;
class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
 const CODE = 'cse_gateway';

    protected $_code = self::CODE;
}