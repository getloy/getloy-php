<?php declare(strict_types=1);

namespace Getloy\PaymentProviders\Configuration;

use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;

class PaymentProviderConfigPipayKh extends PaymentProviderConfig
{
    /**
     * Generate validation configuration for payment method-specific configuration options.
     * @return array Validation configuration
     */
    protected function paymentMethodConfigValidation(): array
    {
        return [
            'merchantId' => [
                'type' => 'string',
                'required' => true,
            ],
            'storeId' => [
                'type' => 'string',
                'required' => true,
            ],
            'deviceId' => [
                'type' => 'string',
                'required' => true,
            ],
        ];
    }
}
