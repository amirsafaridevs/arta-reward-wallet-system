<?php
namespace ArtaRewardWalletSystem\Service\SmsGetway;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractSmsGetway;
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
  
    public static function sendSms(): bool
    {
        return true;
    }

    public static function setApiKey(string $apiKey): object
    {
        self::$apiKey = self::getConfig('api_key');
        return self::get();
    }
    public static function setClient(Client $client): object
    {
        self::$client = $client;
        return self::get();
    }
    public static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client(self::$apiKey);
        }
        return self::$client;
    }
}