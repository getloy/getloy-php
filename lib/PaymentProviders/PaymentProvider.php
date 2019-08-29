<?php declare(strict_types=1);

namespace Getloy\PaymentProviders;

use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;
use Getloy\TransactionDetails\OrderDetails;
use Getloy\TransactionDetails\OrderItems;
use Getloy\TransactionDetails\PayeeDetails;

/**
 * Abstract payment provider class.
 */
abstract class PaymentProvider
{
    protected $paymentMethod;
    protected $requestOrigin = 'getloy-integration-library-php v1.0.0';
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $this->instantiateValidator($config);
    }

    /**
     * Instantiate a payment provider config for the payment method.
     *
     * @param array $config Configuration for the payment method.
     * @return PaymentProviderConfig The configuration.
     */
    abstract protected function instantiateValidator(array $config): PaymentProviderConfig;

    /**
     * Generate the payment provider-specific part of the widget payload.
     *
     * @param string $transactionId
     * @param OrderDetails $order
     * @param PayeeDetails $payee
     * @param string $callbackUrl
     * @param string $paymentMethodVariant Payment method variant name (optional).
     * @return array
     */
    abstract public function paymentProviderPayload(
        string $transactionId,
        OrderDetails $order,
        PayeeDetails $payee,
        string $callbackUrl,
        string $paymentMethodVariant = null
    ): array;

    /**
     * Get configuration value.
     * @param string $configOption Name of the configuration option.
     * @return mixed The configuration value, or an empty string if the option is not set.
     */
    public function get(string $option)
    {
        return $this->config->get($option);
    }
}
