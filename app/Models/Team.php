<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends BaseModel
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
                    'required',
                    'string'
                ],
                'description' => [
                    'string'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'name' => [
                    'string'
                ],
                'description' => [
                    'string'
                ]
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

    public function destroyWithCustomFormat($id): bool
    {
        if(Gate::allows('destroyTeam'))
        {
            // Check if team still have member and still active
            $isExist = DB::table(TeamUser::retrieveTableName())
                ->where('team_id', '=', $id)
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
