Xola OmnipayBundle [![Build status...](https://secure.travis-ci.org/xola/OmnipayBundle.png)](http://travis-ci.org/xola/OmnipayBundle)
==================
This bundle integrates the [Omnipay payment processing library](https://github.com/adrianmacneil/omnipay) into
[Symfony2](http://symfony.com/).

This bundle supports Omnipay 1.x and 2.x

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
parameters:

    # Authorize.NET AIM
    omnipay.authorize_net_aim.apiLoginId: myLoginId
    omnipay.authorize_net_aim.transactionKey: myTransactionKey
    omnipay.authorize_net_aim.gateway: AuthorizeNet_AIM

    # Stripe
    omnipay.stripe.apiKey: myApiKey
    omnipay.stripe.gateway: Stripe

    # Custom gateway
    omnipay.custom_gateway.apiKey: myCustomGatewayKey
    omnipay.custom_gateway.gateway: \Custom\Gateway
```

Usage
-----
Use the new `omnipay` service to create gateway object:

```php
    // From within a controller. This will return an instance `\Omnipay\Stripe`
    $gateway = $this->get('omnipay')->create('stripe');
```

Use `omnipay` service to create custom gateway object

```php
    // From within a controller. This will return an instance `\Custom\Gateway`
    $gateway = $this->get('omnipay')->create('custom_gateway');
```


The rest is identical to how you would normally use Omnipay

```php
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
