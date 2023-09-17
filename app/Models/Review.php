<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends Model
{
    protected $table = "reviews";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->BelongsTo(Product::class, "product_id", "id");
    }

    public function customer(): BelongsTo
    {
        return $this->BelongsTo(Customer::class, "customer_id", "id");
    }
}
