<?php
namespace FacebookModule\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use FacebookModule\Facebook;

class FacebookServiceFactory implements FactoryInterface
{

    /**
     * @var string
     */
    protected $configKey = 'facebook';

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FacebookService
     * @throws \RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (empty($config[$this->configKey]) || !is_array($config[$this->configKey])) {
            throw new \RuntimeException("A '{$this->configKey}' config node must be provided");
        }

        if ($serviceLocator->has('Zend\Session\SessionManager')) {
            throw new \RuntimeException("Zend\Session\SessionManager required");
        }

        return new Facebook($config[$this->configKey], $serviceLocator->get('Zend\Session\SessionManager'));
    }
}