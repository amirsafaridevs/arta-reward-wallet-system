<?php

namespace ArtaRewardWalletSystem\Provider;

use ArtaRewardWalletSystem\Controller\AdminController;
use ArtaRewardWalletSystem\Service\Admin\Menu;
use ArtaRewardWalletSystem\Contract\Abstract\AbstractServiceProvider;
/**
 * Admin Service Provider
 * 
 * Service provider for admin-related functionality
 */
class AdminServiceProvider extends AbstractServiceProvider
{
    /**
     * Register services
     *
     * @return void
     */
    protected function registerServices(): void
    {
        
        $menuService = $this->container->singleton('admin.menu', Menu::class);
        
    }

    /**
     * Boot services
     *
     * @return void
     */
    protected function bootServices(): void
    {
      
        $menuService = $this->container->get('admin.menu');
      
    }
}

