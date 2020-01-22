<?php

namespace Xola\OmnipayBundle\Tests\Service;

use Omnipay\AuthorizeNet\AIMGateway;
use Omnipay\AuthorizeNet\SIMGateway;
use Omnipay\Eway\RapidGateway;
use Omnipay\Mollie\Gateway as MollieGateway;
use Omnipay\PaymentExpress\PxPayGateway;
use Omnipay\PaymentExpress\PxPostGateway;
use Omnipay\PayPal\ExpressGateway;
use Omnipay\PayPal\ProGateway;
use Omnipay\SagePay\DirectGateway as SagePayDirectGateway;
use Omnipay\SagePay\ServerGateway as SagePayServerGateway;
use Omnipay\SecurePay\DirectPostGateway as SecurePayDirectPostGateway;
use Omnipay\Stripe\Gateway as StripeGateway;
use Omnipay\WorldPay\Gateway as WorldPayGateway;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Xola\OmnipayBundle\DependencyInjection\Configuration;
use Xola\OmnipayBundle\DependencyInjection\OmnipayExtension;
use Xola\OmnipayBundle\Service\Omnipay;

class OmnipayTest extends TestCase
{
    private function buildService($params = array())
    {
        $defaults = array(
            'container' => $this->createMock(Container::class),
            'logger' => $this->createMock(LoggerInterface::class)
        );

        $params = array_merge($defaults, $params);

        return new Omnipay($params['container'], $params['logger']);
    }

    /**
     * @param array $params
     *
     * @return Container
     */
    private function getServiceContainer($params)
    {
        $params['omnipay.log.format'] = Configuration::DEBUG_FORMAT;
        $parameterBag = $this->createMock(ParameterBagInterface::class);

        $parameterBag
            ->expects($this->any())
            ->method('all')
            ->will($this->returnValue($params));

        $serviceContainer = $this->createMock(Container::class);

        $serviceContainer
            ->expects($this->once())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        return $serviceContainer;
    }

