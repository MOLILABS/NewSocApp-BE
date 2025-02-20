<?php

namespace App\Models;

use App\Common\Constant;
use App\Common\GlobalVariable;
use App\Common\Helper;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static insert(array $params)
 * @method static create(array $array)
 * @method static where(string $string, mixed $param)
 * @method static find($id)
 * @property mixed $id
 * @property string $table
 * @property mixed $worker
 * @property mixed $role
 */
class User extends Authenticatable
{
    const TABLE_NAME = 'users';

    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'last_sent',
        'confirm_email',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'confirm_email',
        'last_sent',
        'salary',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_active',
        'role'
    ];

    protected $filters = [];
    protected $groupBy = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    const ABILITIES = [
        "showAllChannel",
        "showTeamChannel",
        "showUnassignedChannel",

        "storeChannel",

        "assignChannelToTeamUser",
        "assignChannelToAllUser",

        "updateAssignedChannel",
        "updateTeamChannel",
        "updateAllChannel",

        "destroyAssignedChannel",
        "destroyTeamChannel",
        "destroyAllChannel",

        "storeGroup",
        "updateGroup",
        "destroyGroup",

        "storeCategory",
        "updateCategory",
        "destroyCategory",

        "storeTeam",
        "updateTeam",
        "destroyTeam",

        "assignUserToTeam",
        "assignUserToAllTeam",
        
        "removeUserFromCurrentTeam",
        "removeUserFromAllTeam"
    ];

    const ROLES = [
        "admin" => self::ABILITIES,
        "leader" => [],
        "accountant" => [],
        "hr" => [],
        "finance" => [],
        "guest" => [],
        "creator" => [],
    ];

    /**
     * @return HasMany
     */
    public function absenceRequests(): HasMany
    {
        return $this->hasMany(AbsenceRequest::class);
    }

    /**
     * @param $request , $id
     * @return Application|Response|ResponseFactory
     */
    public function updateSalary(Request $request, $id)

    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => [
                    'required',
                    'integer'
                ],
                'salary' => [
                    'required',
                    'integer'
                ]
            ]
        );

        if ($validator->fails()) {
            return Helper::getResponse('', $validator->errors());
        }

        $salary = $request->get('salary');
        try {
            if (Gate::allows('updateSalary')) {
                DB::table(User::TABLE_NAME)
                    ->where('id', '=', $id)
                    ->update([
                        'salary' => $salary
                    ]);

                return Helper::getResponse(true);
            } else {
                return Helper::getResponse(null, 'Unauthorized', 401);
            }
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }


    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function updateUser(Request $request)
    {
        $validation = $request->only(['name', 'avatar', 'old_password', 'new_password']);
        $validator = Validator::make(
            $validation,
            [
                'name' => [
                    'string'
                ],
                'avatar' => [
                    'string',
                    'max:2000'
                ],
                'old_password' => [
                    'required_with:new_password',
                    'string'
                ],
                //Password validation rule
                //English uppercase characters (A – Z)
                //English lowercase characters (a – z)
                //Base 10 digits (0 – 9)
                //Non-alphanumeric (For example: !, $, #, or %)
                //Unicode characters
                'new_password' => [
                    'required_with:old_password',
                    'min:8',
                    'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                ]
            ]
        );

        if ($validator->fails()) {
            return Helper::getResponse('', $validator->errors());
        }
        /** @var GlobalVariable $global */
        $global = app(GlobalVariable::class);
        $oldPassword = $request->get('old_password');
        $newPassword = $request->get('new_password');
        if ($oldPassword)
            if (!Hash::check($oldPassword, $global->currentUser->password)) {
                return Helper::getResponse('', "Old password doesn't match!");
            }
        $data = $request->except('old_password', 'new_password', 'password');
        if ($newPassword)
            $data['password'] = Hash::make($newPassword);
        try {
            DB::table('users')
                ->where('id', '=', $global->currentUser->id)
                ->update($data);
            $user = User::find($global->currentUser->id);
            return Helper::getResponse(['user' => $user]);
        } catch (Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function queryWithCustomFormat(Request $request): array
    {
        $limit = $request->get('limit');
        $request = $request->only($this->filters);
        $model = with(new static)::select();
        $model = $model->where(Constant::IS_ACTIVE, 1);
        $result = $model
            ->paginate($limit ?: BaseModel::CUSTOM_LIMIT)
            ->appends($request);
        $result = $result->map(function($item){
            if(!isset($item->roles)) {
                return $item;
            }
            return $item;
        });
        return ['data' => $result, 'total' => $result->count()];
    }

    /**
     * @param $model
     * @param $request
     * @return mixed
     */
    function filterByRelation($model, $request)
    {
        $role_id = $request->get('role_id');
        if ($role_id){
            $model->whereHas('userRole', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            });
        }
        return $model;
    }

    /**
     * @return array
     */
    static function getQueryValidator(): array
    {
        return [
            'limit' => [
                'numeric',
                'gte:0'
            ]
        ];
    }

}
