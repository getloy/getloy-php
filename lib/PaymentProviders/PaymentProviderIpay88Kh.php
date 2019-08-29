<?php declare(strict_types=1);

namespace Getloy\PaymentProviders;

use Getloy\PaymentProviders;
use Getloy\PaymentProviders\Configuration\PaymentProviderConfigIpay88Kh;
use Getloy\PaymentProviders\Configuration\PaymentProviderConfig;
use Getloy\TransactionDetails\OrderDetails;
use Getloy\TransactionDetails\OrderItems;
use Getloy\TransactionDetails\PayeeDetails;

class PaymentProviderIpay88Kh extends PaymentProvider
{
    const VARIANT_CREDITCARD = 'cc';
    const VARIANT_UNIONPAY = 'upay';
    const VARIANT_PIPAY = 'pipay';
    const VARIANT_WING = 'wing';
    const VARIANT_METFONE = 'metfone';
    const VARIANT_ALIPAY_BARCODE = 'alipayBarcode';
    const VARIANT_ALIPAY_QR = 'alipayQr';
    const VARIANT_ACLEDA_XPAY = 'acledaXpay';

    protected $paymentMethod = PaymentProviders::PIPAY_KH;
    protected $variantCodes = [
        'cc' => 1,
        'upay' => 15,
        'pipay' => 11,
        'wing' => 123,
        'metfone' => 9,
        'alipaybc' => 234,
        'alipayqr' => 233,
        'acledaxpay' => 3,
    ];
    
    /**
     * Instantiate a payment provider config for the payment method.
     *
     * @param array $config Configuration for the payment method.
     * @return PaymentProviderConfig The configuration.
     */
    protected function instantiateValidator(array $config): PaymentProviderConfig
    {
        return new PaymentProviderConfigIpay88Kh($config);
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

        $payload = [
            'merchant_code' => $this->config->get('merchantCode'),
            'signature' => $this->transactionSignature($transactionId, $order),
        ];

        if ($paymentMethodVariant && array_key_exists($paymentMethodVariant, $this->variantCodes)) {
            $payload['payment_method_id'] = $this->variantCodes[$paymentMethodVariant];
        }
        return $payload;
    }

    /**
     * Generate transaction signature for payment provider payload.
     *
     * @param string $transactionId
     * @param OrderDetails $order
     * @return string
     */
    public function transactionSignature(string $transactionId, OrderDetails $order): string
    {
        return base64_encode(
            sha1(
                $this->config->get('merchantKey') .
                $this->config->get('merchantCode') .
                $transactionId .
                sprintf('%03d', $order->totalAmount() * 100) .
                $order->currency(),
                true
            )
        );
    }
}
