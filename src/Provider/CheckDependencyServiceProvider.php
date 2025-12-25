<?php
namespace ArtaRewardWalletSystem\Provider;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractServiceProvider;
use ArtaRewardWalletSystem\Service\CheckDependency\WooCommerce;
use ArtaRewardWalletSystem\Service\CheckDependency\wooWallet;
class CheckDependencyServiceProvider extends AbstractServiceProvider
{
    protected function registerServices(): void
    {
        $this->container->singleton('checkdependency.woocommerce', WooCommerce::class);
        $this->container->singleton('checkdependency.wooWallet', wooWallet::class);
    }
    protected function bootServices(): void
    {
        $this->container->get('checkdependency.woocommerce')->boot();
        $this->container->get('checkdependency.wooWallet')->boot();
    }
}
