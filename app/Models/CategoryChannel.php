<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected $fillable = [
        'category_id',
        'channel_id'
    ];

    protected $filters = [
        'channel_id'
    ];

    protected $updatable = [
        'category_id' => 'bool',
        'channel_id' => 'bool',
    ];

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'category_id' => [
                    'bool'
                ],
                'channel_id' => [
                    'bool'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
