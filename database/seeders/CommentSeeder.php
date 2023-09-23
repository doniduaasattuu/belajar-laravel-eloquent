<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $comment = new Comment();
        // $comment->email = "doni@gmail.com";
        // $comment->title = "Sample Title";
        // $comment->comment = "Sample Comment";
        // $comment->commentable_id = "EKO";
        // $comment->commentable_type = Customer::class;
        // $comment->save();

        $this->createCommentForVoucher();
        $this->createCommentForProduct();
    }

    public function createCommentForProduct()
    {
        $product = Product::query()->find("1");
        $comment = new Comment();
        $comment->email = "eko@pzn.com";
        $comment->title = "Title";
        $comment->comment = "Comment Product";
        $comment->commentable_id = $product->id;
        $comment->commentable_type = Product::class;
        $comment->save();
    }

    public function createCommentForVoucher()
    {
        $voucher = Product::query()->first();
        $comment = new Comment();
        $comment->email = "eko@pzn.com";
        $comment->title = "Title";
        $comment->comment = "Comment Voucher";
        $comment->commentable_id = $voucher->id;
        $comment->commentable_type = Voucher::class;
        $comment->save();
    }
}
