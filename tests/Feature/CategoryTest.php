<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function testInsert()
    {
        $category = new Category();

        $category->id = "GADGET";
        $category->name = "Gadget";

        $result = $category->save();
        self::assertTrue($result);
    }

    public function testInsertMany()
    {
        $category = new Category();

        $categories = [
            [
                "id" => "FOOD",
                "name" => "Food"
            ],
            [
                "id" => "GADGET",
                "name" => "Gadget"
            ]
        ];

        for ($i = 2; $i < 10; $i++) {
            $categories[] = [
                "id" => "ID - $i",
                "name" => "Name - $i",
            ];
        }

        $result = $category->query()->insert($categories);
        self::assertTrue($result);

        $total = $category->query()->count();
        self::assertEquals(10, $total);
    }
}
