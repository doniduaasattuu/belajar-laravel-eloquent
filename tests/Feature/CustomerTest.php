<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\VirtualAccount;
use App\Models\Wallet;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\VirtualAccountSeeder;
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

    // QUERY BUILDER RELATIONSHIP
    public function testInsertRelationship()
    {
        $customer = new Customer();
        $customer->id = "EKO";
        $customer->name = "Eko";
        $customer->email = "eko@pzn.com";
        $customer->save();

        self::assertNotNull($customer);

        $wallet = new Wallet();
        $wallet->amount = 1000000;

        $customer->wallet()->save($wallet);
        self::assertEquals(1000000, $customer->wallet->amount);
    }

    // HAS ONE THROUGH
    public function testHasOneThrough()
    {
        $this->seed([CustomerSeeder::class, WalletSeeder::class, VirtualAccountSeeder::class]);

        $customer = Customer::find("EKO");
        self::assertNotNull($customer);

        $virtualAccount = $customer->virtualAccount;
        // select `virtual_accounts`.*, `wallets`.`customer_id` as `laravel_through_key` from `virtual_accounts` inner join `wallets` on `wallets`.`id` = `virtual_accounts`.`wallet_id` where `wallets`.`customer_id` = ? limit 1  
        self::assertEquals("BCA", $virtualAccount->bank);
    }
}
