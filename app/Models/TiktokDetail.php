<?php

namespace App\Models;

use App\Common\GlobalVariable;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class TiktokDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'revenue',
        'date',
        'follow',
        'like',
        'video_amount',
        'channel_id'
    ];

    protected $updatable = [
        'revenue' => 'integer',
        'date' => 'date',
        'follow' => 'integer',
        'like' => 'integer',
        'video_amount' => 'integer',
        'channel_id' => 'integer'
    ];

    protected $filters = [
        'between'
    ];

    static function getQueryValidator(Request $request): array
    {
        $global = app(GlobalVariable::class);
        return array_merge(
            [
                'channel_id' => [
                    'required',
                    'integer',
                    $global->currentUser->hasPermissionTo('admin') ? '' : Rule::exists('channel_user', 'channel_id')->where(function ($query) use ($global) {
                        $query->where('user_id', '=', $global->currentUser->id);
                    }),
                ]
            ],
            parent::getQueryValidator($request)
        );
    }

    static function getStoreValidator(Request $request): array
    {
        $global = app(GlobalVariable::class);
        $platforms = array_keys(Platform::PLATFORMS);
        return array_merge(
            [
                'revenue' => [
                    'required',
                    'integer'
                ],
                'date' => [
                    'required',
                    'date'
                ],
                'follow' => [
                    'required',
                    'integer'
                ],
                'like' => [
                    'required',
                    'integer'
                ],
                'video_amount' => [
                    'required',
                    'integer'
                ],
                'channel_id' => [
                    'required',
                    'integer',
                    $global->currentUser->hasPermissionTo('admin') ? '' : Rule::exists('channel_user', 'channel_id')->where(function ($query) use ($global) {
                        $query->where('user_id', '=', $global->currentUser->id);
                    }),
                    Rule::exists(Channel::retrieveTableName(), 'id')->where(function ($query) use ($platforms, $request) {
                        $query
                            ->where('platform_id', '=', ($platforms[1] + 1))
                            ->where('id', '=', $request->get('channel_id'));
                    })
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'revenue' => [
                    'integer'
                ],
                'date' => [
                    'date'
                ],
                'follow' => [
                    'integer'
                ],
                'like' => [
                    'integer'
                ],
                'video_amount' => [
                    'integer'
                ],
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

    public function queryWithCustomFormat(Request $request)
    {
        $channel_id = $request->get('channel_id');
        $model = parent::queryWithCustomFormat($request);
        $model = $model->where('channel_id', '=', $channel_id);
        // Haven't check for empty result yet
        return $model->toQuery()->simplePaginate(BaseModel::CUSTOM_LIMIT);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
