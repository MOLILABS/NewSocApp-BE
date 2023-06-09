<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\Channel;
use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChannelController extends Controller
{
    public $model = Channel::class;

    public function handleIndex(Request $request): Response
    {
        try {
            $result = $this->modelObj->queryWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
