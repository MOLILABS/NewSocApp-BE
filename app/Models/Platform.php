<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends BaseModel
{
    use HasFactory;
    public $active = true;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'created_by',
        'updated_by'
    ];

    protected $updatable = [
        'name' => 'string',
        'description' => 'string',
        'logo' => 'string',
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean'
    ];
}
