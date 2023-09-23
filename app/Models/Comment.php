<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $table = "comments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = true;

    // default value for column if not filled
    protected $attributes = [
        "title" => "Sample Title Default",
        "comment" => "Sample Comment Default",
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
