<?php

namespace App\Common;

class Constant
{
    // Flags
    const PRODUCTION_FLAG = 'production';
    const MAIL_X_RAPIDAPI_HOST = 'rapidprod-sendgrid-v1.p.rapidapi.com';
    const MAIL_EXPIRED_TIME = 300;
    const OTP_LENGTH = 10;
    const OTP_CHANGED = [001, 'OTP changed'];
    const OTP_TIMEOUT = [002, 'OTP timeout'];
    const ALREADY_VERIFIED_EMAIL = [003, 'Already verified email'];
    const Tiktok_X_RapidAPI_Host = 'tiktok28.p.rapidapi.com';
    const Youtube_X_RapidAPI_Host = 'youtube-v2.p.rapidapi.com';
    const TIKTOK = 'tiktok';
    const YOUTUBE = 'youtube';
    const WEBSITE = 'website';
    const FACEBOOK = 'facebook';
    // Common fields
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const IS_ACTIVE = 'is_active';
}
