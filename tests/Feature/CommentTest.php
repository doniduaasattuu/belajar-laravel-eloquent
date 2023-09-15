<?php

namespace Tests\Feature;

use App\Models\Comment;
use Database\Seeders\CommentSeeder;
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
        $comment->save();

        self::assertNotNull($comment->id);
    }

    public function testUpdateComment()
    {
        $this->seed(CommentSeeder::class);

        $comment = Comment::orderBy("id", "desc")->first();
        self::assertNotNull($comment);

        sleep(1); // kolom update_at selisih 1 detik dari created_at;

        $comment->title = "Sample Title Updated";
        $comment->update();
    }

    public function testDefaultValue()
    {
        $comment = new Comment();
        $comment->email = "contoh@email.com";
        $comment->save();

        self::assertNotNull($comment->title);
        self::assertNotNull($comment->comment);
    }
}
