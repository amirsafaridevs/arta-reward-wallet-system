<?php

namespace ArtaRewardWalletSystem\Contract\Abstracts;

use ArtaRewardWalletSystem\Contract\Interface\SmsGetwayInterface;

abstract class AbstractSmsGetway implements SmsGetwayInterface
{
    protected static $instance = null;
    protected static array $config = [];
    protected static string $message = '';
    protected static string $to = '';
    protected static array $response = [];
    protected static string $gatewayName = '';
    protected static string $apiUrl = '';


    abstract public static function sendSms(): array;
    
    // Interface method implementation
    public function send(): bool
    {
        $result = static::sendSms();
        return isset($result['status']) && $result['status'] === 'success';
    }
    
    public static function createPattern(): array
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
        return [];
    }

    
    public static function get(): object
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    public static function setConfig(string $key, mixed $value): object
    {
        self::$config[$key] = $value;
        return self::get();
    }
    public static function setGatewayName(string $gatewayName): object
    {
        self::$gatewayName = $gatewayName;
        return self::get();
    }
    public static function getGatewayName(): string
    {
        return self::$gatewayName;
    }
    public static function setMessage(string $message): object
    {
        self::$message = $message;
        return self::get();
    }
    public static function setTo(string $to): object
    {
        self::$to = $to;
        return self::get();
    }
    public static function setResponse(array $response): object
    {
        self::$response = $response;
        return self::get();
    }

    public function getConfig(): array
    {
        return self::$config;
    }
    
    public function getMessage(): string
    {
        return self::$message;
    }
    
    public function getTo(): string
    {
        return self::$to;
    }
    
    public function getResponse(): array
    {
        return self::$response;
    }

 
}