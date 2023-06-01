<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\ChannelUser;
use App\Models\TeamUser;
use Exception;
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
}
