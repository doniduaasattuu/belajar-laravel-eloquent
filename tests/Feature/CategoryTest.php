<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Scopes\isActiveScope;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    // INSERT
    public function testInsert()
    {
        $category = new Category();

        $category->id = "GADGET";
        $category->name = "Gadget";

        $result = $category->save();
        self::assertTrue($result);
    }

    // INSERT MANY
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

        $result = $category->query()->withoutGlobalScope(isActiveScope::class)->insert($categories);
        self::assertTrue($result);

        $total = $category->query()->withoutGlobalScope(isActiveScope::class)->count();
        self::assertEquals(10, $total);
    }

    // FIND
    public function testFind()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->withoutGlobalScope(isActiveScope::class)->find("FOOD");
        self::assertEquals("FOOD", $category->id);
        self::assertEquals("Food", $category->name);
        self::assertEquals("Food Category", $category->description);
        Log::info(json_encode($category, JSON_PRETTY_PRINT));

        $category = Category::query()->withoutGlobalScope(isActiveScope::class)->find("SMARTPHONE");
        self::assertNull($category);
    }

    // UPDATE
    public function testUpdate()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->withoutGlobalScope(isActiveScope::class)->find("FOOD");

        $category->id = "SMARTPHONE";
        $category->name = "Smartphone";
        $category->description = "Smartphone Category";
        $category->update();

        $categories = Category::query()->withoutGlobalScope(isActiveScope::class)->find("SMARTPHONE");
        self::assertNotNull($categories);
        Log::info(json_encode($categories, JSON_PRETTY_PRINT));
    }

    // SELECT
    public function testSelect()
    {
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->id = "ID $i";
            $category->name = "Name $i";
            $category->save();
        }

        $categories = Category::query()->withoutGlobalScope(isActiveScope::class)->whereNull("description")->get();
        self::assertNotNull($categories);
        self::assertEquals(5, $categories->count());
        foreach ($categories as $cate) {
            Log::info(json_encode($cate));
        }
    }

    // SELECT UDPATE MANY
    public function testSelectUpdateMany()
    {
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->id = "ID $i";
            $category->name = "Name $i";
            $category->save();
        }

        $categories = Category::query()->withoutGlobalScope(isActiveScope::class)->whereNull("description")->get();
        self::assertNotNull($categories);
        self::assertEquals(5, $categories->count());
        foreach ($categories as $category) {
            $category->description = "Updated";
            $category->update();
        }
    }

    // UPDATE MANY
    public function testUpdateMany()
    {
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->id = "ID $i";
            $category->name = "Name $i";
            $category->save();
        }

        $category = new Category();
        $category->id = "ID";
        $category->name = "Name";
        $category->description = "Description";
        $result = $category->save();

        self::assertTrue($result);

        Category::withoutGlobalScope(isActiveScope::class)->whereNull("description")->update([
            "description" => "Updated"
        ]);

        $total = Category::query()->withoutGlobalScope(isActiveScope::class)->where("description", "=", "Updated");
        self::assertEquals(5, $total->count());
    }

    // DELETE
    public function testDelete()
    {
        $this->seed(CategorySeeder::class);

        $categories = Category::query()->withoutGlobalScope(isActiveScope::class)->count();
        self::assertEquals(1, $categories);

        $category = Category::query()->withoutGlobalScope(isActiveScope::class)->find("FOOD");
        $result = $category->delete();

        self::assertTrue($result);

        $categories = Category::withoutGlobalScope(isActiveScope::class)->count();
        self::assertEquals(0, $categories);
    }

    // DELETE MANY
    public function testDeleteMany()
    {
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                "id" => "ID $i",
                "name" => "Name $i",
            ];
        }

        $result = Category::query()->withoutGlobalScope(isActiveScope::class)->insert($categories);
        self::assertTrue($result);

        $total = Category::withoutGlobalScope(isActiveScope::class)->whereNull("description")->count();
        self::assertEquals(10, $total);

        Category::query()->withoutGlobalScope(isActiveScope::class)->whereNull("description")->delete();

        $total = Category::query()->withoutGlobalScope(isActiveScope::class)->count();
        self::assertEquals(0, $total);
    }

    // FILLABLE ATTRIBUTES
    public function testCreateRequest()
    {
        $request = [
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Food Category",
        ];

        $category = new Category($request);
        $result = $category->save();
        self::assertTrue($result);
        self::assertNotNull($category->id);

        // Add [description] to fillable property to allow mass assignment on [App\Models\Category].
    }

    public function testCreateMethodRequest()
    {
        $request = [
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Food Category",
        ];

        $category = Category::create($request);
        $result = $category->save();
        self::assertTrue($result);
        self::assertNotNull($category->id);

        // Add [description] to fillable property to allow mass assignment on [App\Models\Category].
    }

    public function testUpdateMass()
    {
        $this->seed(CategorySeeder::class);

        $request = [
            "name" => "Food Updated",
            "description" => "Food Description Updated"
        ];

        $category = Category::query()->withoutGlobalScope(isActiveScope::class)->find("FOOD");
        $category->fill($request);
        $category->save();

        self::assertEquals("Food Updated", $category->name);
    }

    public function testGlobalScope()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = false;
        $category->save();

        $category = Category::query()->find("FOOD");
        self::assertNull($category);
    }

    public function testWithoutGlobalScope()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = false;
        $category->save();

        $category = Category::query()->withoutGlobalScopes([isActiveScope::class])->find("FOOD");
        self::assertNotNull($category);
    }

    // ONE TO MANY RELATIONSHIP
    public function testOneToMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::query()->find("FOOD")->first();
        self::assertNotNull($category);

        $products = $category->products;
        self::assertCount(1, $products);
        self::assertEquals("Product 1", $products->first()->name);
    }
}
