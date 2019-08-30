<?php declare(strict_types=1);

namespace Getloy\TransactionDetails;

/**
 * Data structure for order details
 */
class OrderDetails
{
    protected $totalAmount;
    protected $currency;
    protected $orderTimestamp;
    protected $orderItems;

    /**
     * Instantiate order details.
     *
     * @param float $totalAmount The total order amount.
     * @param string $currency The order currency code.
     * @param \DateTime $orderTimestamp Order timestamp (optional, default is current date/time).
     * @param OrderItems $orderItems Order items collection (optional).
     */
    public function __construct(
        float $totalAmount,
        string $currency,
        \DateTime $orderTimestamp = null,
        OrderItems $orderItems = null
    ) {

        $this->totalAmount = $totalAmount;
        $this->currency = $currency;
        $this->orderTimestamp = $orderTimestamp ?: new \DateTime();
        $this->orderItems = $orderItems ?: new OrderItems();
    }

    /**
     * Get the total order amount
     *
     * @return float Total amount (in order currency)
     */
    public function totalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * Get the order currency code
     *
     * @return string Order currency code
     */
    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * Get the order items
     *
     * @return OrderItems Order items
     */
    public function orderItems(): OrderItems
    {
        return $this->orderItems;
    }

    /**
     * Generate partial GetLoy widget payload configuration for the instance.
     *
     * @return array Partial widget payload configuration.
     */
    public function payloadConfig(): array
    {
        return [
            'total_amount' => sprintf('%.2f', $this->totalAmount),
            'currency' => $this->currency,
            'order_timestamp' => date_format($this->orderTimestamp, DATE_ATOM),
            'order_items' => $this->orderItems->payloadConfig(),
        ];
    }
}
