<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class TeamUser extends BaseModel
{
    use HasFactory;

    protected $table = 'team_user';

    protected $updatable = [
        'is_leader' => 'bool',
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'team_id' => [
                    'required'
                ],
                'user_id' => [
                    'required'
                ],
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'is_leader' => [
                    'bool'
                ]
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

}
