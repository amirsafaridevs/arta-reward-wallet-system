<?php

namespace ArtaRewardWalletSystem\Service\CheckDependency;


use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;

class wooWallet extends AbstractService
{
    public function boot(): void
    {
       $this->isActive();
    }
    public function isActive(): bool
    {
        if (!class_exists('wooWallet')) {
            throw new \Exception('WooCommerce is not installed');
        }
        return true;
    }
}