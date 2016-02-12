<?php
/**
 * Created by Malik Abiola.
 * Date: 10/02/2016
 * Time: 16:10
 * IDE: PhpStorm
 */

namespace Paystack\Models;


use Paystack\Contracts\TransactionContract;
use Paystack\Exceptions\PaystackInvalidTransactionException;
use Paystack\Factories\PaystackHttpClientFactory;
use Paystack\Helpers\Utils;
use Paystack\Repositories\TransactionResource;

class OneTimeTransaction implements TransactionContract
{
    use Utils;
    private $transactionResource;

    private $transactionRef;
    private $amount;
    private $email;
    private $plan;

    protected function __construct($transactionRef, $amount, $email, $plan)
    {
        $this->transactionRef = $transactionRef;
        $this->amount = $amount;
        $this->email = $email;
        $this->plan = $plan;

        $this->transactionResource = new TransactionResource(PaystackHttpClientFactory::make());
    }

    public static function make($amount, $email, $plan)
    {
        return new static(self::generateTransactionRef(), $amount, $email, $plan);
    }

    public function initialize()
    {
        return !is_null($this->transactionRef) ?
            $this->transactionResource->initialize($this->_requestPayload()) :
            new PaystackInvalidTransactionException(["message" => "Transaction Reference Not Generated."]);
    }

    public function _requestPayload()
    {
        $payload = [
            'amount'    => $this->amount,
            'reference' => $this->transactionRef,
            'email'     => $this->email
        ];

        if (!empty($this->plan)) {
            $payload['plan'] = $this->plan;
        }

        return $this->toJson($payload);
    }
}
