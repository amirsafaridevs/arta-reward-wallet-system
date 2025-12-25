<?php

namespace ArtaRewardWalletSystem\App;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractSingleton;
use ArtaRewardWalletSystem\Core\Application;
use ArtaRewardWalletSystem\Provider\AdminServiceProvider;
use ArtaRewardWalletSystem\Provider\CheckDependencyServiceProvider;
use ArtaRewardWalletSystem\Provider\WooCommerceServiceProvider;
/**
 * App Class
 * 
 * Main application class for Easy Stock and Price Control plugin
 */
class App extends AbstractSingleton
{

    /**
     * Service registry instance
     *
     * @var 
     */
    protected  $providers = [];
    /**
     * Application instance
     *
     * @var Application
     */
    protected Application $application;

    /**
     * Get the singleton instance
     *
     * @return self
     */
    public function __construct() {
        $this->application = Application::get();
        $this->application->setProperty('basePath', plugin_dir_path(__FILE__));
        $this->application->setProperty('version', '0.0.1');
        $this->init();
    }

    /**
     * Initialize the application
     *
     * @return void
     */
    private function init(): void
    {
        // First, register and boot dependency check service provider
        $this->application->registerProvider(CheckDependencyServiceProvider::class);
        $this->application->boot();

        // Check if dependencies are active
        if ($this->areDependenciesActive()) {
            // Register and boot other service providers only if dependencies are active
            $this->application->registerProvider(AdminServiceProvider::class);
            $this->application->registerProvider(WooCommerceServiceProvider::class);
            $this->application->boot();
        }
    }

    /**
     * Check if all required dependencies are active
     *
     * @return bool
     */
    private function areDependenciesActive(): bool
    {
        // Check if dependency check failed
        $dependencyCheckFailed = $this->application->getProperty('dependencyCheckFailed', false);
        
        if ($dependencyCheckFailed) {
            return false;
        }

        // Check WooCommerce service
        $wooCommerceActive = false;
        try {
            $wooCommerceService = $this->application->getContainer()->get('checkdependency.woocommerce');
            if (method_exists($wooCommerceService, 'isActive')) {
                $wooCommerceActive = $wooCommerceService->isActive();
            }
        } catch (\Exception $e) {
            // If service is not available, assume dependencies are not active
            return false;
        }

        // Check wooWallet (Tera Wallet) service
        $wooWalletActive = false;
        try {
            $wooWalletService = $this->application->getContainer()->get('checkdependency.wooWallet');
            if (method_exists($wooWalletService, 'isActive')) {
                $wooWalletActive = $wooWalletService->isActive();
            }
        } catch (\Exception $e) {
            // If service is not available, assume dependencies are not active
            return false;
        }

        // Both dependencies must be active
        return $wooCommerceActive && $wooWalletActive;
    }

    
}
