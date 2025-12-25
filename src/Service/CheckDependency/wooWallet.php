<?php

namespace ArtaRewardWalletSystem\Service\CheckDependency;


use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;
use ArtaRewardWalletSystem\Core\Application;

class wooWallet extends AbstractService
{
    /**
     * Flag to track if Tera Wallet is active
     *
     * @var bool
     */
    private static bool $isWooWalletActive = false;

    public function boot(): void
    {
        if (!$this->isActive()) {
            $this->showAdminNotice();
            $this->preventSystemExecution();
        } else {
            self::$isWooWalletActive = true;
        }
    }

    /**
     * Check if Tera Wallet (wooWallet) is installed and active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        // Check if Tera Wallet plugin is active using WordPress function
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        // Check if Tera Wallet plugin is active (try common paths)
        $wooWallet_paths = [
            'woo-wallet/woo-wallet.php',
            'wooWallet/wooWallet.php',
            'tera-wallet/tera-wallet.php',
            'terawallet/terawallet.php',
        ];

        $isActive = false;
        foreach ($wooWallet_paths as $path) {
            if (is_plugin_active($path)) {
                $isActive = true;
                break;
            }
        }

        // Check if Tera Wallet class exists (common class names)
        $wooWallet_classes = [
            'WooWallet',
            'wooWallet',
            'TeraWallet',
            'Tera_Wallet',
            'Woo_Wallet',
        ];

        $classExists = false;
        foreach ($wooWallet_classes as $className) {
            if (class_exists($className)) {
                $classExists = true;
                break;
            }
        }

        // If plugin check fails but class exists, assume it's active
        if (!$isActive && $classExists) {
            $isActive = true;
        }

        // Additional check: verify common Tera Wallet functions exist
        if ($isActive && function_exists('woo_wallet')) {
            return true;
        }

        return $isActive;
    }

    /**
     * Show admin notice if Tera Wallet is not active
     *
     * @return void
     */
    private function showAdminNotice(): void
    {
        add_action('admin_notices', function () {
            $class = 'notice notice-error';
            $message = sprintf(
                '<strong>%s:</strong> %s',
                __('افزونه امتیازات و شارژ کیف پول', 'arta-reward-wallet-system'),
                __('برای استفاده از این افزونه نیاز به نصب و فعال‌سازی افزونه Tera Wallet (wooWallet) می‌باشد. لطفاً ابتدا Tera Wallet را نصب و فعال کنید.', 'arta-reward-wallet-system')
            );
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
        });
    }

    /**
     * Prevent system execution by setting a flag in Application
     *
     * @return void
     */
    private function preventSystemExecution(): void
    {
        try {
            $app = Application::get();
            $app->setProperty('dependencyCheckFailed', true);
        } catch (\Exception $e) {
            // If Application is not available, we'll handle it in App.php
        }
    }

    /**
     * Get Tera Wallet active status
     *
     * @return bool
     */
    public static function isWooWalletActive(): bool
    {
        return self::$isWooWalletActive;
    }
}