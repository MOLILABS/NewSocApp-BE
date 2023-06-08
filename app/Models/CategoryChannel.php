<?php

namespace App\Models;

use App\Common\GlobalVariable;
use App\Common\Helper;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CategoryChannel extends BaseModel
{
    use HasFactory;

    protected $softDelete = false;

    protected $table = 'category_channel';

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'channel_id' => [
                    'required'
                ],
                'category_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

    protected $fillable = [
        'category_id',
        'channel_id'
    ];

    protected $filters = [
        'channel_id'
    ];

    protected $updatable = [
        'category_id' => 'int',
        'channel_id' => 'int',
    ];

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'category_id' => [
                    'int'
                ],
                'channel_id' => [
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
                'channel_id' => [
                    'int',
                    'required',
                ],
                'category_id' => [
                    'int',
                    'required',
                ],
            ]
        );
        if ($validator->fails()) {
            return Helper::getResponse(null, $validator->errors());
        }
        try {
            $categoryChannel = DB::table(CategoryChannel::retrieveTableName())
                ->where('channel_id', '=', $request->get('channel_id'))
                ->where('category_id', '=', $request->get('category_id'))
                ->first();
            DB::table(CategoryChannel::retrieveTableName())->delete($categoryChannel->id);
            return Helper::getResponse(true);
        } catch (Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
