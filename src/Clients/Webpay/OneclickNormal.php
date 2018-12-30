<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use Exception;

class OneclickNormal extends WebpayClient
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
     * @param WebpayTransaction $transaction
     * @return array
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function register(WebpayTransaction $transaction)
    {
        $registration = (object)[
            'username' => $transaction->username,
            'email' => $transaction->email,
            'responseURL' => $transaction->responseUrl,
        ];

        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performRegister($registration)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Ends the Registration process in WebpaySoap Oneclick systems
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function confirm(WebpayTransaction $transaction)
    {
        $registration = (object)[
            'token' => $transaction->token,
        ];

        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performConfirm($registration)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Unregisters (removes) an User from WebpaySoap Oneclick
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function unregister(WebpayTransaction $transaction)
    {
        $unregister = (object)[
            'tbkUser' => $transaction->tbkUser,
            'username' => $transaction->username,
        ];

        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performUnregister($unregister)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Authorizes (charges) the WebpayClient to the User through WebpaySoap Oneclick
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function charge(WebpayTransaction $transaction)
    {
        $charge = (object)[
            'buyOrder' => $transaction->buyOrder,
            'tbkUser' => $transaction->tbkUser,
            'username' => $transaction->username,
            'amount' => $transaction->amount,
        ];

        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performCharge($charge)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Reverses a WebpayClient made through WebpaySoap Oneclick
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function reverse(WebpayTransaction $transaction)
    {

        $reverse = (object)[
            'buyorder' => $transaction->buyOrder
        ];

        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performReverse($reverse)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
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
