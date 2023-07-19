<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public $model = User::class;

    /**
     * @param Request $request
     * @param $id
     * @return Application|ResponseFactory|Response
     */
    public function updateSalary(Request $request, $id)
    {
        /** @var User $modelObj */
        $modelObj = $this->modelObj;
        return $modelObj->updateSalary($request, $id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateUser(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->updateUser($request);
    }

}
