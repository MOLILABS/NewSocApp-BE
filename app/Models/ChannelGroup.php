<?php

namespace App\Models;

use Illuminate\Http\Request;

class ChannelGroup extends BaseModel
{
    protected $table = 'channel_group';
    protected $updatable = [
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean'
    ];
    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'channel_id' => [
                    'required'
                ],
                'group_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

}
