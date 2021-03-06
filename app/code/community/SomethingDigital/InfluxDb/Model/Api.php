<?php

class SomethingDigital_InfluxDb_Model_Api
{
    const XML_PREFIX = 'sd_influxdb/general/';

    public function __construct($args = array())
    {
        $this->client = new Varien_Http_Client();
        $this->setupAuth();
        $this->precision = ($args['precision']) ? $args['precision'] : 's';
    }

    public function write($data)
    {
        $client = $this->client;
        $client->setUri($this->uri('write'));
        $client->setMethod(Zend_Http_Client::POST);
        $client->setRawData($data);
        $response = $client->request();

        if ($response->getStatus() === 204) {
            return true;
        } else {
            Mage::logException(new Exception('InfluxDB write failed, status=' . $response->getStatus()));
            return false;
        }
    }

    protected function setupAuth()
    {
        $username = $this->config('username');
        $password = $this->config('password');
        if ($username && $password) {
            $this->client->setAuth($username, $password);
        }
    }

    protected function uri($operation)
    {
        return $this->config('uri') . '/' . $operation . '?db=' . $this->config('db') .
            '&precision=' . $this->precision;
    }

    protected function config($path)
    {
        return Mage::getStoreConfig(self::XML_PREFIX . $path);
    }
}
