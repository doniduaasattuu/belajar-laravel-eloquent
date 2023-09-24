<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Voucher;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testOneToMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        $category = $product->category;
        self::assertNotNull($category);
        self::assertEquals("FOOD", $category->id);
        self::assertEquals("Food", $category->name);
        self::assertEquals("Food Category", $category->description);
    }

    // ONE OF MANY POLYMORPHIC
    public function testOneOfManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, CommentSeeder::class]);

        sleep(1);

        $comment = new Comment();
        $comment->email = "eko@pzn.com";
        $comment->title = "Title";
        $comment->comment = "Comment Product Latest";
        $comment->commentable_id = "1";
        $comment->commentable_type = "product";
        $comment->save();

        $product = Product::query()->find("1");

        $comment = $product->oldestComment;
        self::assertNotNull($comment);
        Log::info(json_encode($comment, JSON_PRETTY_PRINT));

        $comment = $product->latestComment;
        self::assertNotNull($comment);
        Log::info(json_encode($comment, JSON_PRETTY_PRINT));
    }

    public function testManyToManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, TagSeeder::class]);

        $product = Product::find("1");
        $tags = $product->tags;
        self::assertNotNull($tags);

        foreach ($tags as $tag) {
            self::assertEquals("pzn", $tag->id);
            self::assertEquals("Programmer Zaman Now", $tag->name);

            $voucher = $tag->vouchers;
            self::assertNotNull($voucher);
        }
    }

    // ELOQUENT COLLECTION
    public function testEloquentCollection()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        // select * from `products`  
        $products = Product::query()->get();

        // select * from `products` where `products`.`id` in (?, ?) and `price` = ? 
        $products = $products->toQuery()->where("price", 200)->get();

        self::assertNotNull($products);
        self::assertEquals("2", $products[0]->id);
    }
}
