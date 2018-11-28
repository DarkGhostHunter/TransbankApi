<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use Exception;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;

class WebpayOneclick extends Transaction
{
    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType = 'oneclick';

    /**
     * Registers the User into WebpaySoap Oneclick systems
     *
     * @param $username
     * @param $email
     * @param $urlReturn
     * @return array
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function register($username, $email, $urlReturn)
    {
        try {
            $registration = new Fluent([
                'username' => $username,
                'email' => $email,
                'responseURL' => $urlReturn,
            ]);

            $response = $this->performRegister($registration);

            // Validate the Response, return the results if it passes
            return $this->validate()
                ? $response
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Ends the Registration process in WebpaySoap Oneclick systems
     *
     * @param string $token
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function confirm($token)
    {
        try {
            $registration = new Fluent([
                'token' => $token,
            ]);

            $response = $this->performConfirm($registration);

            // Return the response if the validation passes
            // Validate the Response, return the results if it passes
            return $this->validate()
                ? $response
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Unregisters (removes) an User from WebpaySoap Oneclick
     *
     * @param $tbkUser
     * @param $username
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function unregister($tbkUser, $username)
    {
        try {

            $unregister = new Fluent([
                'tbkUser' => $tbkUser,
                'username' => $username,
            ]);

            $response = $this->performUnregister($unregister);

            // Return the Response if the validation passes
            return $this->validate()
                ? $response
                : $this->throwException();


        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Authorizes (charges) the Transaction to the User through WebpaySoap Oneclick
     *
     * @param $buyOrder
     * @param $tbkUser
     * @param $username
     * @param $amount
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function charge($buyOrder, $tbkUser, $username, $amount)
    {
        try {
            $charge = new Fluent([
                'buyOrder' => $buyOrder,
                'tbkUser' => $tbkUser,
                'username' => $username,
                'amount' => $amount,
            ]);

            $response = $this->performCharge($charge);

            return $this->validate()
                ? $response
                : $this->throwException();

        } catch (Exception $e) {

            return $this->throwExceptionWithMessage($e->getMessage());

        }
    }

    /**
     * Reverses a Transaction made through WebpaySoap Oneclick
     *
     * @param $buyOrder
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function reverse($buyOrder)
    {
        try {

            $reverse = new Fluent([
                'buyorder' => $buyOrder
            ]);

            $response = $this->performReverse($reverse);

            // Return the Response if the validation passes
            return $this->validate()
                ? $response
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Registers the User in WebpaySoap Oneclick systems
     *
     * @param $register
     * @return mixed
     */
    protected function performRegister($register)
    {
        return (array)($this->connector->initInscription([
            'arg0' => $register
        ]))->return;
    }

    /**
     * Finishes the Inscription process
     *
     * @param $confirm
     * @return mixed
     */
    protected function performConfirm($confirm)
    {
        return (array)($this->connector->finishInscription([
            'arg0' => $confirm
        ]))->return;
    }

    /**
     * Removes a User from WebpaySoap systems
     *
     * @param $unregister
     * @return mixed
     */
    protected function performUnregister($unregister)
    {
        return (array)($this->connector->removeUser([
            'arg0' => $unregister
        ]))->return;
    }

    /**
     * Performs an authorized charge to the User
     *
     * @param $charge
     * @return mixed
     */
    protected function performCharge($charge)
    {
        return (array)($this->connector->authorize([
            'arg0' => $charge
        ]))->return;
    }

    /**
     * Performs a Reverse in WebpaySoap
     *
     * @param $reverse
     * @return mixed
     */
    protected function performReverse($reverse)
    {
        return (array)($this->connector->codeReverseOneClick([
            'arg0' => $reverse
        ]))->return;
    }
}
