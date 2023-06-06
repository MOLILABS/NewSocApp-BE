<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ChannelUser extends BaseModel
{
    use HasFactory;

    protected $table = 'channel_user';
    protected $updatable = [
        'is_supporter' => 'bool',
        'is_responsible' => 'bool',
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

    /**
     * @param Request $request
     * @param string $id
     * @return array
     */
    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'is_supporter' => [
                    'bool'
                ],
                'is_responsible' => [
                    'bool'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
