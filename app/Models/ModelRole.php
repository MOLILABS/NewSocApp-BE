<?php

namespace App\Models;

use App\Common\GlobalVariable;
use App\Common\Helper;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ModelRole extends BaseModel
{
    use HasFactory;

    protected $softDelete = false;

    protected $table = 'model_has_roles';
    protected $hidden = 'model_type';

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'model_id' => [
                    'required'
                ],
                'role_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

    protected $fillable = [
        'model_id',
        'role_id',
        'model_type'
    ];

    protected $filters = [
        'model_id'
    ];

    protected $updatable = [
        'model_id' => 'int',
        'role_id' => 'int',
    ];

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'model_id' => [
                    'int'
                ],
                'role_id' => [
                    'int'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function deleteByForeignKey(Request $request)
    {
        $validation = $request->all();
        $validator = Validator::make(
            $validation,
            [
                'model_id' => [
                    'int',
                    'required',
                ],
                'role_id' => [
                    'int',
                    'required',
                ],
            ]
        );
        if ($validator->fails()) {
            return Helper::getResponse(null, $validator->errors());
        }
        try {
            $data = DB::table(ModelRole::retrieveTableName())
                ->where('model_id', '=', $request->get('model_id'))
                ->where('role_id', '=', $request->get('role_id'))
                ->first();
            DB::table(ModelRole::retrieveTableName())->delete($data->id);
            return Helper::getResponse(true);
        } catch (Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }

    protected function getAdditionalStoreFields(Request $request): array
    {
        return array_merge(
            [
                'model_type' => 'App\Models\User',
            ],
            parent::getAdditionalStoreFields($request)
        );
    }

    protected function getHiddenField(): array
    {
        return array_merge(
            [
                'model_type'
            ],
            parent::getHiddenField()
        );
    }

    /**
     * @param Request $request
     * @return true
     */
    public function storeWithCustomFormat(Request $request): bool
    {
        $keys = array_keys($this::getStoreValidator($request));
        $additionalFields = $this->getAdditionalStoreFields($request);
        $params = array_merge(
            collect($request->all())->only($keys)->toArray(),
            $additionalFields
        );
        self::query()->insert($params);
        return true;
    }

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

}
