<?php

namespace App\Http\Controllers;

use App\Common\Helper;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPUnit\TextUI\Help;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $model;
    /** @var BaseModel $modelObj */
    public $modelObj;

    public function __construct()
    {
        $this->modelObj = new $this->model;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $modelValidator = call_user_func($this->model . '::getQueryValidator');
        $callback = function ($request) {
            return $this->handleIndex($request);
        };
        return $this->validateCustom($request, $modelValidator, $callback);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return Response
     */
    public function show(string $id): Response
    {
        // TODO: missing permission check
        $result = $this->currentQuery()
            ->with($this->modelObj->showingRelations)
            ->where($this->modelObj->queryBy, $id)
            ->orWhere('id', $id)
            ->select($this->modelObj->getAliasString())
            ->first();
        return Helper::getResponse($result);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        return $this->handleDestroy($id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: missing permission check
        $modelValidator = call_user_func($this->model . '::getInsertValidator', $request);
        $callback = function ($request) {
            return $this->handleCreate($request);
        };
        return $this->validateCustom($request, $modelValidator, $callback);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        // TODO: should apply this record-permission-checking on other actions as well
        //        if (!$this->modelObj->checkPermission($id)) {
        //            return Helper::getResponse(null, 'Not allowed', 403);
        //        }
        $modelValidator = call_user_func($this->model . '::getUpdateValidator', $request, $id);
        $callback = function ($request) use ($id) {
            return $this->handleUpdate($request, $id);
        };
        return $this->validateCustom($request, $modelValidator, $callback);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleIndex(Request $request): Response
    {
        try {
            $result = $this->modelObj->queryWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleCreate(Request $request): Response
    {
        try {
            $result = $this->modelObj->insertWithCustomFormat($request);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function handleUpdate(Request $request, $id): Response
    {
        try {
            $result = $this->modelObj->updateWithCustomFormat($request, $id);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function handleDestroy($id): Response
    {
        try {
            $result = $this->modelObj->destroyWithCustomFormat($id);
            return Helper::getResponse($result);
        } catch (Exception $e) {
            return Helper::handleApiError($e);
        }
    }

    /**
     * @return Builder
     */
    private function currentQuery(): Builder
    {
        return call_user_func($this->model . '::query');
    }

    /**
     * @param $input
     * @param $rule
     * @param $callback
     * @return Response
     */
    public function validateCustom($input, $rule, $callback): Response
    {
        $validator = Validator::make($input->all(), $rule);
        try {
            $validator->validate();
            return $callback($input);
        } catch (ValidationException $e) {
            return Helper::getResponse(
                null,
                $validator->errors()->first()
            );
        }
    }
}
