<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comment = new Comment();
        $comment->email = "doni@gmail.com";
        $comment->title = "Sample Title";
        $comment->comment = "Sample Comment";
        $comment->save();
    }
}
