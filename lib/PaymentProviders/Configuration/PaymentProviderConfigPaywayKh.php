<?php declare(strict_types=1);

namespace Getloy\PaymentProviders\Configuration;

use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;

class PaymentProviderConfigPaywayKh extends PaymentProviderConfig
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
            'merchantKey' => [
                'type' => 'string',
                'required' => true,
            ],
            'allowedMethods' => [
                'type' => 'string',
                'required' => false,
                'default' => 'all',
                'allowedValues' => ['all', 'abapay', 'cards'],
            ],
        ];
    }
}
