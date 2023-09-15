<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
