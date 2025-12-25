<?php
namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;

class SettingMenu extends AbstractService
{
    public function boot(): void
    {
        //add_action('admin_menu', [$this, 'addMenu']);
    }
}