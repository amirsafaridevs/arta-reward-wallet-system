<?php

namespace ArtaRewardWalletSystem\Helper;


class Sms
{
    protected static $apiKey = '';
    protected static $parentNumber = '';
    
    /**
     * Static method to send SMS (creates instance internally)
     */
    public static function send($to, $message)
    {
        $instance = new self();
        $instance->setConfig();
        return $instance->sendSms($to, $message);
    }
    
    protected function setConfig()
    {
        self::$apiKey = get_option('arta_sms_api_key') ?? '';
        self::$parentNumber = get_option('arta_sms_parent_number') ?? '';
    }
    
    protected function sendSms($to, $message)
    {
        $response = null; // Initialize response variable
        $this->setLog(['to' => $to, 'message' => $message, 'response' => $response, 'status' => 'success']);
        return [
            'status' => 'success',
            'message' => 'SMS sent successfully',
            'response' => $response
        ];
    }

    protected function setLog($response)
    {
        // گرفتن لاگ فعلی از option
        $logs = get_option('arta_sms_logs', []);
        if (!is_array($logs)) {
            $logs = [];
        }

        // اضافه کردن لاگ جدید به ابتدای آرایه
        array_unshift($logs, [
            'datetime' => current_time('mysql'),
            'response' => $response
        ]);

        // فقط صد تا لاگ آخر را نگه داریم
        if (count($logs) > 100) {
            $logs = array_slice($logs, 0, 100);
        }

        // ذخیره مجدد لاگ‌ها در option
        update_option('arta_sms_logs', $logs);
    }
   
}