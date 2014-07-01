<?php

namespace Xola\OmnipayBundle\Service;

use Symfony\Component\DependencyInjection\Container as Helper;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\AbstractGateway;

class Omnipay
{
    protected $config;
    protected $cache = array();

    public function __construct(Helper $container)
    {
        $this->parameters = $container->getParameterBag()->all();
    }

    /**
     * Returns an Omnipay gateway.
     *
     * @param string $name Name of the gateway as defined in the config
     *
     * @return AbstractGateway
     */
    public function get($name)
    {
        if (isset($this->cache[$name])) {
            // We've already instantiated this gateway, so just return the cached copy
            return $this->cache[$name];
        }

        $config = $this->getConfig();

        $factory = new GatewayFactory();

        /** @var GatewayInterface $gateway */
        $gateway = $factory->create($config[$name]['gateway']);

        // Initialize the gateway with config parameters
        if (isset($config[$name])) {
            $gateway->initialize($config[$name]);
        }

        // Cache the gateway
        $this->cache[$name] = $gateway;

        return $gateway;
    }

    public function getConfig()
    {
        $key = 'omnipay';
        if (!isset($this->config)) {
            // Asked config is not parsed yet. Parse it.
            $configs = array($key);
            foreach ($this->parameters as $param => $value) {
                if (!preg_match("/^$key/", $param)) {
                    continue;
                }
                $this->assignArrayByPath($configs, $param, $value);
            }

            $this->config = isset($configs[$key]) ? $configs[$key] : null;
        }

        return $this->config;
    }

    /**
     * Helper method to convert a config in dot notation to a multi-dimensional array
     * For example: "subscription.default: free" becomes array('subscription' => array('default' => 'free'))
     *
     * @param array  $arr The destination array
     * @param string $path The config in dot notation
     * @param string $value The value to be assigned to the config
     */
    private function assignArrayByPath(&$arr, $path, $value)
    {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = & $arr[$key];
        }

        $arr = $value;
    }
}
