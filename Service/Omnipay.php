<?php

namespace Xola\OmnipayBundle\Service;

use Symfony\Component\DependencyInjection\Container as Helper;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\GatewayFactory;

class Omnipay
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function create($class)
    {
        /** @var GatewayInterface $gateway */
        $gateway = GatewayFactory::create($class);

        // Normalize the name to be underscored
        $name = $gateway->getShortName();
        $name = Helper::underscore($name);
        $name = str_replace('.', '_', $name);

        // Initialize the gateway with config parameters
        if(isset($this->config[$name])) {
            $gateway->initialize($this->config[$name]);
        }

        return $gateway;
    }
}
