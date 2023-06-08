<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\TiktokDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TikTokDetailController extends Controller
{
    public $model = TiktokDetail::class;

    public function handleIndex(Request $request): Response
    {
        try {
            $result = (new TiktokDetail)->queryWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
