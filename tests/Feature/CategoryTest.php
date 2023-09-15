<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

    public function testFind()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->find("FOOD");
        self::assertEquals("FOOD", $category->id);
        self::assertEquals("Food", $category->name);
        self::assertEquals("Food Category", $category->description);
        Log::info(json_encode($category, JSON_PRETTY_PRINT));

        $category = Category::query()->find("SMARTPHONE");
        self::assertNull($category);
    }

    public function testUpdate()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->find("FOOD");

        $category->id = "SMARTPHONE";
        $category->name = "Smartphone";
        $category->description = "Smartphone Category";
        $category->update();

        $categories = Category::query()->find("SMARTPHONE");
        self::assertNotNull($categories);
        Log::info(json_encode($categories, JSON_PRETTY_PRINT));
    }
}
