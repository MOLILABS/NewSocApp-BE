<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends BaseModel
{
    public $active = true;

    protected $fillable = [
        'name',
        'description'
    ];
}
