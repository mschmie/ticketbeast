<?php
/**
 * Created by PhpStorm.
 * User: msc
 * Date: 22.02.18
 * Time: 12:05
 */

use App\Concert;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Billing\PaymentGateway;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function customer_can_purchase_concert_tickets()
    {
        // Arrange
        // Create a concert
        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250,
        ]);

        // Act
        // Purchase concert tickets
        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        // Assert
        $this->assertResponseStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Make sure that an order exists for that customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $params)
    {
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    private function assertValidationError($field)
    {
        $this->assertResponseStatus(422);
        $json = $this->decodeResponseJson();
        $this->assertArrayHasKey($field, $json['errors']);

    }

    /** @test */
    public function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('email');
    }

    /** @test */
    public function email_must_be_valid_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $this->orderTickets($concert, [
            'email' => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('email');
    }

    /** @test */
    public function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    public function payment_token_is_required()
    {
        $concert = factory(Concert::class)->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
        ]);

        $this->assertValidationError('payment_token');
    }


}