<?php

namespace App\Models;

use App\Common\Helper;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChannelGroup extends BaseModel
{
    use HasFactory;

    protected $table = 'channel_group';

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'channel_id' => [
                    'required'
                ],
                'group_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

    protected $fillable = [
        'group_id',
        'channel_id'
    ];

    protected $updatable = [
        'group_id' => 'int',
        'channel_id' => 'int',
    ];

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'group_id' => [
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
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
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
                'group_id' => [
                    'int',
                    'required',
                ],
            ]
        );
        if ($validator->fails()) {
            return Helper::getResponse(null, $validator->errors());
        }
        try {
            $groupChannel = DB::table(ChannelGroup::retrieveTableName())
                ->where('channel_id', '=', $request->get('channel_id'))
                ->where('group_id', '=', $request->get('group_id'))
                ->first();
            DB::table(ChannelGroup::retrieveTableName())->delete($groupChannel->id);
            return Helper::getResponse(true);
        } catch (Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
