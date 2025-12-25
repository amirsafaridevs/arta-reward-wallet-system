<?php
namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;
use ArtaRewardWalletSystem\Core\Application;

class SmsLogs extends AbstractService
{
    public function boot(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
    }

    public function addMenu(): void
    {
        add_submenu_page(
            'arta-reward-wallet-system',
            'لاگ‌های SMS',
            'لاگ‌های SMS',
            'manage_options',
            'arta-sms-logs',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        $logs = get_option('arta_sms_logs', []);
        if (!is_array($logs)) {
            $logs = [];
        }

        Application::view('sms-logs', [
            'logs' => $logs
        ]);
    }
}

