<?php

namespace App\Models;

use Illuminate\Http\Request;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function storeWithCustomFormat(Request $request)
    {
        $global = app(GlobalVariable::class);
        $user = $global->currentUser;
        $team_id = $request->get('team_id');
        $user_id = $request->get('user_id');

        if (Gate::allows('assignUserToAllTeam')) {
            return parent::storeWithCustomFormat($request);
        } else if (Gate::allows('assignUserToTeam')) {
            $teamIds = DB::table(TeamUser::retrieveTableName())
                ->where('user_id', '=', $user->id)
                ->pluck('team_id');

            $user = User::find($user_id);
            $user->removeRole('guest');
            $user->assignRole('creator');

            $isExist = $teamIds->contains($team_id);

            if($isExist)
            {
                return parent::storeWithCustomFormat($request);
            }
            
            return null;
        }

        return null;
    }
}
