<?php

namespace App\Models;

use App\Common\Helper;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'category_id' => 'int',
        'channel_id' => 'int',
    ];

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'category_id' => [
                    'int'
                ],
                'channel_id' => [
                    'int'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    public function updateWithCustomFormat(Request $request, $id): ?Model
    {
        $model = parent::updateWithCustomFormat($request, $id);
        $global = app(GlobalVariable::class);
        $user = $global->currentUser;
        $abilities = User::ABILITIES;
        $channel_id = $request->get('channel_id');

        if (Gate::allows($abilities[8])) {
            return $model;
        } else if (Gate::allows($abilities[7])) {
            // Get user's teams
            $teamIDs = DB::table(TeamUser::retrieveTableName())
                ->where('user_id', '=', $user->id)
                ->pluck('team_id');

            // Get all user from all the team
            $userIds = DB::table(TeamUser::retrieveTableName())
                ->whereIn('team_id', $teamIDs)
                ->pluck('user_id');

            // Get all channel from all the user
            $channelIds = DB::table(ChannelUser::retrieveTableName())
                ->whereIn('user_id', $userIds)
                ->pluck('channel_id');

            $isExist = $channelIds->contains($channel_id);

            if ($isExist) {
                return $model;
            }

            // Not sure if return null is correct since this function
            // required to return a Model, not null
            return null;
        } else if (Gate::allows($abilities[6])) {
            // Check if the user have the channel
            $isExist = DB::table(ChannelUser::retrieveTableName())
                ->where('channel_id', '=', $channel_id)
                ->where('user_id', '=', $user->id)
                ->exists();

            if ($isExist) {
                return $model;
            }

            return null;
        }
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
