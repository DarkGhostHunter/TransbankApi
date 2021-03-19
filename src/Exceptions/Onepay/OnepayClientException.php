<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class OnepayClientException extends \Exception implements TransbankException, OnepayException
{

    /**
     * OnepayResponseErrorException constructor.
     * @param string $transaction
     * @param ResponseInterface|null $response
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $transaction, ResponseInterface $response = null,int $code = 0, Throwable $previous = null)
    {
        $error = "Onepay has returned an error\nTransaction: $transaction";

        if ($response !== null) {
            $error .= "\nResponse: " . json_decode($response->getBody()->getContents());
        }

        parent::__construct($error, $code, $previous);
    }
}