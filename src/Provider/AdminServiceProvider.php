<?php

namespace ArtaRewardWalletSystem\Provider;
use ArtaRewardWalletSystem\Contract\Abstract\AbstractServiceProvider;
use ArtaRewardWalletSystem\Service\Admin\MainMenu;
use ArtaRewardWalletSystem\Service\Admin\SettingMenu;
use ArtaRewardWalletSystem\Service\Admin\ImportUsers;
use ArtaRewardWalletSystem\Service\Admin\SmsLogs;
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
        $importUsers = $this->container->singleton('admin.importUsers', ImportUsers::class);
        $smsLogs = $this->container->singleton('admin.smsLogs', SmsLogs::class);

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

        $importUsers = $this->container->get('admin.importUsers');
        $importUsers->setContainer($this->container);
        $importUsers->boot();

        $smsLogs = $this->container->get('admin.smsLogs');
        $smsLogs->setContainer($this->container);
        $smsLogs->boot();
    }
}

