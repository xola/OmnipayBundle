<?php

namespace Xola\OmnipayBundle\Service;

use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\Formatter\FullHttpMessageFormatter;
use Omnipay\Common\GatewayFactory;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Http\Client;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Container;

class Omnipay
{
    protected $config;
    protected $cache = array();
    protected $logger;
    private $container;

    public function __construct(Container $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->initConfig($container->getParameterBag()->all());
        $this->logger = $logger;
    }

    private function initConfig($parameters)
    {
        $key = 'omnipay';
        $configs = array($key);
        foreach ($parameters as $param => $value) {
            if (!preg_match("/^$key/", $param)) {
                continue;
            }
            $this->assignArrayByPath($configs, $param, $value);
        }

        $this->config = isset($configs[$key]) ? $configs[$key] : null;
    }

    /**
     * Returns an Omnipay gateway.
     *
     * @param string $key Gateway key as defined in the config
     *
     * @return GatewayInterface
     * @throws RuntimeException If no gateway is configured for the key
     */
    public function get($key = null)
    {
        $config = $this->getConfig();

        if (is_null($key) && isset($config['default'])) {
            // No key was specified, so use the default gateway
            $key = $config['default'];
        }

        if (isset($this->cache[$key])) {
            // We've already instantiated this gateway, so just return the cached copy
            return $this->cache[$key];
        }

        $gatewayName = $this->getGatewayName($key);
        if (!$gatewayName) {
            // Invalid gateway key
            throw new RuntimeException('Gateway key "' . $key . '" is not configured');
        }

        $loggerPlugin = new LoggerPlugin($this->logger, new FullHttpMessageFormatter());
        $httpClient = new PluginClient(HttpClientDiscovery::find(), [$loggerPlugin]);

        $factory = new GatewayFactory();
        $client = new Client($httpClient);
        $gateway = $factory->create($gatewayName, $client);

        if (isset($config[$key])) {
            // Default parameters have been configured, so use them
            $combinedParameters = array_merge($this->getParametersByGatewayName($gatewayName), $config[$key]);
            $gatewayName = strtolower($combinedParameters['gateway']);
            if (isset($config['defaults'][$gatewayName])) {
                $combinedParameters = array_merge($combinedParameters, $config['defaults'][$gatewayName]);
            }
            $gateway->initialize($combinedParameters);
        }

        // Cache the gateway
        $this->cache[$key] = $gateway;

        return $gateway;
    }

    /**
     * Get omnipay parameters based on the 'gateway' attribute
     *
     * @param string $name
     *
     * @return array
     */
    private function getParametersByGatewayName($name)
    {
        foreach ($this->config as $gatewayConfig) {
            if (isset($gatewayConfig['gateway']) && $gatewayConfig['gateway'] === $name) {
                return $gatewayConfig;
            }
        }

        return array();
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

        if (isset($config[$key])) {
            $gateway = $config[$key]['gateway'];
        }

        return $gateway;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the default parameters for a gateway key.
     *
     * @param string $key   Gateway key
     * @param mixed  $value Parameters for the gateway
     */
    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
    }

    /**
     * Helper method to convert a config in dot notation to a multi-dimensional array
     * For example: "subscription.default: free" becomes array('subscription' => array('default' => 'free'))
     *
     * @param array  $arr   The destination array
     * @param string $path  The config in dot notation
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