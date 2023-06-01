<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Category extends BaseModel
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

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'description' => [
                    'required','string'
                ],
                'name' => [
                    'required','string'
                ]
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
