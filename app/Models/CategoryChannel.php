<?php

namespace App\Models;

use Illuminate\Http\Request;

class CategoryChannel extends BaseModel
{
    protected $table = 'category_channel';
    protected $updatable = [
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean',
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'channel_id' => [
                    'required'
                ],
                'category_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }
}
