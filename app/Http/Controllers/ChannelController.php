<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChannelController extends Controller
{
    public $model = Channel::class;
}
