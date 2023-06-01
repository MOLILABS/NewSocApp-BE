<?php

namespace App\Models;

use App\Common\Helper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
