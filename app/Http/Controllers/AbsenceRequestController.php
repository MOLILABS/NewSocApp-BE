<?php

namespace App\Http\Controllers;

use App\Mail\Mail;
use App\Common\Helper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Common\GlobalVariable;
use App\Models\AbsenceRequest;
use Illuminate\Http\Request;

class AbsenceRequestController extends Controller
{
    public $model = AbsenceRequest::class;

    public function handleStore(Request $request): Response
    {
        $global = app(GlobalVariable::class);
        $user = $global->currentUser;
        $htmlFilePath = base_path() . '\resources/html/absence_request.php';

        try {
            $request_id = DB::table(AbsenceRequest::retrieveTableName())
                ->insertGetId([
                    'date' => $request->get('date'),
                    'absence_type_id' => $request->get('absence_type_id'),
                    'reason' => $request->get('reason'),
                    'user_id' => $user->id,
                    'status' => AbsenceRequest::REQUEST_STATUS[0],
                    'created_by' => $user->id,
                    'updated_by' => $user->id
                ]);

            $htmlContent = file_get_contents($htmlFilePath);
            $acceptLink = env('FE_URL') . 'absence-request/1/answer' . '?absenceid=' . $request_id . '&otp=' . $user->otp . '&accept=true';
            $denyLink = env('FE_URL') . 'absence-request/1/answer' . '?absenceid=' . $request_id . '&otp=' . $user->otp . '&accept=false';
            $htmlContent = str_replace('{{linkAccept}}', $acceptLink, $htmlContent);
            $htmlContent = str_replace('{{linkDeny}}', $denyLink, $htmlContent);

            $htmlContent = str_replace('{{name}}', $user->name, $htmlContent);
            $htmlContent = str_replace('{{date}}', $request->get('date'), $htmlContent);
            $htmlContent = str_replace('{{reason}}', $request->get('reason'), $htmlContent);

            Mail::sendMail($user->email, "Xin nghỉ phép", $htmlContent);

            return Helper::getResponse(true);
        } catch (\Exception $ex) {
            return Helper::handleApiError($ex);
        }
    }
}
