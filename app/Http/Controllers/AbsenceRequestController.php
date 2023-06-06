<?php

namespace App\Http\Controllers;

use App\Mail\Mail;
use Carbon\Carbon;
use App\Common\Helper;
use App\Common\Constant;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Common\GlobalVariable;
use App\Models\AbsenceRequest;
use Illuminate\Http\Request;

class AbsenceRequestController extends Controller
{
    public $model = AbsenceRequest::class;

    /**
     * @param Request $request
     * @return Response
     */
    public function handleStore(Request $request): Response
    {
        $global = app(GlobalVariable::class);
        $user = $global->currentUser;
        $htmlFilePath = base_path() . '\resources/html/absence_request.php';

        $absence_type_id = $request->get('absence_type_id');
        $date = $request->get('date');
        $reason = $request->get('reason');

        try {
            $request_id = DB::table(AbsenceRequest::retrieveTableName())
                ->insertGetId([
                    'date' => $date,
                    'absence_type_id' => $absence_type_id,
                    'reason' => $reason,
                    'user_id' => $user->id,
                    'status' => AbsenceRequest::REQUEST_STATUS[0],
                    'last_sent' => Carbon::now(),
                    'otp' => Str::random(Constant::OTP_LENGTH),
                    'created_by' => $user->id,
                    'updated_by' => $user->id
                ]);
            
            $otp = DB::table(AbsenceRequest::retrieveTableName())
                    ->where('id','=',$request_id)
                    ->first('otp');

            $htmlContent = file_get_contents($htmlFilePath);
            $acceptLink = env('FE_URL') . '/answer' . '?request_id=' . $request_id . '&otp=' . $otp->otp . '&action=accept';
            $denyLink = env('FE_URL') . '/answer' . '?request_id=' . $request_id . '&otp=' . $otp->otp . '&action=deny';
            
            $htmlContent = str_replace('{{linkAccept}}', $acceptLink, $htmlContent);
            $htmlContent = str_replace('{{linkDeny}}', $denyLink, $htmlContent);

            $htmlContent = str_replace('{{name}}', $user->name, $htmlContent);
            $htmlContent = str_replace('{{date}}', $date, $htmlContent);
            $htmlContent = str_replace('{{reason}}', $reason, $htmlContent);

            // should be send to team leader mail
            Mail::sendMail($user->email, "Xin nghỉ phép", $htmlContent);

            return Helper::getResponse(true);
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }

    public function answerRequest(Request $request)
    {
        $modelObj = $this->modelObj;
        return $modelObj->confirm($request);
    }
}
