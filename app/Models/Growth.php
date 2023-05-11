<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Growth extends BaseModel
{
    protected $updatable = [
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean',
        'detail' => 'string',
    ];
}