    public function testCreateAuthorizeNetAIM()
    {
        $config = array(
            'omnipay.authorize_net_aim.apiLoginId' => 'abc123',
            'omnipay.authorize_net_aim.transactionKey' => 'xyz987',
            'omnipay.authorize_net_aim.gateway' => 'AuthorizeNet_AIM'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var AIMGateway $gateway */
        $gateway = $service->get('authorize_net_aim');

        $this->assertInstanceOf(AIMGateway::class, $gateway, 'Must return an Authorize.NET AIM gateway');
        $this->assertEquals('abc123', $gateway->getApiLoginId(), 'API login ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getTransactionKey(), 'Transaction key must match configuration');
    }

    public function testCreateAuthorizeNetSIM()
    {
        $config = array(
            'omnipay.authorize_net_sim.apiLoginId' => 'abc123',
            'omnipay.authorize_net_sim.transactionKey' => 'xyz987',
            'omnipay.authorize_net_sim.gateway' => 'AuthorizeNet_SIM',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var AIMGateway $gateway */
        $gateway = $service->get('authorize_net_sim');

        $this->assertInstanceOf(SIMGateway::class, $gateway, 'Must return an Authorize.NET SIM gateway');
        $this->assertEquals('abc123', $gateway->getApiLoginId(), 'API login ID must match configuration');
        $this->assertEquals('xyz987', $gateway->getTransactionKey(), 'Transaction key must match configuration');
    }

    public function testCreateEwayRapid()
    {
        $config = array(
            'omnipay.eway_rapid.apiKey' => 'abc123',
            'omnipay.eway_rapid.password' => 'xyz987',
            'omnipay.eway_rapid.gateway' => 'Eway_Rapid',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var RapidGateway $gateway */
        $gateway = $service->get('eway_rapid');

        $this->assertInstanceOf(RapidGateway::class, $gateway, 'Must return an eWAY Rapid gateway');
        $this->assertEquals('abc123', $gateway->getApiKey(), 'API key must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreateMollie()
    {
        $config = array(
            'omnipay.mollie.apiKey' => 'fooBar',
            'omnipay.mollie.gateway' => 'Mollie',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var MollieGateway $gateway */
        $gateway = $service->get('mollie');

        $this->assertInstanceOf(MollieGateway::class, $gateway, 'Must return a Mollie gateway');
        $this->assertEquals('fooBar', $gateway->getApiKey(), 'apiKey must match configuration');
    }

    public function testCreatePaymentExpressPxPay()
    {
        $config = array(
            'omnipay.payment_express_px_pay.username' => 'abc123',
            'omnipay.payment_express_px_pay.password' => 'xyz987',
            'omnipay.payment_express_px_pay.gateway' => 'PaymentExpress_PxPay',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var PxPayGateway $gateway */
        $gateway = $service->get('payment_express_px_pay');

        $this->assertInstanceOf(PxPayGateway::class, $gateway, 'Must return a PaymentExpress PxPay gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreatePaymentExpressPxPost()
    {
        $config = array(
            'omnipay.payment_express_px_post.username' => 'abc123',
            'omnipay.payment_express_px_post.password' => 'xyz987',
            'omnipay.payment_express_px_post.gateway' => 'PaymentExpress_PxPost',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var PxPostGateway $gateway */
        $gateway = $service->get('payment_express_px_post');

        $this->assertInstanceOf(PxPostGateway::class, $gateway, 'Must return a PaymentExpress PxPost gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
    }

    public function testCreatePayPalPro()
    {
        $config = array(
            'omnipay.pay_pal_pro.username' => 'abc123',
            'omnipay.pay_pal_pro.password' => 'xyz987',
            'omnipay.pay_pal_pro.signature' => 'pqr567',
            'omnipay.pay_pal_pro.gateway' => 'PayPal_Pro',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var ProGateway $gateway */
        $gateway = $service->get('pay_pal_pro');

        $this->assertInstanceOf(ProGateway::class, $gateway, 'Must return a PayPal Pro gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
        $this->assertEquals('pqr567', $gateway->getSignature(), 'Signature must match configuration');
    }

    public function testCreatePayPalExpress()
    {
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

        /** @var ExpressGateway $gateway */
        $gateway = $service->get('pay_pal_express');

        $this->assertInstanceOf(ExpressGateway::class, $gateway, 'Must return a PayPal Express gateway');
        $this->assertEquals('abc123', $gateway->getUsername(), 'Username must match configuration');
        $this->assertEquals('xyz987', $gateway->getPassword(), 'Password must match configuration');
        $this->assertEquals('pqr567', $gateway->getSignature(), 'Signature must match configuration');
        $this->assertEquals(array('foo', 'bar'), $gateway->getSolutionType(), 'Solution type must match configuration');
        $this->assertEquals(array('baz'), $gateway->getLandingPage(), 'Landing page must match configuration');
        $this->assertEquals('uvw543', $gateway->getHeaderImageUrl(), 'Header image URL must match configuration');
    }

    public function testCreateSagePayDirect()
    {
        $config = array(
            'omnipay.sage_pay_direct.vendor' => 'abc123',
            'omnipay.sage_pay_direct.gateway' => 'SagePay_Direct'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var SagePayDirectGateway $gateway */
        $gateway = $service->get('sage_pay_direct');

        $this->assertInstanceOf(SagePayDirectGateway::class, $gateway, 'Must return a SagePay Direct gateway');
        $this->assertEquals('abc123', $gateway->getVendor(), 'Vendor must match configuration');
    }

    public function testCreateSagePayServer()
    {
        $config = array(
            'omnipay.sage_pay_server.vendor' => 'abc123',
            'omnipay.sage_pay_server.gateway' => 'SagePay_Server',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var SagePayServerGateway $gateway */
        $gateway = $service->get('sage_pay_server');

        $this->assertInstanceOf(SagePayServerGateway::class, $gateway, 'Must return a SagePay Server gateway');
        $this->assertEquals('abc123', $gateway->getVendor(), 'Vendor must match configuration');
    }

    public function testCreateSecurePayDirectPost()
    {
        $config = array(
            'omnipay.secure_pay_direct_post.merchantId' => 'abc123',
            'omnipay.secure_pay_direct_post.transactionPassword' => 'xyz987',
            'omnipay.secure_pay_direct_post.gateway' => 'SecurePay_DirectPost',
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var SecurePayDirectPostGateway $gateway */
        $gateway = $service->get('secure_pay_direct_post');

        $this->assertInstanceOf(SecurePayDirectPostGateway::class, $gateway, 'Must return a SecurePay Direct Post gateway');
        $this->assertEquals('abc123', $gateway->getMerchantId(), 'Merchant ID must match configuration');
        $this->assertEquals(
            'xyz987',
            $gateway->getTransactionPassword(),
            'Transaction password must match configuration'
        );
    }

    public function testCreateStripe()
    {
        $config = array(
            'omnipay.stripe.apiKey' => 'abc123',
            'omnipay.stripe.gateway' => 'Stripe'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var StripeGateway $gateway */
        $gateway = $service->get('stripe');

        $this->assertInstanceOf(StripeGateway::class, $gateway, 'Must return a Stripe gateway');
        $this->assertEquals('abc123', $gateway->getApiKey(), 'API key must match configuration');
    }

    public function testCreateWorldPay()
    {
        $config = array(
            'omnipay.world_pay.installationId' => 'abc123',
            'omnipay.world_pay.secretWord' => 'xyz987',
            'omnipay.world_pay.callbackPassword' => 'pqr567',
            'omnipay.world_pay.gateway' => 'WorldPay'
        );

        $service = $this->buildService(array('container' => $this->getServiceContainer($config)));

        /** @var WorldPayGateway $gateway */
        $gateway = $service->get('world_pay');

        $this->assertInstanceOf(WorldPayGateway::class, $gateway, 'Must return a WorldPay gateway');
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
                'format' => Configuration::DEBUG_FORMAT
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
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('setParameter'))
            ->getMock();

        $containerBuilder
            ->expects($this->at(0)) // Values get set before YAML config is loaded
            ->method('setParameter')
            ->with('omnipay.log.format', Configuration::DEBUG_FORMAT);

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

        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('setParameter'))
            ->getMock();

        $containerBuilder
            ->expects($this->at(0)) // Values get set before YAML config is loaded
            ->method('setParameter')
            ->with('omnipay.log.format', 'abc123');

        $extension = new OmnipayExtension();
        $extension->load($config, $containerBuilder);
    }
}
