<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use App\Models\Scopes\isActiveScope;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ReviewSeeder;
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
        self::assertCount(2, $products);
        self::assertEquals("Product 1", $products->first()->name);
    }

    // QUERY BUILDER RELATIONSHIP
    public function testInsertRelationship()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "FOOD";
        $category->save();

        self::assertNotNull($category);

        $products = new Product();
        $products->id = "1";
        $products->name = "Product 1";
        $products->category_id = "FOOD";

        $category->products()->save($products);
        self::assertEquals(0, $category->products->first()->price);
    }

    public function testRelationshipQuery()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $product = new Product();
        $product->id = "3";
        $product->name = "Product 3";
        $product->description = "Description 3";
        $product->category_id = "FOOD";
        $product->stock = 100;
        $product->save();

        $category = Category::find("FOOD");
        $outOfStocksProduct = $category->products()->where("stock", "<=", 0)->get();

        self::assertNotNull($outOfStocksProduct);
        self::assertCount(2, $outOfStocksProduct);
        self::assertEquals("Product 1", $outOfStocksProduct->first()->name);

        $productReady = $category->products()->where("stock", ">=", 1)->get();
        self::assertNotNull($productReady);
        self::assertEquals("Product 3", $productReady->first()->name);
    }

    // HAS ONE OF MANY
    public function testHasOneOfMany()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find("FOOD");
        $cheapestProduct = $category->cheapestProduct;
        self::assertNotNull($cheapestProduct);
        self::assertEquals(0, $cheapestProduct->price);

        $mostExpensiveProduct = $category->mostExpensiveProduct;
        self::assertNotNull($mostExpensiveProduct);
        self::assertEquals(200, $mostExpensiveProduct->price);
    }

    // HAS MANY THROUGH
    public function testHasManyThrough()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class, CustomerSeeder::class, ReviewSeeder::class]);

        $category = Category::find("FOOD");
        self::assertNotNull($category);

        // select `reviews`.*, `products`.`category_id` as `laravel_through_key` from `reviews` inner join `products` on `products`.`id` = `reviews`.`product_id` where `products`.`category_id` = ?  
        $reviews = $category->reviews;

        self::assertCount(2, $reviews);
    }

    // QUERYING RELATIONSHIP
    public function testQueryingRelationship()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find("FOOD");

        $products = $category->products;
        self::assertNotNull($products);

        $product = $category->products()->where("price", "=", 200)->get();
        self::assertCount(1, $product);
        self::assertEquals("Description 2", $product[0]->description);
        Log::info(json_encode($product, JSON_PRETTY_PRINT));
    }

    // AGGREGATING RELATIONSHIP
    public function testAggregatingRelationship()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::find("FOOD");

        $total_product = $category->products()->count();
        self::assertNotNull($total_product);
        self::assertEquals(2, $total_product);

        $total_product = $category->products()->where("price", "=", 200)->count();
        self::assertNotNull($total_product);
        self::assertEquals(1, $total_product);
    }
}
