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
        $this->application->registerProvider(CheckDependencyServiceProvider::class);
        $this->application->registerProvider(AdminServiceProvider::class);
        $this->application->registerProvider(WooCommerceServiceProvider::class);
        $this->application->boot();
    }

    
}
