<?php
namespace ArtaRewardWalletSystem\Service\SmsGetway;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractSmsGetway;

class FarazSms extends AbstractSmsGetway
{
    protected static string $name = 'farazsms';
    protected static string $apiUrl = 'https://api.iranpayamak.com';


    public static function createPattern(): mixed
    {
        return [];
    }
    public static function getPatterns(): array
    {
        return [];
    }
    public static function getBalance(): mixed
    {
        return null;
    }
    public static function getProfile(): mixed
    {
            
    }
    public static function sendSms(): bool
    {
        return true;
    }
}