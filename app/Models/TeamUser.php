<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class TeamUser extends BaseModel
{
    use HasFactory;

    protected $table = 'team_user';

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

}
