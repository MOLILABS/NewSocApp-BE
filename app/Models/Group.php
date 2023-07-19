<?php

namespace App\Models;

use App\Common\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];
    protected $updatable = [
        'name' => 'string',
        'description' => 'string',
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'name' => [
                    'required','string'
                ],
                'description' => [
                    'required', 'string'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'description' => [
                    'string'
                ],
                'name' => [
                    'string'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    public function storeWithCustomFormat(Request $request)
    {
        if(Gate::allows('storeGroup'))
        {
            return parent::storeWithCustomFormat($request);
        }
        return null;
    }

    public function updateWithCustomFormat(Request $request, $id): ?Model
    {
        if(Gate::allows('updateGroup'))
        {
            return parent::updateWithCustomFormat($request, $id);
        }
        return null;
    }

    public function destroyWithCustomFormat($id): bool
    {
        if(Gate::allows('destroyGroup'))
        {
            // Check if group still have channel and still active
            $isExist = DB::table(ChannelGroup::retrieveTableName())
                ->where('group_id', '=', $id)
                ->where('is_active', '=', true)
                ->exists();

            if($isExist)
            {
                return false;
            }
            return parent::destroyWithCustomFormat($id);
        }

        return false;
    }
}
