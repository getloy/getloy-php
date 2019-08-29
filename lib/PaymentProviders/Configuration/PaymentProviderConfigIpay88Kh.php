<?php declare(strict_types=1);

namespace Getloy\PaymentProviders\Configuration;

use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;

class PaymentProviderConfigIpay88Kh extends PaymentProviderConfig
{
    /**
     * Generate validation configuration for payment method-specific configuration options.
     * @return array Validation configuration
     */
    // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
    protected function paymentMethodConfigValidation(): array
    {
        return [
            'merchantCode' => [
                'type' => 'string',
                'required' => true,
            ],
            'merchantKey' => [
                'type' => 'string',
                'required' => true,
            ],
        ];
    }
}
