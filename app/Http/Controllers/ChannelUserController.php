<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\ChannelUser;
use App\Models\TeamUser;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChannelUserController extends Controller
{
    public $model = ChannelUser::class;

    public function handleStore(Request $request): Response
    {
        #TODO: waiting for permission
        try {
            $user_id = $request['user_id'];
            $team_id = $request['team_id'];
            $data = TeamUser::query()->where('user_id', $user_id)->where('team_id', $team_id)->first();
            if ($data){
                $result = $this->modelObj->storeWithCustomFormat($request);
                return Helper::getResponse($result);
            }
            else
                return Helper::getResponse(null,'Out of team', 400);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'user_id' => [
                    'required'
                ],
                'team_id' => [
                    'required'
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
}
