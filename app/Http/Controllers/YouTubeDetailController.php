<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\YoutubeDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YouTubeDetailController extends Controller
{
    public $model = YoutubeDetail::class;

    public function handleIndex(Request $request): Response
    {
        try {
            $result = (new YoutubeDetail)->queryWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
