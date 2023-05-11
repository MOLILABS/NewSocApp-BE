<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\ChannelUser;
use App\Models\TeamUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TeamUserController extends Controller
{
    public $model = TeamUser::class;

    public function handleStore(Request $request): Response
    {
        try {
            $result = $this->modelObj->storeWithCustomFormat($request);
            $user_id = $request['user_id'];
            $datas = ChannelUser::query()->where('user_id', $user_id);
            DB::beginTransaction();
            try {
                foreach ($datas as $data)
                    ChannelUser::destroy($data->id);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }
}
