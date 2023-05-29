<?php

namespace App\Models;

use App\Common\GlobalVariable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class YoutubeDetail extends BaseModel
{
    use HasFactory;

    protected $fillable = [];
    protected $filters = [
        'between'
    ];

    // function filterByRelation($model)
    // {
    //     /** @var GlobalVariable $global */
    //     $global = app(GlobalVariable::class);
    //     $user_id = $global->currentUser->id;

    //     if ($global->currentUser->hasPermissionTo('admin'))
    //         return $model;
    //     return $model->where('created_by', $user_id);
    // }
}
