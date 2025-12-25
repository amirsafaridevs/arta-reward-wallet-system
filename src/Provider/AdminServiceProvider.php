<?php

namespace ArtaRewardWalletSystem\Provider;

use ArtaRewardWalletSystem\Controller\AdminController;
use ArtaRewardWalletSystem\Service\Admin\MainMenu;
use ArtaRewardWalletSystem\Service\Admin\SettingMenu;
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
        
        $mainMenu = $this->container->singleton('admin.mainmenu', MainMenu::class);
        $settingMenu = $this->container->singleton('admin.settingmenu', SettingMenu::class);
    }

    /**
     * Boot services
     *
     * @return void
     */
    protected function bootServices(): void
    {
      
        $mainMenu = $this->container->get('admin.mainmenu');
        $mainMenu->setContainer($this->container);
        $mainMenu->boot();
        
        $settingMenu = $this->container->get('admin.settingmenu');
        $settingMenu->setContainer($this->container);
        $settingMenu->boot();
    }
}

