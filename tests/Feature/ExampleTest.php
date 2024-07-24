<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    private function prepareForPusherTest(): Order
    {
        Config::set('broadcasting.default', 'pusher');
        
        Broadcast::channel('orders.{order}', function (User $user, Order $order) {
            return true;
        });

        $user = User::factory()->create();

        $order = Order::create();

        $this->expectExceptionMessageMatches('/Invalid socket ID TEST/');
        
        $this
            ->withoutExceptionHandling()
            ->actingAs($user)
            ->post('/broadcasting/auth', ['channel_name' => 'presence-orders.' . $order->id, 'socket_id' => 'TEST']);


        return $order;
    }

    public function test_failing_route_model_binding(): void
    {   
        Route::model('order', Order::class);
        
        $this->prepareForPusherTest();
    }

    public function test_working_route_model_binding(): void
    {         
        $this->prepareForPusherTest();
    }
}
