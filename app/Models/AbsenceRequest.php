<?php

namespace App\Models;


use App\Mail\Mail;
use App\Common\Helper;
use App\Common\Constant;
use App\Models\BaseModel;
use Illuminate\Support\Str;
use App\Common\GlobalVariable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class AbsenceRequest extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'absence_type_id'
    ];

    protected $updatable = [
        'reason' => 'string',
        'absence_type_id' => 'integer'
    ];

    const REQUEST_STATUS = [
        'Pending',
        'Accepted',
        'Denied'
    ];

    static function getStoreValidator(Request $request): array
    {
        return array_merge(
            [
                'reason' => [
                    'required',
                    'string'
                ],
                'absence_type_id' => [
                    'required',
                    'integer'
                ]
            ],
            parent::getStoreValidator($request)
        );
    }

    static function getUpdateValidator(Request $request, string $id): array
    {
        return array_merge(
            [
                'reason' => [
                    'string'
                ],
                'absence_type_id' => [
                    'integer'
                ]
            ],
            parent::getUpdateValidator($request, $id)
        );
    }

    /**
     * @return BelongsTo
     */
    public function absenceType(): BelongsTo
    {
        return $this->belongsTo(AbsenceType::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function confirm(Request $request)
    {
        $htmlFilePath = base_path() . '\resources/html/answer_request.php';
        $validate = Validator::make(
            $request->all(),
            [
                'absenceid' => 'required',
                'accept' => 'required',
                'otp' => 'required',
                'note' => 'string'
            ]
        );
        $absence_id = $request->get('absenceid');

        if ($validate->fails()) {
            return Helper::getResponse(null, $validate->errors());
        }

        try {
            $requestID = DB::table(AbsenceRequest::retrieveTableName())
                ->where('id', '=', $absence_id)
                ->first();

            $user = DB::table('users')
                ->where('id', '=', $requestID->user_id)
                ->first();

            if ($request->get('otp') == $requestID->otp) {
                // Check if the OTP is correct
                if ($request->get('accept') == true) {
                    DB::table(AbsenceRequest::retrieveTableName())
                        ->where('id', '=', $absence_id)
                        ->update(
                            [
                                'status' => AbsenceRequest::REQUEST_STATUS[1]
                            ]
                        );
                } else if ($request->get('accept') == false) {
                    DB::table(AbsenceRequest::retrieveTableName())
                        ->where('id', '=', $absence_id)
                        ->update(
                            [
                                'status' => AbsenceRequest::REQUEST_STATUS[2]
                            ]
                        );
                }
            } else {
                DB::table(AbsenceRequest::retrieveTableName())
                    ->where('id', '=', $absence_id)
                    ->update(
                        [
                            'otp' => Str::random(Constant::OTP_LENGTH)
                        ]
                    );
                return Helper::getResponse(null, [
                    'code' => Constant::OTP_CHANGED[0],
                    'message' => Constant::OTP_CHANGED[1],
                ]);
            }


            $htmlContent = file_get_contents($htmlFilePath);
            if ($request->get('note')) {
                $htmlContent = str_replace('{{note}}', $request->get('note'), $htmlContent);
            }

            Mail::sendMail($user->email, "Mail phản hồi nghỉ phép", $htmlContent);

            return Helper::getResponse('Answer sucess');
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
