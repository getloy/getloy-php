<?php declare(strict_types=1);

namespace Getloy\TransactionDetails;

/**
 * Payee details
 */
class PayeeDetails
{
    protected $firstName;
    protected $lastName;
    protected $email;
    protected $phone;
    protected $company;
    protected $address;
    protected $city;
    protected $state;
    protected $postcode;
    protected $country;

    /**
     * Instantiate payee details.
     *
     * @param string $firstName First name (optional)
     * @param string $lastName  Last name (optional)
     * @param string $email     Email address (optional)
     * @param string $phone     Phone number (optional)
     * @param string $company   Company name (optional)
     * @param string $address   Street address (optional)
     * @param string $city      City name (optional)
     * @param string $state     State code (ISO 3166-2 code, optional)
     * @param string $postcode  Postcode (optional)
     * @param string $country   Country code (ISO 3166-1 alpha-2 code, optional)
     */
    public function __construct(
        string $firstName = '',
        string $lastName = '',
        string $email = '',
        string $phone = '',
        string $company = '',
        string $address = '',
        string $city = '',
        string $state = '',
        string $postcode = '',
        string $country = ''
    ) {

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
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
        $payload = [];
        
        $payloadMapping = [
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'email_address' => 'email',
            'mobile_number' => 'phone',
            'company' => 'company',
            'address' => 'address',
            'city' => 'city',
            'state' => 'state',
            'country' => 'country',
        ];
        foreach ($payloadMapping as $payloadField => $propName) {
            if ($this->$propName) {
                $payload[$payloadField] = $this->$propName;
            }
        }

        return $payload;
    }
}
