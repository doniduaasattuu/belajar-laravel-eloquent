<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\VirtualAccount;
use App\Models\Wallet;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\VirtualAccountSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;
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

    // MANY TO MANY
    public function testManyToMany()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class, ProductSeeder::class]);
        $customer = Customer::find("EKO");
        self::assertNotNull($customer);

        // insert into `customers_likes_products` (`customer_id`, `product_id`) values (?, ?) 
        $customer->likeProducts()->attach("1");

        // select `products`.*, `customers_likes_products`.`customer_id` as `pivot_customer_id`, `customers_likes_products`.`product_id` as `pivot_product_id` from `products` inner join `customers_likes_products` on `products`.`id` = `customers_likes_products`.`product_id` where `customers_likes_products`.`customer_id` = ?
        $products = $customer->likeProducts;

        self::assertCount(1, $products);
        self::assertEquals("Product 1", $products->first()->name);
    }

    public function testManyToManyDetachFromProduct()
    {
        $this->testManyToMany();

        $product = Product::find("1");
        self::assertNotNull($product);

        // delete from `customers_likes_products` where `customers_likes_products`.`product_id` = ? and `customers_likes_products`.`customer_id` in (?)  
        $product->likedByCustomer()->detach("EKO");

        $likedByCustomer = $product->likedByCustomer;
        self::assertCount(0, $likedByCustomer);
    }


    public function testManyToManyDetachFromCustomer()
    {
        $this->testManyToMany();

        $customer = Customer::find("EKO");
        self::assertNotNull($customer);

        // delete from `customers_likes_products` where `customers_likes_products`.`customer_id` = ? and `customers_likes_products`.`product_id` in (?)  
        $customer->likeProducts()->detach("1");

        $likeProducts = $customer->likeProducts;
        self::assertCount(0, $likeProducts);
    }

    public function testPivotAttribute()
    {
        $this->testManyToMany();

        $customer = Customer::find("EKO");
        $products = $customer->likeProducts;

        self::assertNotNull($products);
        $products->each(function ($product) {
            $pivot = $product->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);

            self::assertNotNull($pivot->customer);
            self::assertNotNull($pivot->product);
        });


        Log::info(json_encode($products, JSON_PRETTY_PRINT));
        // [
        //     {
        //         "id": "1",
        //         "name": "Product 1",
        //         "description": "Description 1",
        //         "price": 0,
        //         "stock": 0,
        //         "category_id": "FOOD",
        //         "pivot": {
        //             "customer_id": "EKO",
        //             "product_id": "1",
        //             "created_at": "2023-09-20T23:23:43.000000Z"
        //         }
        //     }
        // ]  
    }

    public function testWherePivot()
    {
        $this->testManyToMany();

        $customer = Customer::find("EKO");

        // select `products`.*, `customers_likes_products`.`customer_id` as `pivot_customer_id`, `customers_likes_products`.`product_id` as `pivot_product_id`, `customers_likes_products`.`created_at` as `pivot_created_at` from `products` inner join `customers_likes_products` on `products`.`id` = `customers_likes_products`.`product_id` where `customers_likes_products`.`customer_id` = ? and `customers_likes_products`.`created_at` >= ?
        $products = $customer->likeProductsLastWeek;

        self::assertNotNull($products);
        Log::info(json_encode($products, JSON_PRETTY_PRINT));
    }

    // PIVOT MODEL
    public function testPivotModel()
    {
        $this->testManyToMany();

        $customer = Customer::find("EKO");
        $products = $customer->likeProducts;

        self::assertNotNull($products);
        Log::info(json_encode($products, JSON_PRETTY_PRINT));

        foreach ($products as $product) {
            $pivot = $product->pivot; // object Like::class
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);

            self::assertNotNull($pivot->customer);
            self::assertNotNull($pivot->product);
        }
    }

    // POLYMORPHIC RELATIONSHIP
    public function testOneToOnePolymorphicCustomer()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class, ProductSeeder::class, ImageSeeder::class]);

        $customer = Customer::query()->find("EKO");
        $image = $customer->image;

        self::assertNotNull($image);
        self::assertEquals("https://www.programmerzamannow.com/image/1.jpg", $image->url);
    }

    public function testOneToOnePolymorphicProduct()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, ImageSeeder::class]);

        $product = Product::find("1");
        $image = $product->image;

        self::assertNotNull($image);
        self::assertEquals("https://www.programmerzamannow.com/image/2.jpg", $image->url);
        Log::info(json_encode($image));
    }

    // EAGER & LAZY LOADING
    public function testEagerLoading()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class, WalletSeeder::class]);

        // wallet dan image sudah di eksekusi saat deklarasi customer
        $customer = Customer::with(["image", "wallet"])->find("EKO");
        self::assertNotNull($customer);

        Log::info("executed last");
        self::assertNotNull($customer->wallet);
        self::assertNotNull($customer->image);
    }

    public function testLazyLoading()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class, WalletSeeder::class]);

        // wallet dan image sudah di eksekusi saat deklarasi customer
        $customer = Customer::find("EKO");
        self::assertNotNull($customer);

        Log::info("executed in the middle");
        self::assertNotNull($customer->wallet);
        self::assertNotNull($customer->image);
    }

    public function testEagerModel()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class, WalletSeeder::class]);

        $customer = Customer::find("EKO");
        self::assertNotNull($customer);

        Log::info("executed last");

        // query wallet sudah di eksekusi sejak deklarasi customer dengan cara mengoverride protected $with di dalam model customer
        self::assertNotNull($customer->wallet);
    }

    // SERIALIZATION
    public function testSerializationWithRelationship()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class, WalletSeeder::class]);

        $customer = Customer::find("EKO");
        self::assertNotNull($customer);

        $json = $customer->toJson(JSON_PRETTY_PRINT);
        Log::info($json);
        /**
         * {
         *    "id": "EKO",
         *    "name": "Eko",
         *    "email": "eko@pzn.com",
         *    "wallet": 
         *          {
         *            "id": 211,
         *            "customer_id": "EKO",
         *            "amount": 1000000
         *          }
         *}  
         */
    }
}
