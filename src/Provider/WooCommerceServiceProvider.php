<?php
namespace ArtaRewardWalletSystem\Provider;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractServiceProvider;
use ArtaRewardWalletSystem\Service\WooCommerce\AccountDetails;
class WooCommerceServiceProvider extends AbstractServiceProvider
{
    /**
     * Register services
     *
     * @return void
     */
    protected function registerServices(): void
    {
        $accountDetails = $this->container->singleton('woocommerce.accountdetails', AccountDetails::class);
    }
    protected function bootServices(): void
    {
        $accountDetails = $this->container->get('woocommerce.accountdetails');
        $accountDetails->setContainer($this->container);
        $accountDetails->boot();
    }
}