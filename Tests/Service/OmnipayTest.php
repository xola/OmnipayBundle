<?php

namespace Xola\OmnipayBundle\Tests\Service;

use Guzzle\Log\MessageFormatter;
use Omnipay\Stripe\Gateway as StripeGateway;
use Xola\OmnipayBundle\Service\Omnipay;
use Xola\OmnipayBundle\DependencyInjection\OmnipayExtension;

class OmnipayTest extends \PHPUnit_Framework_TestCase
{
    private function buildService($params = array())
    {
        $defaults = array(
            'container' => $this->getMock('Symfony\Component\DependencyInjection\Container'),
            'logger' => $this->getMock('Psr\Log\LoggerInterface')
        );

        $params = array_merge($defaults, $params);

        return new Omnipay($params['container'], $params['logger']);
    }

    /**
     * @param array $params
     *
     * @return \Symfony\Component\DependencyInjection\Container
     */
    private function getServiceContainer($params)
    {
        $params['omnipay.log.format'] = MessageFormatter::DEBUG_FORMAT;
        $parameterBag = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');

        $parameterBag
            ->expects($this->any())
            ->method('all')
            ->will($this->returnValue($params));

        $serviceContainer = $this->getMock('Symfony\Component\DependencyInjection\Container');

        $serviceContainer
            ->expects($this->once())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        return $serviceContainer;
    }

