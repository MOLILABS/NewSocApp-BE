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

    const PLATFORM_TYPES = [
        'facebook' => [
            'description' => 'Facebook',
            'name' => 'Facebook',
            'logo' => 'logo'
        ],
        'youtube' => [
            'description' => 'Youtube',
            'name' => 'Youtube',
            'logo' => 'logo'
        ],
        'tiktok' => [
            'description' => 'Tiktok',
            'name' => 'Tiktok',
            'logo' => 'logo'
        ],
        'website' => [
            'description' => 'Website',
            'name' => 'Website',
            'logo' => 'logo'
        ],
    ];

}
