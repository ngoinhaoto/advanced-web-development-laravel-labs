<?php

namespace Tests\Unit\Notifications;

use App\Events\OrderCreated;
use App\Listeners\SendOrderCreatedNotification;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class OrderCreatedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_send_order_created_notification()
    {
        Notification::fake();
        Event::fake(); // Prevents events from auto-firing during the test

        // Simulate a user session
        Session::put('user_id', 'DUMMY_USER_ID');

        // Create a cart with items
        $cart = Cart::factory()
            ->has(CartItem::factory()->count(3), 'items')
            ->create();

        // Simulate checkout form submission
        $body = [
            'full_name' => 'John Doe',
            'phone_number' => '+84111122222',
            'email' => 'john@example.com',
            'shipping_address' => '123 Main Street',
        ];

        $this->withSession(['user_id' => 'DUMMY_USER_ID'])
            ->post(route('checkout.store'), $body);

        // Ensure the order is created
        $order = Order::where('cart_id', $cart->id)->first();
        $this->assertNotNull($order);

        // Manually trigger the OrderCreated event and listener
        Event::assertDispatched(OrderCreated::class);
        (new SendOrderCreatedNotification())->handle(new OrderCreated($order));

        // Assert that the notification was sent via the correct email
        Notification::assertSentOnDemand(OrderCreatedNotification::class, function ($notification, $channels, $notifiable) use ($order) {
            return $notifiable->routes['mail'] === [$order->email => $order->full_name];
        });
    }
}
