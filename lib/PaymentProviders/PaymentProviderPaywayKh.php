<?php declare(strict_types=1);

namespace Getloy\PaymentProviders;

use Getloy\PaymentProviders;
use Getloy\PaymentProviders\Configuration\PaymentProviderConfigPaywayKh;
use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;
use Getloy\TransactionDetails\OrderDetails;
use Getloy\TransactionDetails\OrderItems;
use Getloy\TransactionDetails\PayeeDetails;

class PaymentProviderPaywayKh extends PaymentProvider
{
    protected $paymentMethod = PaymentProviders::PAYWAY_KH;
    
    /**
     * Instantiate a payment provider config for the payment method.
     *
     * @param array $config Configuration for the payment method.
     * @return PaymentProviderConfig The configuration.
     */
    protected function instantiateValidator(array $config): PaymentProviderConfig
    {
        return new PaymentProviderConfigPaywayKh($config);
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
            'init_token' => $this->initToken($transactionId, $order),
            'status_token' => $this->statusToken($transactionId),
            'payment_method' => $this->config->get('allowedMethods'),
        ];
    }

    /**
     * Gnerate status token for payment provider payload.
     *
     * @param string $transactionId
     * @return string
     */
    protected function statusToken(string $transactionId): string
    {
        return hash_hmac(
            'sha512',
            $this->config->get('merchantId') . $transactionId,
            $this->config->get('merchantKey')
        );
    }

    protected function initToken(string $transactionId, OrderDetails $order): string
    {
        return hash_hmac(
            'sha512',
            sprintf(
                '%s%s%.2f%s',
                $this->config->get('merchantId'),
                $transactionId,
                $order->totalAmount(),
                $this->base64OrderItems($order->orderItems())
            ),
            $this->config->get('merchantKey')
        );
    }

    /**
     * Transform an array of order items to a base64 encoded string.
     *
     * @param OrderItems $orderItems
     * @return string Order items as base64 encoded string
     */
    protected function base64OrderItems(OrderItems $orderItems): string
    {
        $items = [];
        foreach ($orderItems->payloadConfig() as $item) {
            $items[] = [
                'name' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['unit_price'],
            ];
        }

        return base64_encode(mb_convert_encoding(json_encode($items), 'UTF-8'));
    }
}
