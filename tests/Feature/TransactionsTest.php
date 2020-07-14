<?php

namespace Tests\Feature;

use Laravel\Paddle\Customer;
use Laravel\Paddle\Transaction;
use Money\Currency;

class TransactionsTest extends FeatureTestCase
{
    public function test_it_can_returns_its_amount_and_currency()
    {
        $customer = new Customer(['paddle_id' => 1]);
        $transaction = new Transaction($customer, [
            'user' => ['user_id' => 1],
            'amount' => '12.45',
            'currency' => 'EUR',
        ]);

        $this->assertSame('€12.45', $transaction->amount());
        $this->assertSame('12.45', $transaction->rawAmount());
        $this->assertInstanceOf(Currency::class, $transaction->currency());
        $this->assertSame('EUR', $transaction->currency()->getCode());
    }

    public function test_it_can_returns_its_subscription()
    {
        $billable = $this->createBillable();
        $subscription = $billable->subscriptions()->create([
            'name' => 'default',
            'paddle_id' => 244,
            'paddle_plan' => 2323,
            'paddle_status' => 'active',
            'quantity' => 1,
        ]);
        $transaction = new Transaction($billable->customer, [
            'user' => ['user_id' => $billable->customer->paddle_id],
            'is_subscription' => true,
            'subscription' => [
                'subscription_id' => 244,
            ],
        ]);

        $this->assertTrue($subscription->is($transaction->subscription()));
    }
}
