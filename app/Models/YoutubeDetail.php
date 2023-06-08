<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class YoutubeDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'revenue',
        'date',
        'view',
        'subscriber',
        'video_amount',
        'channel_id'
    ];

    protected $updatable = [
        'revenue' => 'integer',
        'date' => 'date',
        'view' => 'integer',
        'subscriber' => 'integer',
        'video_amount' => 'integer',
        'channel_id' => 'integer'
    ];

    static function getQueryValidator(): array
    {
        $global = app(GlobalVariable::class);
        return array_merge(
            [
                'channel_id' => [
                    'required',
                    'integer',
                    $global->currentUser->hasPermissionTo('admin') ? '' : Rule::exists('channel_user', 'channel_id')->where(function ($query) use ($global) {
                        $query->where('user_id', '=', $global->currentUser->id);
                    })
                ]
            ],
            parent::getQueryValidator()
        );
    }

    static function getStoreValidator(Request $request): array
    {
        $global = app(GlobalVariable::class);
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
                'view' => [
                    'required',
                    'integer'
                ],
                'subscriber' => [
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
                    })
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    protected $filters = [
        'between'
    ];

    public function queryWithCustomFormat(Request $request)
    {
        $channel_id = $request->get('channel_id');
        $model = parent::queryWithCustomFormat($request);
        $model = $model->where('channel_id', '=', $channel_id);
        // Haven't check for empty result yet
        return $model->toQuery()->simplePaginate(BaseModel::CUSTOM_LIMIT);
    }

    /**
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
