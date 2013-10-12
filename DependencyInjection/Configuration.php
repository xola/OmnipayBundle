<?php

namespace Xola\OmnipayBundle\DependencyInjection;

use Omnipay\Common\GatewayFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('omnipay');

        $root
            ->children()

                // Authorize.NET AIM
                ->arrayNode('authorize_net_aim')
                    ->children()
                        ->scalarNode('apiLoginId')->isRequired()->end()
                        ->scalarNode('transactionKey')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                        ->scalarNode('developerMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Authorize.NET SIM
                ->arrayNode('authorize_net_sim')
                    ->children()
                        ->scalarNode('apiLoginId')->isRequired()->end()
                        ->scalarNode('transactionKey')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                        ->scalarNode('developerMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Buckaroo
                ->arrayNode('buckaroo')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('secret')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // CardSave
                ->arrayNode('card_save')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()

                // eWAY Rapid 3.0
                ->arrayNode('eway_rapid')
                    ->children()
                        ->scalarNode('apiKey')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // GoCardless
                ->arrayNode('go_cardless')
                    ->children()
                        ->scalarNode('appId')->isRequired()->end()
                        ->scalarNode('appSecret')->isRequired()->end()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('accessToken')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // MIGS 2-Party
                ->arrayNode('migs_two_party')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('merchantAccessCode')->isRequired()->end()
                        ->scalarNode('secureHash')->end()
                    ->end()
                ->end()

                // MIGS 3-Party
                ->arrayNode('migs_three_party')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('merchantAccessCode')->isRequired()->end()
                        ->scalarNode('secureHash')->end()
                    ->end()
                ->end()

                // Mollie
                ->arrayNode('mollie')
                    ->children()
                        ->scalarNode('partnerId')->isRequired()->end()
                        ->scalarNode('testMode')->end()
                    ->end()
                ->end()

                // MultiSafepay
                ->arrayNode('multi_safepay')
                    ->children()
                        ->scalarNode('accountId')->isRequired()->end()
                        ->scalarNode('siteId')->isRequired()->end()
                        ->scalarNode('siteCode')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Netaxept
                ->arrayNode('netaxept')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // NetBanx
                ->arrayNode('net_banx')
                    ->children()
                        ->scalarNode('accountNumber')->isRequired()->end()
                        ->scalarNode('storeId')->isRequired()->end()
                        ->scalarNode('storePassword')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // PayFast
                ->arrayNode('pay_fast')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('merchantKey')->isRequired()->end()
                        ->scalarNode('pdtKey')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Payflow Pro
                ->arrayNode('payflow_pro')
                    ->children()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('vendor')->end()
                        ->scalarNode('partner')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // PaymentExpress PxPay
                ->arrayNode('payment_express_px_pay')
                    ->children()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()

                // PaymentExpress PxPost
                ->arrayNode('payment_express_px_post')
                    ->children()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                    ->end()
                ->end()

                // PayPal Pro
                ->arrayNode('pay_pal_pro')
                    ->children()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('signature')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // PayPal Express
                ->arrayNode('pay_pal_express')
                    ->children()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('signature')->end()
                        ->scalarNode('solutionType')->defaultValue(array('Sole', 'Mark'))->end()
                        ->scalarNode('landingPage')->defaultValue(array('Billing', 'Login'))->end()
                        ->scalarNode('headerImageUrl')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Pin
                ->arrayNode('pin')
                    ->children()
                        ->scalarNode('secretKey')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Sage Pay Direct
                ->arrayNode('sage_pay_direct')
                    ->children()
                        ->scalarNode('vendor')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                        ->scalarNode('simulatorMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Sage Pay Server
                ->arrayNode('sage_pay_server')
                    ->children()
                        ->scalarNode('vendor')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                        ->scalarNode('simulatorMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // SecurePay Direct Post
                ->arrayNode('secure_pay_direct_post')
                    ->children()
                        ->scalarNode('merchantId')->isRequired()->end()
                        ->scalarNode('transactionPassword')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // Stripe
                ->arrayNode('stripe')
                    ->children()
                        ->scalarNode('apiKey')->isRequired()->end()
                    ->end()
                ->end()

                // 2Checkout
                ->arrayNode('two_checkout')
                    ->children()
                        ->scalarNode('accountNumber')->isRequired()->end()
                        ->scalarNode('secretWord')->isRequired()->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

                // WorldPay
                ->arrayNode('world_pay')
                    ->children()
                        ->scalarNode('installationId')->isRequired()->end()
                        ->scalarNode('secretWord')->isRequired()->end()
                        ->scalarNode('callbackPassword')->end()
                        ->scalarNode('testMode')->defaultValue(false)->end()
                    ->end()
                ->end()

            ->end();

        return $builder;
    }
}
