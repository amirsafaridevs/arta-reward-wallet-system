<?php
namespace ArtaRewardWalletSystem\Service\SmsGetway;

use ArtaRewardWalletSystem\Contract\Abstracts\AbstractSmsGetway;
use IPPanel\Client;

class FarazSms extends AbstractSmsGetway
{
    protected static string $name = 'farazsms';
    protected static $client = null;
    protected static string $apiKey = '';
    
    public static function getBalance(): mixed
    {
        return self::getClient()->getCredit();
    }
  
    public static function sendSms(): array
    {
        try {
            $instance = static::get();
            $config = $instance->getConfig();
            
            $pattern = $config['pattern'] ?? '';
            $parentNumber = $config['parent_number'] ?? '';
            $to = $config['to'] ?? '';
            $data = $config['data'] ?? [];
            
            if (empty($pattern)) {
                return [
                    'status' => 'error',
                    'message' => 'پارامتر  کد پترن لازم برای ارسال SMS تنظیم نشده است',
                    'code' => 'missing_parameters'
                ];
            }
            if (empty($to)) {
                return [
                    'status' => 'error',
                    'message' => 'شماره تلفن لازم برای ارسال SMS تنظیم نشده است',
                    'code' => 'missing_parameters'
                ];
            }
            
            // Get API key from config if not set
            if (empty(self::$apiKey)) {
                self::$apiKey = $config['api_key'] ?? '';
            }
            
            if (empty(self::$apiKey)) {
                return [
                    'status' => 'error',
                    'message' => 'API Key تنظیم نشده است',
                    'code' => 'missing_api_key'
                ];
            }
            
            $client = self::getClient();
            $messageId = $client->sendPattern($pattern, $parentNumber, $to, $data);
            
            return [
                'status' => 'success',
                'message' => 'SMS با موفقیت ارسال شد',
                'message_id' => $messageId,
                'code' => 'sent'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 'exception',
                'exception' => get_class($e)
            ];
        }
    }

    private static function setApiKey(string $apiKey): object
    {
        self::$apiKey = $apiKey;
        return self::get();
    }
    private static function setClient(Client $client): object
    {
        self::$client = $client;
        return self::get();
    }
    private static function getClient(): Client
    {
        if (self::$client === null) {
            $apiKey = self::$apiKey;
            if (empty($apiKey)) {
                // Try to get from config if apiKey is empty
                $instance = static::get();
                $config = $instance->getConfig();
                $apiKey = isset($config['api_key']) ? $config['api_key'] : '';
            }
            self::$client = new Client($apiKey);
        }
        return self::$client;
    }
}