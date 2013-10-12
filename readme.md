(work in progress - DO NOT USE)

Xola OmnipayBundle
==================
This bundle integrates the [Omnipay payment processing library](https://github.com/adrianmacneil/omnipay) into
[Symfony2](http://symfony.com/).


Installation
------------
To install via [Composer](http://getcomposer.org/), add the following to your `composer.json` file:

```json
{
    "require": {
        "xola/omnipay-bundle": "1.*"
    }
}
```

Configuration
-------------
(Optional) In the Omnipay library, you would programmatically set parameters required by a gateway. With this bundle,
it's possible to configure these parameters in your Symfony config files.

```yaml
# app/config/password_dev.yml
omnipay:

    # Authorize.NET AIM
    authorize_net_aim:
        apiLoginId: myLoginId
        transactionKey: myTransactionKey

    # Stripe
    stripe:
        apiKey: myApiKey
```

In the configuration file, you'll need to "underscore" the names of the gateways, but their parameters should be left
intact i.e. the gateway name "AuthorizeNet_AIM" becomes "authorize_net_aim", however all it's parameters remain
unchanged and remain in camel case.

Usage
-----
Use the new `omnipay` service to create gateway classes:

```php
// From within a controller
$gateway = $this->get('omnipay')->create('Stripe');

// The rest is identical to how you would normally use Omnipay

$formData = ['number' => '4242424242424242', 'expiryMonth' => '11', 'expiryYear' => '2018', 'cvv' => '123'];
$response = $gateway->purchase(['amount' => '10.00', 'currency' => 'USD', 'card' => $formData])->send();

if ($response->isSuccessful()) {
    // payment was successful: update database
    print_r($response);
} elseif ($response->isRedirect()) {
    // redirect to offsite payment gateway
    $response->redirect();
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```
The gateway classes which are returned are already initialized with the parameters defined in the config files.
