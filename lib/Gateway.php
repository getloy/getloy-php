<?php declare(strict_types=1);

namespace Getloy;

use \Exception;
use Getloy\PaymentProviders;
use Getloy\CallbackDetails;
use Getloy\TransactionDetails\OrderDetails;
use Getloy\TransactionDetails\PayeeDetails;

/**
 * Gateway
 */
class Gateway
{
    protected $getloyToken;
    protected $requestOrigin;
    protected $paymentProviders = [];

    public function __construct(
        string $getloyToken,
        string $requestOrigin = 'getloy-integration-library-php v1.0.0'
    ) {

        $this->getloyToken = $getloyToken;
        $this->requestOrigin = $requestOrigin;
    }

    /**
     * Getter for the GetLoy token.
     *
     * @return string
     */
    public function getloyToken(): string
    {
        return $this->getloyToken;
    }

    /**
     * Register a payment provider
     * @param string $paymentMethod Payment method identifier.
     * @param array $config Configuration for the payment method.
     * @return boolean True if the registration completed successfully.
     * @throws Exception If the provided payment method is unsupported or the payment method
     *                   configuration is incomplete.
     */
    public function registerPaymentProvider(string $paymentMethod, array $config): bool
    {
        $this->paymentProviders[$paymentMethod] = PaymentProviders::paymentProviderFactory(
            $paymentMethod,
            $config
        );
        return true;
    }

    /**
     * Generates JavaScript code for the GetLoy widget.
     * @param array $options The widget options.
     * @return string Widget code (without enclosing <scipt> tag).
     * @throws Exception if $options array does not include the key `payload`.
     */
    protected function widgetCode(array $options): string
    {
        if (!array_key_exists('payload', $options)) {
            throw new Exception('Cannot create widget without payload option!');
        }
        $optionString = '';
        foreach ($options as $option => $value) {
            $optionString .= sprintf(
                "gl(%s, %s);",
                json_encode($option),
                json_encode($value)
            );
        }
        return sprintf(
            "!function(g,e,t,l,o,y){g.GetLoyPayments=t;g[t]||(g[t]=function(){"
            . "(g[t].q=g[t].q||[]).push(arguments)});g[t].l=+new Date;o=e.createElement(l);"
            . "y=e.getElementsByTagName(l)[0];o.src='https://some.getloy.com/getloy.js';"
            . "y.parentNode.insertBefore(o,y)}(window,document,'gl','script');"
            . "%s",
            $optionString
        );
    }

    /**
     * Generates HTML code for the GetLoy widget.
     * @param string $transactionId Payment transaction identifier.
     * @param string $paymentMethod Payment method identifier (see {@see \Getloy\PaymentProviders}).
     * @param OrderDetails $order Order details.
     * @param PayeeDetails $payee Payee details.
     * @param string $callbackUrl URL to call from the GetLoy backend to notify the custom web
     *                            application of status updates for the payment transaction.
     * @param string $successUrl URL to redirect the user to when the payment completed successfully
     *                           (optional).
     * @param string $cancelUrl URL to redirect the user to when the payment gets cancelled or fails
     *                          (optional).
     * @param string $paymentMethodVariant Payment method variant name (optional).
     * @param bool $addPopupContainer Whether to add the HTML element that will contain the payment
     *                                popup (default is true).
     * @return string Widget HTML code.
     * @throws Exception if the provided transaction details are invalid.
     */
    public function widgetHtml(
        string $transactionId,
        string $paymentMethod,
        OrderDetails $order,
        PayeeDetails $payee,
        string $callbackUrl,
        string $successUrl = null,
        string $cancelUrl = null,
        string $paymentMethodVariant = null,
        bool $addPopupContainer = true
    ): string {

        $payload = $this->widgetPayload(
            $transactionId,
            $paymentMethod,
            $order,
            $payee,
            $callbackUrl,
            $paymentMethodVariant
        );
        $widgetOptions = [
            'payload' => $payload,
            'success_callback' => sprintf(
                "function(){window.location='%s';}",
                addcslashes($successUrl, "'")
            ),
            'cancel_callback' => sprintf(
                "function(){window.location='%s';}",
                addcslashes($cancelUrl, "'")
            ),
        ];
        return sprintf(
            "%s<script>\n%s\n</script>",
            $addPopupContainer ? '<div class="getloy"></div>' : '',
            $this->widgetCode($widgetOptions)
        );
    }

    /**
     * Generate payload for GetLoy widget.
     *
     * @param string $transactionId Payment transaction identifier.
     * @param string $paymentMethod Payment method identifier.
     * @param OrderDetails $order Order details.
     * @param PayeeDetails $payee Payee details.
     * @param string $callbackUrl URL to call from the GetLoy backend to notify the custom web
     *                            application of status updates for the payment transaction.
     * @param string $paymentMethodVariant Payment method variant name (optional).
     * @return string Widget payload (JSON string)
     */
    protected function widgetPayload(
        string $transactionId,
        string $paymentMethod,
        OrderDetails $order,
        PayeeDetails $payee,
        string $callbackUrl,
        string $paymentMethodVariant = null
    ): array {

        $payload = [
            'tid' => $transactionId,
            'provider' => $paymentMethod,
            'request_origin' => $this->requestOrigin,
            'callback' => $callbackUrl,
            'merchant_hash' => $this->merchantHash(),
            'auth_hash' => $this->transactionHash($transactionId, $order),
            'test_mode' => $this->paymentProviders[$paymentMethod]->get('testMode'),
            'payee' => $payee->payloadConfig(),
            'order' => $order->payloadConfig(),
            'payment_provider' => $this->paymentProviders[$paymentMethod]->paymentProviderPayload(
                $transactionId,
                $order,
                $payee,
                $callbackUrl,
                $paymentMethodVariant
            ),
        ];
        if ($paymentMethodVariant) {
            $payload['provider_variant'] = $paymentMethodVariant;
        }
        return $payload;
    }

    /**
     * Parse and validate the body of a callback request.
     *
     * @param string $callbackBody
     * @return CallbackDetails
     */
    public function parseCallback(string $callbackBody): CallbackDetails
    {
        $callbackDetails = new CallbackDetails($this);
        $callbackDetails->parseCallback($callbackBody);
        return $callbackDetails;
    }

    /**
     * Read callback body from php://input, parse and validate the  request.
     *
     * @return CallbackDetails
     */
    public function readCallbackBodyAndParse(): CallbackDetails
    {
        $callbackBody = file_get_contents('php://input');
        return $this->parseCallback($callbackBody);
    }

    /**
     * Compute the merchant hash.
     *
     * @return string Hash value (hex string).
     */
    protected function merchantHash(): string
    {
        return hash_hmac('sha512', $this->getloyToken, $this->getloyToken);
    }

    /**
     * Compute the transaction authentication hash.
     *
     * @param string $transactionId Payment transaction identifier.
     * @param OrderDetails $order Order details.
     * @return string Hash value (hex string).
     */
    protected function transactionHash(string $transactionId, OrderDetails $order): string
    {
        return hash_hmac(
            'sha512',
            sprintf(
                '%s%s%.2f',
                $this->getloyToken,
                $transactionId,
                $order->totalAmount()
            ),
            $this->getloyToken
        );
    }
}
