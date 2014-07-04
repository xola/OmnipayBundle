<?php

namespace Xola\OmnipayBundle\Service;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\GatewayInterface;
use Symfony\Component\DependencyInjection\Container;

class Omnipay
{
    protected $config;
    protected $cache = array();

    public function __construct(Container $container)
    {
        $this->parameters = $container->getParameterBag()->all();
    }

    /**
     * Returns an Omnipay gateway.
     *
     * @param string $key The gateway key as defined in the config
     *
     * @return AbstractGateway
     */
    public function get($key)
    {
        if (isset($this->cache[$key])) {
            // We've already instantiated this gateway, so just return the cached copy
            return $this->cache[$key];
        }

        $config = $this->getConfig();
        $factory = new GatewayFactory();
        /** @var GatewayInterface $gateway */
        $gateway = $factory->create($config[$key]['gateway']);

        // Initialize the gateway with config parameters
        if (isset($config[$key])) {
            $gateway->initialize($config[$key]);
        }

        // Cache the gateway
        $this->cache[$key] = $gateway;

        return $gateway;
    }

    /**
     * Returns the gateway name (or type) for a given gateway key.
     *
     * @param string $key The configured gateway key
     *
     * @return null|string The gateway name, or NULL if not found
     */
    public function getGatewayName($key)
    {
        $gateway = null;
        $config = $this->getConfig();

        if(isset($config[$key])) {
            $gateway = $config[$key]['gateway'];
        }

        return $gateway;
    }

    public function getConfig()
    {
        if (!isset($this->config)) {
            // Config has not been parsed yet. So do it.
            $key = 'omnipay';
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
     * @param array $arr The destination array
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
