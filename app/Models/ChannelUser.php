<?php

namespace App\Models;

use App\Common\Helper;
use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function storeWithCustomFormat(Request $request)
    {
        $global = app(GlobalVariable::class);
        $user = $global->currentUser;
        $abilities = User::ABILITIES;
        $assignedUserId = $request->get('user_id');

        if (Gate::allows($abilities[4])) {
            return parent::storeWithCustomFormat($request);
        } else if (Gate::allows($abilities[3])) {
            // Get current user team ids
            $teamIds = DB::table(TeamUser::retrieveTableName())
                ->where('user_id', '=', $user->id)
                ->pluck('team_id');

            // Check if the user_id given by the current user 
            // have the same team as the user
            $isExist = DB::table(TeamUser::retrieveTableName())
                ->where('user_id', '=', $assignedUserId)
                ->whereIn('team_id', $teamIds)
                ->exists();

            if ($isExist) {
                return parent::storeWithCustomFormat($request);
            }
            return Helper::getResponse('',"The team of the user is not available");
        } else {
            return Helper::getResponse(null, "Unauthorized", 401);
        }
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
