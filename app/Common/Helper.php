<?php
namespace App\Common;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class Helper
{
    /**
     * @param $result
     * @param $errorMessage
     * @param int|null $status
     * @return Application|ResponseFactory|Response
     */
    public static function getResponse($result, $errorMessage = null, int $status = null)
    {
        if ($result) {
            return response([
                'success' => $result
            ], $status ?? 200);
        } else {
            return response([
                'error' => $errorMessage ?? 'Please try again'
            ], $status ?? 400);
        }
    }

    /**
     * @param Exception $e
     * @return Application|ResponseFactory|Response
     */
    public static function handleApiError(Exception $e) {
        if (env('APP') != Constant::PRODUCTION_FLAG) {
            return self::getResponse(null, $e->getMessage() . $e->getTraceAsString());
        } else {
            return self::getResponse(null);
        }
    }

    /**
     * @return string
     */
    public static function generateRandomColor(): string
    {
        return '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
    }
}
