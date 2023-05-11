<?php

namespace App\Models;

use Illuminate\Http\Request;

class ChannelUser extends BaseModel
{
    protected $updatable = [
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean',
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
