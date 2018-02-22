<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 22.02.18
 * Time: 12:05
 */

use App\Concert;
use App\Billing\FakePaymentGateWay;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function customer_can_purchase_concert_tickets()
    {

        $paymentGateWay = new FakePaymentGateWay;

        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250
        ]);

        // Act
        // Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->ID}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateWay->getValidTestToken(),
        ]);


        // Assert
        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $paymentGateWay->totalCharges());
        // Make sure that an order exists for that customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());
    }
}