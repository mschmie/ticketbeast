<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 22.02.18
 * Time: 15:48
 */

namespace App\Billing;


interface PaymentGateway
{
    public function charge($amount, $token);
}