    public function testCreateAuthorizeNetAIM()
    {
        // Do not run test if Gateway has not been included
        if (!class_exists('Omnipay\\AuthorizeNet\\AIMGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\AuthorizeNet\\AIMGateway not found');

            return;
        }

        $config = array(
            'omnipay.authorize_net_aim.apiLoginId' => 'abc123',
            'omnipay.authorize_net_aim.transactionKey' => 'xyz987',
            'omnipay.authorize_net_aim.gateway' => 'AuthorizeNet_AIM'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\AuthorizeNet\AIMGateway $gateway */
        $gateway = $service->get('authorize_net_aim');

        $this->assertInstanceOf(
            'Omnipay\\AuthorizeNet\\AIMGateway',
            $gateway,
            'Must return an Authorize.NET AIM gateway'
        );
        $this->assertEquals('abc123', $gateway->getApiLoginId(), 'API login ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getTransactionKey(), 'Transaction key must match configuration');
    }

    public function testCreateAuthorizeNetSIM()
    {
        if (!class_exists('Omnipay\\AuthorizeNet\\SIMGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\AuthorizeNet\\SIMGateway not found');

            return;
        }

        $config = array(
            'omnipay.authorize_net_sim.apiLoginId' => 'abc123',
            'omnipay.authorize_net_sim.transactionKey' => 'xyz987',
            'omnipay.authorize_net_sim.gateway' => 'AuthorizeNet_SIM',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\AuthorizeNet\AIMGateway $gateway */
        $gateway = $service->get('authorize_net_sim');

        $this->assertInstanceOf(
            'Omnipay\\AuthorizeNet\\SIMGateway',
            $gateway,
            'Must return an Authorize.NET SIM gateway'
        );
        $this->assertEquals('abc123', $gateway->getApiLoginId(), 'API login ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getTransactionKey(), 'Transaction key must match configuration');
    }

    public function testCreateBuckaroo()
    {
        if (!class_exists('Omnipay\\Buckaroo\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Buckaroo\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.buckaroo.merchantId' => 'abc123',
            'omnipay.buckaroo.secret' => 'xyz987',
            'omnipay.buckaroo.gateway' => 'Buckaroo',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Buckaroo\Gateway $gateway */
        $gateway = $service->get('buckaroo');

        $this->assertInstanceOf('Omnipay\\Buckaroo\\Gateway', $gateway, 'Must return a Buckaroo gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getSecret(), 'Secret must match configuration');
    }

    public function testCreateCardSave()
    {
        if (!class_exists('Omnipay\\CardSave\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\CardSave\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.card_save.merchantId' => 'abc123',
            'omnipay.card_save.password' => 'xyz987',
            'omnipay.card_save.gateway' => 'CardSave',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\CardSave\Gateway $gateway */
        $gateway = $service->get('card_save');

        $this->assertInstanceOf('Omnipay\\CardSave\\Gateway', $gateway, 'Must return a CardSave gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreateEwayRapid()
    {
        if (!class_exists('Omnipay\\Eway\\RapidGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Eway\\RapidGateway not found');

            return;
        }

        $config = array(
            'omnipay.eway_rapid.apiKey' => 'abc123',
            'omnipay.eway_rapid.password' => 'xyz987',
            'omnipay.eway_rapid.gateway' => 'Eway_Rapid',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Eway\RapidGateway $gateway */
        $gateway = $service->get('eway_rapid');

        $this->assertInstanceOf('Omnipay\\Eway\\RapidGateway', $gateway, 'Must return an eWAY Rapid gateway');
        $this->assertEquals('abc123', $gateway->getApiKey(), 'API key must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreateGoCardless()
    {
        if (!class_exists('Omnipay\\GoCardless\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\GoCardless\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.go_cardless.appId' => 'abc123',
            'omnipay.go_cardless.appSecret' => 'xyz987',
            'omnipay.go_cardless.merchantId' => 'pqr567',
            'omnipay.go_cardless.accessToken' => 'uvw543',
            'omnipay.go_cardless.gateway' => 'GoCardless',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\GoCardless\Gateway $gateway */
        $gateway = $service->get('go_cardless');

        $this->assertInstanceOf('Omnipay\\GoCardless\\Gateway', $gateway, 'Must return a GoCardless gateway');
        $this->assertEquals('abc123', $gateway->getAppId(), 'App ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getAppSecret(), 'App secret must match configuration');
        $this->assertEquals('pqr567', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals('uvw543', $gateway->getAccessToken(), 'Access token must match configuration');
    }

    public function testCreateMigsTwoParty()
    {
        if (!class_exists('Omnipay\\Migs\\TwoPartyGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Migs\\TwoPartyGateway not found');

            return;
        }

        $config = array(
            'omnipay.migs_two_party.merchantId' => 'abc123',
            'omnipay.migs_two_party.merchantAccessCode' => 'xyz987',
            'omnipay.migs_two_party.secureHash' => 'pqr567',
            'omnipay.migs_two_party.gateway' => 'Migs_TwoParty',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Migs\TwoPartyGateway $gateway */
        $gateway = $service->get('migs_two_party');

        $this->assertInstanceOf('Omnipay\\Migs\\TwoPartyGateway', $gateway, 'Must return a MIGS 2-Party gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals(
            'xyz987',
            $gateway->getMerchantAccessCode(),
            'Merchant access code must match configuration'
        );
        $this->assertEquals('pqr567', $gateway->getSecureHash(), 'Secure hash must match configuration');
    }

    public function testCreateMigsThreeParty()
    {
        if (!class_exists('Omnipay\\Migs\\ThreePartyGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Migs\\ThreePartyGateway not found');

            return;
        }

        $config = array(
            'omnipay.migs_three_party.merchantId' => 'abc123',
            'omnipay.migs_three_party.merchantAccessCode' => 'xyz987',
            'omnipay.migs_three_party.secureHash' => 'pqr567',
            'omnipay.migs_three_party.gateway' => 'Migs_ThreeParty',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Migs\ThreePartyGateway $gateway */
        $gateway = $service->get('migs_three_party');

        $this->assertInstanceOf('Omnipay\\Migs\\ThreePartyGateway', $gateway, 'Must return a MIGS 3-Party gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals(
            'xyz987',
            $gateway->getMerchantAccessCode(),
            'Merchant access code must match configuration'
        );
        $this->assertEquals('pqr567', $gateway->getSecureHash(), 'Secure hash must match configuration');
    }

    public function testCreateMollie()
    {
        if (!class_exists('Omnipay\\Mollie\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Mollie\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.mollie.apiKey' => 'fooBar',
            'omnipay.mollie.gateway' => 'Mollie',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Mollie\Gateway $gateway */
        $gateway = $service->get('mollie');

        $this->assertInstanceOf('Omnipay\\Mollie\\Gateway', $gateway, 'Must return a Mollie gateway');
        $this->assertEquals('fooBar', $gateway->getApiKey(), 'apiKey must match configuration');
    }

    public function testCreateMultiSafepay()
    {
        if (!class_exists('Omnipay\\MultiSafepay\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\MultiSafepay\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.multi_safepay.accountId' => 'abc123',
            'omnipay.multi_safepay.siteId' => 'xyz987',
            'omnipay.multi_safepay.siteCode' => 'pqr567',
            'omnipay.multi_safepay.gateway' => 'MultiSafepay',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\MultiSafepay\Gateway $gateway */
        $gateway = $service->get('multi_safepay');

        $this->assertInstanceOf('Omnipay\\MultiSafepay\\Gateway', $gateway, 'Must return a MultiSafepay gateway');
        $this->assertEquals('abc123', $gateway->getAccountId(), 'Account ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getSiteId(), 'Site ID must match configuration');
        $this->assertEquals('pqr567', $gateway->getSiteCode(), 'Site code must match configuration');
    }

    public function testCreateNetaxept()
    {
        if (!class_exists('Omnipay\\Netaxept\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Netaxept\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.netaxept.merchantId' => 'abc123',
            'omnipay.netaxept.password' => 'xyz987',
            'omnipay.netaxept.gateway' => 'Netaxept',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Netaxept\Gateway $gateway */
        $gateway = $service->get('netaxept');

        $this->assertInstanceOf('Omnipay\\Netaxept\\Gateway', $gateway, 'Must return a Netaxept gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreateNetBanx()
    {
        if (!class_exists('Omnipay\\NetBanx\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\NetBanx\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.net_banx.accountNumber' => 'abc123',
            'omnipay.net_banx.storeId' => 'xyz987',
            'omnipay.net_banx.storePassword' => 'pqr567',
            'omnipay.net_banx.gateway' => 'NetBanx',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\NetBanx\Gateway $gateway */
        $gateway = $service->get('net_banx');

        $this->assertInstanceOf('Omnipay\\NetBanx\\Gateway', $gateway, 'Must return a NetBanx gateway');
        $this->assertEquals('abc123', $gateway->getAccountNumber(), 'Account number must match configuration');
        $this->assertEquals('xyz987', $gateway->getStoreId(), 'Store ID must match configuration');
        $this->assertEquals('pqr567', $gateway->getStorePassword(), 'Store password must match configuration');
    }

    public function testCreatePayFast()
    {
        if (!class_exists('Omnipay\\PayFast\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\PayFast\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.pay_fast.merchantId' => 'abc123',
            'omnipay.pay_fast.merchantKey' => 'xyz987',
            'omnipay.pay_fast.pdtKey' => 'pqr567',
            'omnipay.pay_fast.gateway' => 'PayFast',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\PayFast\Gateway $gateway */
        $gateway = $service->get('pay_fast');

        $this->assertInstanceOf('Omnipay\\PayFast\\Gateway', $gateway, 'Must return a PayFast gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getMerchantKey(), 'Merchant key must match configuration');
        $this->assertEquals('pqr567', $gateway->getPdtKey(), 'PDT key must match configuration');
    }

    public function testCreatePayflow()
    {
        if (!class_exists('Omnipay\\Payflow\\ProGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Payflow\\ProGateway not found');

            return;
        }

        $config = array(
            'omnipay.payflow_pro.username' => 'abc123',
            'omnipay.payflow_pro.password' => 'xyz987',
            'omnipay.payflow_pro.vendor' => 'pqr567',
            'omnipay.payflow_pro.partner' => 'uvw543',
            'omnipay.payflow_pro.gateway' => 'Payflow_Pro',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Payflow\ProGateway $gateway */
        $gateway = $service->get('payflow_pro');

        $this->assertInstanceOf('Omnipay\\Payflow\\ProGateway', $gateway, 'Must return a Payflow Pro gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
        $this->assertEquals('pqr567', $gateway->getVendor(), 'Vendor must match configuration');
        $this->assertEquals('uvw543', $gateway->getPartner(), 'Partner must match configuration');
    }

    public function testCreatePaymentExpressPxPay()
    {
        if (!class_exists('Omnipay\\PaymentExpress\\PxPayGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\PaymentExpress\\PxPayGateway not found');

            return;
        }

        $config = array(
            'omnipay.payment_express_px_pay.username' => 'abc123',
            'omnipay.payment_express_px_pay.password' => 'xyz987',
            'omnipay.payment_express_px_pay.gateway' => 'PaymentExpress_PxPay',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\PaymentExpress\PxPayGateway $gateway */
        $gateway = $service->get('payment_express_px_pay');

        $this->assertInstanceOf(
            'Omnipay\\PaymentExpress\\PxPayGateway',
            $gateway,
            'Must return a PaymentExpress PxPay gateway'
        );
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreatePaymentExpressPxPost()
    {
        if (!class_exists('Omnipay\\PaymentExpress\\PxPostGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\PaymentExpress\\PxPostGateway not found');

            return;
        }

        $config = array(
            'omnipay.payment_express_px_post.username' => 'abc123',
            'omnipay.payment_express_px_post.password' => 'xyz987',
            'omnipay.payment_express_px_post.gateway' => 'PaymentExpress_PxPost',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\PaymentExpress\PxPostGateway $gateway */
        $gateway = $service->get('payment_express_px_post');

        $this->assertInstanceOf(
            'Omnipay\\PaymentExpress\\PxPostGateway',
            $gateway,
            'Must return a PaymentExpress PxPost gateway'
        );
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreatePayPalPro()
    {
        if (!class_exists('Omnipay\\PayPal\\ProGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\PayPal\\ProGateway not found');

            return;
        }

        $config = array(
            'omnipay.pay_pal_pro.username' => 'abc123',
            'omnipay.pay_pal_pro.password' => 'xyz987',
            'omnipay.pay_pal_pro.signature' => 'pqr567',
            'omnipay.pay_pal_pro.gateway' => 'PayPal_Pro',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\PayPal\ProGateway $gateway */
        $gateway = $service->get('pay_pal_pro');

        $this->assertInstanceOf('Omnipay\\PayPal\\ProGateway', $gateway, 'Must return a PayPal Pro gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
        $this->assertEquals('pqr567', $gateway->getSignature(), 'Signature must match configuration');
    }

    public function testCreatePayPalExpress()
    {
        if (!class_exists('Omnipay\\PayPal\\ExpressGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\PayPal\\ExpressGateway not found');

            return;
        }

        $config = array(
            'omnipay.pay_pal_express.username' => 'abc123',
            'omnipay.pay_pal_express.password' => 'xyz987',
            'omnipay.pay_pal_express.signature' => 'pqr567',
            'omnipay.pay_pal_express.solutionType' => array('foo', 'bar'),
            'omnipay.pay_pal_express.landingPage' => array('baz'),
            'omnipay.pay_pal_express.headerImageUrl' => 'uvw543',
            'omnipay.pay_pal_express.gateway' => 'PayPal_Express',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\PayPal\ExpressGateway $gateway */
        $gateway = $service->get('pay_pal_express');

        $this->assertInstanceOf('Omnipay\\PayPal\\ExpressGateway', $gateway, 'Must return a PayPal Express gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
        $this->assertEquals('pqr567', $gateway->getSignature(), 'Signature must match configuration');
        $this->assertEquals(array('foo', 'bar'), $gateway->getSolutionType(), 'Solution type must match configuration');
        $this->assertEquals(array('baz'), $gateway->getLandingPage(), 'Landing page must match configuration');
        $this->assertEquals('uvw543', $gateway->getHeaderImageUrl(), 'Header image URL must match configuration');
    }

    public function testCreatePin()
    {
        if (!class_exists('Omnipay\\Pin\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Pin\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.pin.secretKey' => 'abc123',
            'omnipay.pin.gateway' => 'Pin'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Pin\Gateway $gateway */
        $gateway = $service->get('pin');

        $this->assertInstanceOf('Omnipay\\Pin\\Gateway', $gateway, 'Must return a Pin gateway');
        $this->assertEquals('abc123', $gateway->getSecretKey(), 'API key must match configuration');
    }

    public function testCreateSagePayDirect()
    {
        if (!class_exists('Omnipay\\SagePay\\DirectGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\SagePay\\DirectGateway not found');

            return;
        }

        $config = array(
            'omnipay.sage_pay_direct.vendor' => 'abc123',
            'omnipay.sage_pay_direct.gateway' => 'SagePay_Direct'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\SagePay\DirectGateway $gateway */
        $gateway = $service->get('sage_pay_direct');

        $this->assertInstanceOf('Omnipay\\SagePay\\DirectGateway', $gateway, 'Must return a SagePay Direct gateway');
        $this->assertEquals('abc123', $gateway->getVendor(), 'Vendor must match configuration');
    }

    public function testCreateSagePayServer()
    {
        if (!class_exists('Omnipay\\SagePay\\ServerGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\SagePay\\ServerGateway not found');

            return;
        }

        $config = array(
            'omnipay.sage_pay_server.vendor' => 'abc123',
            'omnipay.sage_pay_server.gateway' => 'SagePay_Server',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\SagePay\ServerGateway $gateway */
        $gateway = $service->get('sage_pay_server');

        $this->assertInstanceOf('Omnipay\\SagePay\\ServerGateway', $gateway, 'Must return a SagePay Server gateway');
        $this->assertEquals('abc123', $gateway->getVendor(), 'Vendor must match configuration');
    }

    public function testCreateSecurePayDirectPost()
    {
        if (!class_exists('Omnipay\\SecurePay\\DirectPostGateway')) {
            $this->markTestSkipped('Gateway Omnipay\\SecurePay\\DirectPostGateway not found');

            return;
        }

        $config = array(
            'omnipay.secure_pay_direct_post.merchantId' => 'abc123',
            'omnipay.secure_pay_direct_post.transactionPassword' => 'xyz987',
            'omnipay.secure_pay_direct_post.gateway' => 'SecurePay_DirectPost',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\SecurePay\DirectPostGateway $gateway */
        $gateway = $service->get('secure_pay_direct_post');

        $this->assertInstanceOf(
            'Omnipay\\SecurePay\\DirectPostGateway',
            $gateway,
            'Must return a SecurePay Direct Post gateway'
        );
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals(
            'xyz987',
            $gateway->getTransactionPassword(),
            'Transaction password must match configuration'
        );
    }

    public function testCreateStripe()
    {
        if (!class_exists('Omnipay\\Stripe\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\Stripe\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.stripe.apiKey' => 'abc123',
            'omnipay.stripe.gateway' => 'Stripe'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\Stripe\Gateway $gateway */
        $gateway = $service->get('stripe');

        $this->assertInstanceOf('Omnipay\\Stripe\\Gateway', $gateway, 'Must return a Stripe gateway');
        $this->assertEquals('abc123', $gateway->getApiKey(), 'API key must match configuration');
    }

    public function testCreateTwoCheckout()
    {
        if (!class_exists('Omnipay\\TwoCheckout\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\TwoCheckout\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.two_checkout.accountNumber' => 'abc123',
            'omnipay.two_checkout.secretWord' => 'xyz987',
            'omnipay.two_checkout.gateway' => 'TwoCheckout',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\TwoCheckout\Gateway $gateway */
        $gateway = $service->get('two_checkout');

        $this->assertInstanceOf('Omnipay\\TwoCheckout\\Gateway', $gateway, 'Must return a TwoCheckout gateway');
        $this->assertEquals('abc123', $gateway->getAccountNumber(), 'Account number must match configuration');
        $this->assertEquals('xyz987', $gateway->getSecretWord(), 'Secret word must match configuration');
    }

    public function testCreateWorldPay()
    {
        if (!class_exists('Omnipay\\WorldPay\\Gateway')) {
            $this->markTestSkipped('Gateway Omnipay\\WorldPay\\Gateway not found');

            return;
        }

        $config = array(
            'omnipay.world_pay.installationId' => 'abc123',
            'omnipay.world_pay.secretWord' => 'xyz987',
            'omnipay.world_pay.callbackPassword' => 'pqr567',
            'omnipay.world_pay.gateway' => 'WorldPay'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var \Omnipay\WorldPay\Gateway $gateway */
        $gateway = $service->get('world_pay');

        $this->assertInstanceOf('Omnipay\\WorldPay\\Gateway', $gateway, 'Must return a WorldPay gateway');
        $this->assertEquals('abc123', $gateway->getInstallationId(), 'Installation ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getSecretWord(), 'Secret word must match configuration');
        $this->assertEquals('pqr567', $gateway->getCallbackPassword(), 'Callback password must match configuration');
    }

    public function testGetConfig()
    {
        $config = array(
            'omnipay.world_pay.installationId' => 'abc123',
            'omnipay.world_pay.secretWord' => 'xyz987',
            'omnipay.world_pay.callbackPassword' => 'pqr567',
        );
        $serviceContainer = $this->getServiceContainer($config);

        $service = $this->buildService(array('container' => $serviceContainer));

        $expected = array(
            'log' => array(
                'format' => MessageFormatter::DEBUG_FORMAT
            ),
            'world_pay' => array(
                'installationId' => 'abc123',
                'secretWord' => 'xyz987',
                'callbackPassword' => 'pqr567',
            )
        );

        $this->assertEquals($expected, $service->getConfig(), 'Array structured config should match');
    }

    public function testGetGatewayName()
    {
        $config = array(
            'omnipay.stripe_canada.gateway' => 'Stripe',
        );
        $serviceContainer = $this->getServiceContainer($config);
        $service = $this->buildService(array('container' => $serviceContainer));
        $this->assertEquals(
            'Stripe',
            $service->getGatewayName('stripe_canada'),
            'The configured gateway name should return'
        );
        $this->assertNull($service->getGatewayName('stripe_uk'), 'Invalid gateway key should return null');
    }

    public function testGetDefault()
    {
        $config = array(
            'omnipay.default' => 'my_gateway',
            'omnipay.my_gateway.gateway' => 'Stripe',
            'omnipay.my_gateway.apiKey' => 'abc123'
        );
        $serviceContainer = $this->getServiceContainer($config);
        $service = $this->buildService(array('container' => $serviceContainer));

        /** @var StripeGateway $gateway */
        $gateway = $service->get();
        $this->assertInstanceOf('Omnipay\\Stripe\\Gateway', $gateway, 'The default gateway should return');
        $this->assertEquals('abc123', $gateway->getApiKey(), 'API key should be set');
    }

    public function testGetDefaultWithKey()
    {
        $config = array(
            'omnipay.default' => 'my_gateway',
            'omnipay.my_gateway.gateway' => 'Stripe',
            'omnipay.my_gateway.apiKey' => 'abc123',
            // Another Stripe gateway which does not have the apiKey
            'omnipay.another_gateway.gateway' => 'Stripe'
        );
        $serviceContainer = $this->getServiceContainer($config);
        $service = $this->buildService(array('container' => $serviceContainer));

        /** @var StripeGateway $gateway */
        $gateway = $service->get('another_gateway');
        $this->assertInstanceOf('Omnipay\\Stripe\\Gateway', $gateway, 'The default gateway should return');
        $this->assertEquals('abc123', $gateway->getApiKey(), 'API key should be take from the gateway with the same name');
    }

    public function testShouldMergeDefaultParametersDefinedForTheGateway()
    {
        $config = array(
            'omnipay.my_gateway.gateway' => 'Stripe',
            'omnipay.defaults.stripe.apiKey' => 'xyz123'
        );
        $serviceContainer = $this->getServiceContainer($config);
        $service = $this->buildService(array('container' => $serviceContainer));

        /** @var StripeGateway $gateway */
        $gateway = $service->get('my_gateway');
        $this->assertInstanceOf('Omnipay\\Stripe\\Gateway', $gateway, 'The default gateway should return');
        $this->assertEquals('xyz123', $gateway->getApiKey());
    }

    public function testSetConfig()
    {
        $config = array(
            'omnipay.default' => 'my_gateway',
            'omnipay.my_gateway.gateway' => 'Stripe',
            'omnipay.my_gateway.apiKey' => 'abc123'
        );
        $serviceContainer = $this->getServiceContainer($config);
        $service = $this->buildService(array('container' => $serviceContainer));

        $service->setConfig('my_gateway', array('apiKey' => 'xyz789'));
        $serviceConfig = $service->getConfig();
        $this->assertEquals('xyz789', $serviceConfig['my_gateway']['apiKey'], 'API key should be updated');
    }

    public function testDefaultBundleConfig()
    {
        $containerBuilder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $containerBuilder
            ->expects($this->at(0)) // Values get set before YAML config is loaded
            ->method('setParameter')
            ->with('omnipay.log.format', MessageFormatter::DEBUG_FORMAT);

        $extension = new OmnipayExtension();
        $extension->load(array(), $containerBuilder);
    }

    public function testSetBundleConfig()
    {
        $config = array(
            array(
                'log' => array(
                    'format' => 'abc123'
                )
            )
        );

        $containerBuilder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $containerBuilder
            ->expects($this->at(0)) // Values get set before YAML config is loaded
            ->method('setParameter')
            ->with('omnipay.log.format', 'abc123');

        $extension = new OmnipayExtension();
        $extension->load($config, $containerBuilder);
    }
}
