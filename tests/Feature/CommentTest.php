<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Voucher;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function testCreateComment()
    {
        $comment = new Comment();
        $comment->email = "doni@gmail.com";
        $comment->title = "Sample Title";
        $comment->comment = "Sample Comment";
        $comment->commentable_id = "EKO";
        $comment->commentable_type = Customer::class;
        $comment->save();

        self::assertNotNull($comment->id);
    }

    public function testUpdateComment()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, CommentSeeder::class]);

        $comment = Comment::query()->first();
        self::assertNotNull($comment);

        sleep(1); // kolom update_at selisih 1 detik dari created_at;

        $comment->title = "Sample Title Updated";
        $comment->update();
    }

    public function testDefaultValue()
    {
        $comment = new Comment();
        $comment->email = "doni@gmail.com";
        $comment->commentable_id = "EKO";
        $comment->commentable_type = Customer::class;
        $comment->save();

        self::assertNotNull($comment->title);
        self::assertNotNull($comment->comment);
    }

    // ONE TO MANY POLYMORPHIC
    public function testOneToManyPolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, CommentSeeder::class]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        $comments = $product->comments;
        foreach ($comments as $comment) {
            self::assertEquals(Product::class, $comment->commentable_type);
            self::assertEquals($product->id, $comment->commentable_id);
            self::assertEquals("Comment Product", $comment->comment);
        }
        Log::info(json_encode($comments, JSON_PRETTY_PRINT));
    }

    public function testOneToManyPolymorphicVoucher()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, CommentSeeder::class]);

        $voucher = Voucher::query()->first();
        Log::info(json_encode($voucher));
        self::assertNotNull($voucher);

        $comments = $voucher->comments; // Note: this comments not found !!!
        foreach ($comments as $comment) {
            self::assertEquals(Voucher::class, $comment->commentable_type);
            self::assertEquals($voucher->id, $comment->commentable_id);
            self::assertEquals("Comment Voucher", $comment->comment);
        }
        Log::info(json_encode($comments, JSON_PRETTY_PRINT));
    }

    public function testOneToManyPolymorphicProduct()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, VoucherSeeder::class, CommentSeeder::class]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        $comments = $product->comments;
        foreach ($comments as $comment) {
            self::assertEquals(Product::class, $comment->commentable_type);
            self::assertEquals($product->id, $comment->commentable_id);
            self::assertEquals("Comment Product", $comment->comment);
        }
        Log::info(json_encode($comments, JSON_PRETTY_PRINT));
    }
}
