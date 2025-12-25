<?php

namespace ArtaRewardWalletSystem\Service\CheckDependency;


use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;
use ArtaRewardWalletSystem\Core\Application;

class WooCommerce extends AbstractService
{
    /**
     * Flag to track if WooCommerce is active
     *
     * @var bool
     */
    private static bool $isWooCommerceActive = false;

    public function boot(): void
    {
        if (!$this->isActive()) {
            $this->showAdminNotice();
            $this->preventSystemExecution();
        } else {
            self::$isWooCommerceActive = true;
        }
    }

    /**
     * Check if WooCommerce is installed and active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        // Check if WooCommerce class exists (this means WooCommerce is loaded)
        if (!class_exists('WooCommerce')) {
            return false;
        }

        // Additional check: verify WooCommerce main function exists
        if (!function_exists('WC')) {
            return false;
        }

        // Check if WooCommerce is active using WordPress function
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        // Check if WooCommerce plugin is active (try common paths)
        $woocommerce_paths = [
            'woocommerce/woocommerce.php',
            'WooCommerce/woocommerce.php',
        ];

        $isActive = false;
        foreach ($woocommerce_paths as $path) {
            if (is_plugin_active($path)) {
                $isActive = true;
                break;
            }
        }

        // If plugin check fails but class exists, assume it's active (might be loaded differently)
        if (!$isActive && class_exists('WooCommerce') && function_exists('WC')) {
            $isActive = true;
        }

        return $isActive;
    }

    /**
     * Show admin notice if WooCommerce is not active
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
                __('برای استفاده از این افزونه نیاز به نصب و فعال‌سازی افزونه WooCommerce می‌باشد. لطفاً ابتدا WooCommerce را نصب و فعال کنید.', 'arta-reward-wallet-system')
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
     * Get WooCommerce active status
     *
     * @return bool
     */
    public static function isWooCommerceActive(): bool
    {
        return self::$isWooCommerceActive;
    }
}