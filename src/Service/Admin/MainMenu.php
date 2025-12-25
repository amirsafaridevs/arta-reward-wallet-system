<?php
namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;
use ArtaRewardWalletSystem\Core\Application;

class MainMenu extends AbstractService
{
    protected Application $application;
    public function boot(): void
    {
        // Hook into admin_menu to ensure WordPress is fully loaded
        add_action('admin_menu', [$this, 'addMenu']);
    }
    public function addMenu(): void
    {
        add_menu_page(
            ' امتیازات و شارژ کیف پول',
            ' امتیازات و شارژ کیف پول',
            'manage_options',
            'arta-reward-wallet-system',
            [$this, 'render'],
        );
    }

    public function render(): void
    {
        // Get statistics
        $stats = $this->getStatistics();
        
        // Render dashboard view
        Application::view('dashboard', [
            'stats' => $stats
        ]);
    }

    private function getStatistics(): array
    {
        // Get total users count
        $totalUsers = count_users();
        $totalUsersCount = $totalUsers['total_users'];

        // Get customer users count (WooCommerce customers)
        $customerCount = 0;
        if (class_exists('WooCommerce')) {
            $customerUsers = get_users([
                'role' => 'customer',
                'count_total' => true
            ]);
            $customerCount = is_array($customerUsers) ? count($customerUsers) : 0;
        }

        // Get users with subscriber role
        $subscriberCount = isset($totalUsers['avail_roles']['subscriber']) 
            ? $totalUsers['avail_roles']['subscriber'] 
            : 0;

        return [
            'total_users' => $totalUsersCount,
            'customers' => $customerCount,
            'subscribers' => $subscriberCount,
        ];
    }
}