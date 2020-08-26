<?php
/**
 * Custom payment method in Magento 2
 * @category    Cse
 * @package     Apexx_Cse
 */
namespace Apexx\Cse\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Apexx\Cse\Helper\Data as Config;
use Magento\Framework\Locale\ResolverInterface;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'cse_gateway';
    const encryption_key = 'encryption_key' ;
    private $config;

    public function __construct(
      Config $config,
        ResolverInterface $localeResolver
    ) {
    
        $this->config = $config;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
      public function getConfig()
        {
            $requestConfig = [
                'payment' => [
                    self::CODE => [
                        'encryption_key' => $this->config->getEncryptionKey()
                    ]
                ]
            ];

            return $requestConfig ; 
        }
}
