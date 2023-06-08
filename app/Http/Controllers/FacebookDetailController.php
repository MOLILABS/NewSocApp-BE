<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FacebookDetail;

class FacebookDetailController extends Controller
{
    public $model = FacebookDetail::class;

    public function handleIndex(Request $request): Response
    {
        try {
            $result = (new FacebookDetail)->queryWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
