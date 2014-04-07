<?php
namespace FacebookModule;

use Zend\Authentication\Adapter\AdapterInterface;
use BaseFacebook as Facebook;
use Zend\Authentication\Result;

class AuthenticationAdapter implements AdapterInterface
{

    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * @var array|null
     */
    protected $authenticatedUserData;

    /**
     * @var string
     */
    protected $identityFieldName;

    public function __construct(Facebook $facebook, $identityFieldName = 'email')
    {
        $this->facebook = $facebook;
        $this->setIdentityFieldName($identityFieldName);
    }

    /**
     * @return array|null
     */
    public function getAuthenticatedUserData()
    {
        return $this->authenticatedUserData;
    }

    /**
     * @return string
     */
    public function getIdentityFieldName()
    {
        return $this->identityFieldName;
    }

    /**
     * @param string $identityFieldName
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setIdentityFieldName($identityFieldName)
    {
        if (empty($identityFieldName) || !is_string($identityFieldName)) {
            throw new \InvalidArgumentException('$identityFieldName must be a string and can not be empty');
        }
        $this->identityFieldName = $identityFieldName;
        return $this;
    }

    /**
     * @return Result
     */
    public function authenticate()
    {
        $token  = $this->getAccessToken();

        if (empty($token)) {
            return new Result(Result::FAILURE, null);
        }

        $data   = $this->api('/me');
        $fbId   = $data['id'];

        if (empty($fbId)) {
            return new Result(Result::FAILURE, null);
        }

        if (!empty($data[$this->identityField])) {
            $this->authenticatedUserData = $data;
            return new Result(Result::SUCCESS, $data[$this->identityField]);
        }

        return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null);
    }

}