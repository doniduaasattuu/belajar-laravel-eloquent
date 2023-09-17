<?php

namespace App\Models;

use App\Models\Scopes\isActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    protected $table = "categories";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    public $timestamps = false;

    // to avoid error: Add [description] to fillable property to allow mass assignment on [App\Models\Category].
    protected $fillable = [
        "id",
        "name", // jika name tidak ditambahkan maka akan error: General error: 1364 Field 'name' doesn't have a default value
        "description"
    ];

    // SCOPE
    protected static function booted(): void
    {
        parent::booted();
        self::addGlobalScope(new isActiveScope());
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, "category_id", "id");
    }

    public function cheapestProduct(): HasOne
    {
        return $this->hasOne(Product::class, "category_id", "id")->oldest("price");
    }

    public function mostExpensiveProduct(): HasOne
    {
        return $this->hasOne(Product::class, "category_id", "id")->latest("price");
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            Product::class,
            "category_id",
            "product_id",
            "id",
            "id"
        );
    }
}
