<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends BaseModel
{
    use HasFactory;

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}