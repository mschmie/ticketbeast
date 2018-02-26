<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Psy\Tests\Input\CodeArgumentTest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakePaymentGatewayTest extends TestCase
{

    /** @test */
    public function charges_with_a_valid_payment_token_are_valid()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

}
