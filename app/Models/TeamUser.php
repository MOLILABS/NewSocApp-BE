<?php

namespace App\Models;

use App\Common\Helper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamUser extends BaseModel
{
    protected $updatable = [
        'created_by' => 'string',
        'updated_by' => 'string',
        'is_active' => 'boolean'
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

}
