<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];
    protected $updatable = [
        'name' => 'string',
        'description' => 'string',
    ];
}
