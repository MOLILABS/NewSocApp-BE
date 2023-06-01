<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class ChannelUser extends BaseModel
{
    use HasFactory;

    protected $table = 'channel_user';
    protected $updatable = [
        'is_supporter' => 'boolean',
        'is_responsible' => 'boolean',
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'channel_id' => [
                    'required'
                ],
                'user_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }
}
