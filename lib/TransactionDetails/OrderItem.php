<?php declare(strict_types=1);

namespace Getloy\TransactionDetails;

/**
 * Single transaction order item.
 */
class OrderItem
{
    protected $description;
    protected $quantity;
    protected $totalPrice;
    protected $unitPrice;

    /**
     * Instantiate order item.
     *
     * @param string $description Order item description.
     * @param integer $quantity Ordered quantity.
     * @param float $totalPrice Total price for the ordered items.
     * @param float $unitPrice Price per unit (optional).
     */
    public function __construct(
        string $description,
        int $quantity,
        float $totalPrice,
        float $unitPrice = null
    ) {

        $this->description = $description;
        $this->quantity = $quantity;
        $this->totalPrice = round($totalPrice, 2);
        $this->unitPrice = round($unitPrice, 2);
    }

    /**
     * Generate partial GetLoy widget payload configuration for the instance.
     *
     * @return array Partial widget payload configuration.
     */
    public function payloadConfig(): array
    {
        $payload = [
            'description' => $this->description,
            'quantity' => $this->quantity,
            'total_price' => $this->totalPrice,
        ];
        if ($this->unitPrice) {
            $payload['unit_price'] = $this->unitPrice;
        }
        return $payload;
    }
}
