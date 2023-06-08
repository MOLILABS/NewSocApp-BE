<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
    ];

    const TIKTOK = 'Tiktok';
    const YOUTUBE = 'Youtube';
    const WEBSITE = 'Website';
    const FACEBOOK = 'Facebook';

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
