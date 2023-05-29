<?php

namespace App\Http\Controllers;

use Exception;
use App\Common\Helper;
use App\Models\Platform;
use Illuminate\Support\Str;
use App\Models\TiktokDetail;
use Illuminate\Http\Request;
use App\Models\YoutubeDetail;
use Illuminate\Http\Response;
use App\Models\FacebookDetail;

class GrowthController extends Controller
{
    

    public function __construct(Request $request)
    {
        // $platform = $request->route()->parameter('platform');
        // if ($platform == Platform::PLATFORMS[0]) {
        //     $this->model = FacebookDetail::class;
        // } else if ($platform == Platform::PLATFORMS[1]) {
        //     $this->model = TiktokDetail::class;
        // } else if ($platform == Platform::PLATFORMS[2]) {
            $this->model = YoutubeDetail::class;
        // }
        parent::__construct($request);
    }
    // public function handleIndex(Request $request): Response
    // {
    //     $platform = Str::ucfirst($request->get('platform'));

    //     try {
    //         $modelObj = new $this->model;
    //         $result = $modelObj->queryWithCustomFormat($request);
    //         return Helper::getResponse($result);
    //     } catch (Exception $ex) {
    //         return Helper::handleApiError($ex);
    //     }
    // }
}
