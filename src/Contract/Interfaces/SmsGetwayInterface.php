<?php
namespace ArtaRewardWalletSystem\Contract\Interfaces;

interface SmsGetwayInterface
{
    public function send(): bool;
    public function getConfig(): array;
    public function getMessage(): string;
    public function getTo(): string;
    public function getResponse(): array;
}