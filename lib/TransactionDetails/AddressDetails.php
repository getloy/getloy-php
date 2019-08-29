<?php declare(strict_types=1);

namespace Getloy\TransactionDetails;

/**
 * Transaction address details.
 */
class AddressDetails
{
    protected $firstName;
    protected $lastName;
    protected $company;
    protected $address;
    protected $city;
    protected $state;
    protected $postcode;
    protected $country;

    /**
     * Instantiate address details.
     *
     * @param string $firstName First name
     * @param string $lastName  Last name
     * @param string $company   Company name (optional)
     * @param string $address   Street address (optional)
     * @param string $city      City name (optional)
     * @param string $state     State name (optional)
     * @param string $postcode  Postcode (optional)
     * @param string $country   Country name (optional)
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $company = '',
        string $address = '',
        string $city = '',
        string $state = '',
        string $postcode = '',
        string $country = ''
    ) {

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->company = $company;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->postcode = $postcode;
        $this->country = $country;
    }

    /**
     * Generate partial GetLoy widget payload configuration for the instance.
     *
     * @return array Partial widget payload configuration.
     */
    public function payloadConfig(): array
    {
        $payload = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
        ];
        if ($this->company) {
            $payload['company'] = $this->company;
        }
        if ($this->address) {
            $payload['address'] = $this->address;
        }
        if ($this->city) {
            $payload['city'] = $this->city;
        }
        if ($this->state) {
            $payload['state'] = $this->state;
        }
        if ($this->postcode) {
            $payload['postcode'] = $this->postcode;
        }
        if ($this->country) {
            $payload['country'] = $this->country;
        }
        return $payload;
    }
}
