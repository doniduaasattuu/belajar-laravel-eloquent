<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Wallet;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function testOneToOneFromCustomer()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class]);

        $customer = Customer::query()->find("EKO");
        self::assertNotNull($customer);

        $wallet = $customer->wallet;
        self::assertEquals(1000000, $wallet->amount);
    }

    public function testOneToOneFromWallet()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class]);

        $wallet = Wallet::query()->where("customer_id", "=", "EKO")->first();
        self::assertNotNull($wallet);
        self::assertEquals(1000000, $wallet->amount);

        $customer = $wallet->customer;
        self::assertEquals("Eko", $customer->name);
        self::assertEquals("eko@pzn.com", $customer->email);
    }
}
