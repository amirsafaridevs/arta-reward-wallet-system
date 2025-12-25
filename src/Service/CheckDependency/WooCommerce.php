<?php

namespace ArtaRewardWalletSystem\Service\CheckDependency;


use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;

class WooCommerce extends AbstractService
{
    public function boot(): void
    {
       $this->isActive();
    }
    public function isActive(): bool
    {
        if (!class_exists('WooCommerce')) {
            throw new \Exception('WooCommerce is not installed');
        }
        return true;
    }
}