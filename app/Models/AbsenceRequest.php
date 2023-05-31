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
        'absence_type_id',
        'date'
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
                ],
                'date' => [
                    'required',
                    'date'
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
        $htmlContent = file_get_contents($htmlFilePath);

        $validate = Validator::make(
            $request->all(),
            [
                'request_id' => 'required',
                'action' => 'required',
                'otp' => 'required',
                'note' => 'string'
            ]
        );
        $request_id = $request->get('request_id');

        if ($validate->fails()) {
            return Helper::getResponse(null, $validate->errors());
        }

        try {
            $requestID = DB::table(AbsenceRequest::retrieveTableName())
                ->where('id', '=', $request_id)
                ->first();

            $user = DB::table('users')
                ->where('id', '=', $requestID->user_id)
                ->first();

            if ($request->get('otp') == $requestID->otp) {
                // Check if the OTP is correct
                if ($request->get('action') === 'accept') {
                    DB::table(AbsenceRequest::retrieveTableName())
                        ->where('id', '=', $request_id)
                        ->update(
                            [
                                'status' => AbsenceRequest::REQUEST_STATUS[1]
                            ]
                        );

                    $htmlContent = str_replace('{{response}}', "Đơn xin đã được chấp thuận", $htmlContent);
                } else if ($request->get('action') === 'deny') {
                    DB::table(AbsenceRequest::retrieveTableName())
                        ->where('id', '=', $request_id)
                        ->update(
                            [
                                'status' => AbsenceRequest::REQUEST_STATUS[2]
                            ]
                        );

                    $htmlContent = str_replace('{{response}}', "Đơn xin đã bị từ chối", $htmlContent);
                }
            } else {
                DB::table(AbsenceRequest::retrieveTableName())
                    ->where('id', '=', $request_id)
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
