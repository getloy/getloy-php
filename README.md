# GetLoy Integration Library for PHP
[![Latest Stable Version](https://poser.pugx.org/getloy/getloy-php/v/stable)](https://packagist.org/packages/getloy/getloy-php) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/getloy/getloy-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/getloy/getloy-php/?branch=master) [![Total Downloads](https://poser.pugx.org/getloy/getloy-php/downloads)](https://packagist.org/packages/getloy/getloy-php) [![License](https://poser.pugx.org/getloy/getloy-php/license)](https://packagist.org/packages/getloy/getloy-php)

The GetLoy Integration Library provides an easy way to access the GetLoy API from applications 
written in PHP.

The library currently supports the following payment methods:

* iPay88 Cambodia (all supported payment methods)
* Pi Pay
* PayWay by ABA Bank (debit/credit card only, ABA Pay only, debit/credit card only, or both)

## Requirements

PHP 5.6.0 or later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require getloy/getloy-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/getloy/getloy-php/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/getloy-php/init.php');
```

## Getting Started

A simple usage example:

```php
$gateway = new \Getloy\Gateway('YOUR GETLOY TOKEN');
$gateway->registerPaymentProvider(
    \Getloy\PaymentProviders::PIPAY_KH,
    [
        'testMode' => true,
        'merchantId' => 'YOUR PIPAY MERCHANT ID',
        'storeId' => 'YOUR PIPAY STORE ID',
        'deviceId' => 'YOUR PIPAY DEVICE ID',
    ]
);

$order = new \Getloy\TransactionDetails\OrderDetails(99.99, 'USD');
$payee = new \Getloy\TransactionDetails\PayeeDetails('John', 'Doe', 'j.doe@test.com', '012345678');

echo $gateway->widgetHtml(
    'ORDER-123',
    \Getloy\PaymentProviders::PIPAY_KH,
    $order,
    $payee,
    'https://mysite.com/payment-callback.php',
    'https://mysite.com/pages/payment-complete/'
    'https://mysite.com/pages/payment-failed/'
);
```

## Passing additional order details
Most payment systems will display additional order details such as the order line items in the payment interface. This information can be provided in the `widgetHtml()` call.

```php
$orderItems = new \Getloy\TransactionDetails\OrderItems();

$orderItems->add(
    new \Getloy\TransactionDetails\OrderItem('Item 1', 2, 19.98, 9.99)
);
$orderItems->add(
    new \Getloy\TransactionDetails\OrderItem('Item 2', 1, 12.50, 12.50)
);

$order = new \Getloy\TransactionDetails\OrderDetails(32.48, 'USD', null, $orderItems);
```

## Callback validation

Getloy will make a POST request to the callback URL provided in the `widgetHtml()` call once the payment was confirmed, but before the user is redirected to the success URL.

The callback content is validated against a hash value that includes the (secret) GetLoy Token.

The following example, `$order` and `lookupOrder()` are placeholders for objects/methods provided by your application.

```php
$gateway = new \Getloy\Gateway('YOUR GETLOY TOKEN');

if ('POST' !== $_SERVER['REQUEST_METHOD']) {
    // unsupported request type
    return;
}

try {
    $callbackDetails = $gateway->readCallbackBodyAndParse();
} catch (Exception $exception) {
    // log invalid callback
    return;
}

if (Getloy\CallbackDetails::STATUS_SUCCESS !== $callbackDetails->status()) {
    // log invalid transaction status
    return;
}

// look up the order details using the transaction ID
$order = lookupOrder($callbackDetails->transactionId());

if (
    abs($order->amountPaid - $callbackDetails->amountPaid()) > 0.01
    || $order->currency !== $callbackDetails->currency()
) {
    // log transaction amount/currency mismatch
    return;
}

// update the order status to paid
$order->updateStatus('paid');
```

If you are developing your application locally, you can use a service like [ngrok](https://ngrok.com/) to redirect callbacks from GetLoy to your local webserver.
