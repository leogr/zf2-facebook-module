<?php
namespace FacebookModule;

use Zend\Authentication\Adapter\AdapterInterface;
use BaseFacebook;
use Zend\Authentication\Result;

class AuthenticationAdapter implements AdapterInterface
{

    /**
     * @var BaseFacebook
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

    public function __construct(BaseFacebook $facebook, $identityFieldName = 'email')
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
        $token  = $this->facebook->getAccessToken();

        if (empty($token)) {
            return new Result(Result::FAILURE, null);
        }

        $data   = $this->facebook->api('/me');
        $fbId   = $data['id'];
        $data['token'] = $token;

        if (empty($fbId)) {
            return new Result(Result::FAILURE, null);
        }

        $identityFieldName = $this->getIdentityFieldName();

        if (!empty($data[$identityFieldName])) {
            $this->authenticatedUserData = $data;
            return new Result(Result::SUCCESS, $data[$identityFieldName]);
        }

        return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null);
    }

}