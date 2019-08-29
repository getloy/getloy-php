<?php declare(strict_types=1);

namespace Getloy\PaymentProviders;

use Getloy\PaymentProviders;
use Getloy\PaymentProviders\Configuration\PaymentProviderConfigPipayKh;
use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;
use Getloy\TransactionDetails\OrderDetails;
use Getloy\TransactionDetails\OrderItems;
use Getloy\TransactionDetails\PayeeDetails;

class PaymentProviderPipayKh extends PaymentProvider
{
    protected $paymentMethod = PaymentProviders::PIPAY_KH;
    
    /**
     * Instantiate a payment provider config for the payment method.
     *
     * @param array $config Configuration for the payment method.
     * @return PaymentProviderConfig The configuration.
     */
    protected function instantiateValidator(array $config): PaymentProviderConfig
    {
        return new PaymentProviderConfigPipayKh($config);
    }

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
    public function paymentProviderPayload(
        string $transactionId,
        OrderDetails $order,
        PayeeDetails $payee,
        string $callbackUrl,
        string $paymentMethodVariant = null
    ): array {

        return [
            'merchant_id' => $this->config->get('merchantId'),
            'store_id' => $this->config->get('storeId'),
            'device_id' => $this->config->get('deviceId'),
        ];
    }
}
