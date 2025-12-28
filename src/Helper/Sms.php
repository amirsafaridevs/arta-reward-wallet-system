<?php

namespace ArtaRewardWalletSystem\Helper;
use ArtaRewardWalletSystem\Service\SmsGetway\FarazSms;

class Sms
{
    protected static $apiKey = '';
    protected static $parentNumber = '';
    
    /**
     * Static method to send SMS (creates instance internally)
     */
    public static function send($to, $pattern='',$data=[])
    {
        $instance = new self();
        $instance->setConfig();
        return $instance->sendSms($to, $pattern, $data);
    }
    
    protected function setConfig()
    {
        
        self::$apiKey = get_option('arta_sms_api_key') ?? '';
        self::$parentNumber = get_option('arta_sms_parent_number') ?? '';
    }
    
    protected function sendSms($to, $pattern, $data)
    {
        FarazSms::setConfig('api_key', self::$apiKey);
        FarazSms::setConfig('pattern', $pattern);
        FarazSms::setConfig('parent_number', self::$parentNumber);
        FarazSms::setConfig('to', $to);
        FarazSms::setConfig('data', $data);
        
        $response = FarazSms::sendSms();
        
        // Log the response
        $logData = [
            'to' => $to,
            'pattern' => $pattern,
            'data' => $data,
            'response' => $response,
            'status' => $response['status'] ?? 'unknown'
        ];
        $this->setLog($logData);
        
        // Return the response
        return $response;
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