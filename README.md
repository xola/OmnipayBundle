Xola OmnipayBundle [![Build status...](https://secure.travis-ci.org/xola/OmnipayBundle.png)](http://travis-ci.org/xola/OmnipayBundle)
==================
This bundle integrates the [Omnipay payment processing library](https://github.com/adrianmacneil/omnipay) into
[Symfony2](http://symfony.com/).

This bundle supports Omnipay 2.x

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
    # Custom gateway
    omnipay.my_custom_key.apiKey: myGatewayKey
    omnipay.my_custom_key.gateway: MyGateway

    # Default Stripe gateway
    omnipay.stripe_default.apiKey: myApiKey
    omnipay.stripe_default.gateway: Stripe

    # Gateway for Stripe Canada account
    omnipay.stripe_canada.apiKey: myStripeCanadaApiKey
    omnipay.stripe_canada.gateway: Stripe

    # Authorize.NET AIM
    omnipay.authorize_net_aim.transactionKey: myTransactionKey
    omnipay.authorize_net_aim.gateway: AuthorizeNet_AIM
```
In the sample configuration above, `my_custom_key` is a unique key you define for each of your gateways.
`omnipay.my_custom_name.gateway` is the class name for a Omnipay gateway driver (e.g. `Stripe`). You may choose to define
multiple keys for the same Omnipay gateway with different credentials. In the above configuration, we have configured
two gateway definitions for Stripe -- both use the Stripe Omnipay driver, however, they each use a different set of
credentials.


Usage
-----
Use the new `omnipay` service to create gateway object:

```php
    // From within a controller. This will return an instance `\Omnipay\Stripe`. `stripe_default` is the key as
    // specified in the config.
    $gateway = $this->get('omnipay')->get('stripe_default');

    // From within a controller. This will return an instance of `\Omnipay\MyGateway` as specified in
    // `omnipay.my_custom_name.gateway`
    $gateway = $this->get('omnipay')->get('my_custom_name');
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