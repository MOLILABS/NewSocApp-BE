<?php

namespace App\Http\Controllers;

use App\Models\ChannelGroup;
use Illuminate\Http\Request;

class ChannelGroupController extends Controller
{
    public $model = ChannelGroup::class;

    public function deleteByForeignKey(Request $request)
    {
        /** @var ChannelGroup $modelObj */
        $modelObj = $this->modelObj;
        return $modelObj->deleteByForeignKey($request);
    }
}
