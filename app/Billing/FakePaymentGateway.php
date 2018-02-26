<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 22.02.18
 * Time: 12:45
 */

namespace App\Billing;


class FakePaymentGateway implements PaymentGateway
{

    private $charges;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return "valid-token";
    }

    public function charge($amount, $token)
    {
        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

}