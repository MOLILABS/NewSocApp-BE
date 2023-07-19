<?php

namespace App\Http\Controllers;

use App\Models\CategoryChannel;
use Illuminate\Http\Request;

class CategoryChannelController extends Controller
{
    public $model = CategoryChannel::class;

    public function deleteByForeignKey(Request $request)
    {
        /** @var CategoryChannel $modelObj */
        $modelObj = $this->modelObj;
        return $modelObj->deleteByForeignKey($request);
    }
}
