<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Growth extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'detail',
        'date'
    ];
    protected $updatable = [
        'detail' => 'string',
        'date' => 'date',
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'name' => [
                    'required','string'
                ],
                'description' => [
                    'required', 'string'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'description' => [
                    'string'
                ],
                'name' => [
                    'string'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }
}
