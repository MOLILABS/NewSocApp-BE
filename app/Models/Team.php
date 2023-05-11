<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description'
    ];

    protected $updatable = [
        'name' => 'string',
        'description' => 'string',
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean'
    ];
}
