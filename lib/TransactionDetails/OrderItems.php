<?php declare(strict_types=1);

namespace Getloy\TransactionDetails;

/**
 * Transaction order items.
 */
class OrderItems
{
    protected $orderItems = [];

    /**
     * Add order item.
     *
     * @param OrderItem $orderItem Order item
     * @return void
     */
    public function add(OrderItem $orderItem)
    {
        $this->orderItems[] = $orderItem;
    }

    /**
     * Generate partial GetLoy widget payload configuration for the instance.
     *
     * @return array Partial widget payload configuration.
     */
    public function payloadConfig(): array
    {
        $payload = [];
        foreach ($this->orderItems as $item) {
            $payload[] = $item->payloadConfig();
        }
        return $payload;
    }
}
