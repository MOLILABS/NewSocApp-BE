<?php

namespace App\Mail;

use App\Common\Constant as Constant;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Mail
{
    /**
     * @throws GuzzleException
     */
    public static function sendMail($toMail, $subject, $htmlContent, $cc = null)
    {
        $url = 'https://'.Constant::MAIL_X_RAPIDAPI_HOST.'/mail/send';
        $sender = env('MAIL_USERNAME');
        $data = array(
            "personalizations" => array(
                array(
                    "to" => array(
                        array(
                            "email" => $toMail
                        )
                    )
                )
            ),
            "from" => array(
                "email" => $sender
            ),
            "subject" => $subject,
            "content" => array(
                array(
                    "type" => "text/html",
                    "value" => $htmlContent
                )
            )
        );

        if ($cc) {
            $data["personalizations"][0]["cc"] = [];
            
            foreach ($cc as $key => $value) {
                $data["personalizations"][0]["cc"][] = [
                    "email" => $value
                ];
            }
        }

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'X-RapidAPI-Host' => Constant::MAIL_X_RAPIDAPI_HOST,
                'X-RapidAPI-Key' =>  env("X_RAPIDAPI_KEY")
            ]
        ]);

       $client->post($url, [
            'json' => $data
        ]);

    }
}
