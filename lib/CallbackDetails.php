<?php declare(strict_types=1);

namespace Getloy;

use \Exception;

/**
 * Callback details
 */
class CallbackDetails
{
    /** Payment completed successfully */
    const STATUS_SUCCESS = 'successful';

    protected $gateway;
    protected $transactionId;
    protected $status;
    protected $amountPaid;
    protected $currency;

    /**
     * Instantiate callback details
     *
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Parse and validate the body of a callback request.
     *
     * @param string $callbackBody Raw callback body.
     * @throws Exception If the body is invalid.
     */
    public function parseCallback(string $callbackBody): void
    {
        $callbackData = json_decode($callbackBody, true);
        if (is_null($callbackData)) {
            throw new Exception('Malformed callback body!');
        }
        $this->validateCallback($callbackData);
    }

    /**
     * Validate a parsed callback request.
     *
     * @param array $callbackBody Parsed callback body.
     * @throws Exception If the body is invalid.
     */
    public function validateCallback(array $callbackBody): void
    {
        $valueMap = [
            'transactionId' => [
              'name' =>'tid',
              'type' => 'string',
            ],
            'status' => [
              'name' =>'status',
              'type' => 'string',
            ],
            'amountPaid' => [
              'name' =>'amount_paid',
              'type' => 'float',
            ],
            'currency' => [
              'name' =>'currency',
              'type' => 'string',
            ],
        ];

        foreach ($valueMap as $callbackKey => $def) {
            if (!array_key_exists($callbackKey, $callbackBody)) {
                throw new Exception(
                    sprintf('Callback received without required key "%s"!', $callbackKey)
                );
            }
            $this->setProperty($def['name'], $callbackBody[$callbackKey], $def['type']);
        }
        if (!array_key_exists('auth_hash_ext', $callbackBody)
            || !$this->validateHash($callbackBody['auth_hash_ext'])
        ) {
            throw new Exception('Callback hash validation failed!');
        }
    }

    /**
     * Validate if the provided hash value matches the instance's callback details.
     *
     * @param string $hash The hash value.
     * @return boolean True if the hash value is valid.
     */
    protected function validateHash(string $hash): bool
    {
        $hashRecalc = hash_hmac(
            'sha512',
            sprintf(
                '%s|%s|%s|%s|%s',
                $this->gateway->getloyToken(),
                $this->transactionId,
                $this->amountPaid,
                $this->currency,
                $this->status
            ),
            $this->gateway->getloyToken()
        );

        return $hash === $hashRecalc;
    }

    /**
     * Convert and set property value.
     *
     * @param string $propName
     * @param string $value
     * @param string $type
     */
    protected function setProperty(string $propName, string $value, string $type = 'string')
    {
        switch ($type) {
            case 'float':
                $this->$propName = (float) $value;
                break;

            default:
                $this->$propName = $value;
        }
    }

    /**
     * Getter for transaction ID.
     *
     * @return string
     */
    public function transactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Getter for transaction status.
     *
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Getter for amount paid.
     *
     * @return float
     */
    public function amountPaid(): float
    {
        return $this->amountPaid;
    }

    /**
     * Getter for transaction currency.
     *
     * @return string
     */
    public function currency(): string
    {
        return $this->currency;
    }
}
