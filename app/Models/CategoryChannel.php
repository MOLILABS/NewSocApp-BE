<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class CategoryChannel extends BaseModel
{
    use HasFactory;

    protected $table = 'category_channel';

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
