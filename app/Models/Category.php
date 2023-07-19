<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
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
                'description' => [
                    'required','string'
                ],
                'name' => [
                    'required','string'
                ]
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
        if(Gate::allows('storeCategory'))
        {
            return parent::storeWithCustomFormat($request);
        }
        return null;
    }

    public function updateWithCustomFormat(Request $request, $id): ?Model
    {
        if(Gate::allows('updateCategory'))
        {
            return parent::updateWithCustomFormat($request, $id);
        }
        return null;
    }

    public function destroyWithCustomFormat($id): bool
    {
        if(Gate::allows('destroyCategory'))
        {
            // Check if is there category still have channel and still active
            $isExist = DB::table(CategoryChannel::retrieveTableName())
                ->where('category_id', '=', $id)
                ->where('is_active', '=', 1)
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
