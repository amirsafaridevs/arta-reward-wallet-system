<?php
namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;
use ArtaRewardWalletSystem\Core\Application;

class SettingMenu extends AbstractService
{
    protected Application $application;

    public function boot(): void
    {
        $this->application = $this->getContainer()->get('app');
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_post_arta_save_settings', [$this, 'saveSettings']);
    }

    public function addMenu(): void
    {
        add_submenu_page(
            'arta-reward-wallet-system',
            'تنظیمات',
            'تنظیمات',
            'manage_options',
            'arta-reward-wallet-settings',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        // Get current settings
        $settings = $this->getSettings();
        
        // Get account fields
        $accountFields = $this->getAccountFields();
        
        // Render settings view
        Application::view('settings', [
            'settings' => $settings,
            'accountFields' => $accountFields
        ]);
    }

    public function saveSettings(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('دسترسی غیرمجاز');
        }

        check_admin_referer('arta_settings_nonce');

        // Save general settings
        $enableRegistrationBonus = isset($_POST['enable_registration_bonus']) ? 1 : 0;
        $registrationBonusAmount = isset($_POST['registration_bonus_amount']) ? floatval($_POST['registration_bonus_amount']) : 0;

        update_option('arta_enable_registration_bonus', $enableRegistrationBonus);
        update_option('arta_registration_bonus_amount', $registrationBonusAmount);

        // Save account fields settings
        if (isset($_POST['account_fields'])) {
            $fields = [];
            foreach ($_POST['account_fields'] as $fieldKey => $fieldData) {
                $fields[$fieldKey] = [
                    'required' => isset($fieldData['required']) ? 1 : 0,
                    'enabled' => isset($fieldData['enabled']) ? 1 : 0,
                ];
            }
            update_option('arta_account_fields', $fields);
        }

        // Save custom fields
        if (isset($_POST['custom_fields'])) {
            $customFields = [];
            foreach ($_POST['custom_fields'] as $index => $field) {
                if (!empty($field['label']) && !empty($field['name'])) {
                    $customFields[] = [
                        'label' => sanitize_text_field($field['label']),
                        'name' => sanitize_text_field($field['name']),
                        'type' => sanitize_text_field($field['type']),
                        'required' => isset($field['required']) ? 1 : 0,
                    ];
                }
            }
            update_option('arta_custom_account_fields', $customFields);
        }

        wp_redirect(add_query_arg(['page' => 'arta-reward-wallet-settings', 'updated' => '1'], admin_url('admin.php')));
        exit;
    }

    private function getSettings(): array
    {
        return [
            'enable_registration_bonus' => get_option('arta_enable_registration_bonus', 0),
            'registration_bonus_amount' => get_option('arta_registration_bonus_amount', 0),
        ];
    }

    private function getAccountFields(): array
    {
        // Default WooCommerce account fields
        $defaultFields = [
            'account_first_name' => [
                'label' => 'نام',
                'type' => 'text',
                'required' => true,
            ],
            'account_last_name' => [
                'label' => 'نام خانوادگی',
                'type' => 'text',
                'required' => true,
            ],
            'account_display_name' => [
                'label' => 'نام نمایشی',
                'type' => 'text',
                'required' => false,
            ],
            'account_email' => [
                'label' => 'ایمیل',
                'type' => 'email',
                'required' => true,
            ],
        ];

        // Get saved field settings
        $savedFields = get_option('arta_account_fields', []);

        // Merge with saved settings
        foreach ($defaultFields as $key => &$field) {
            if (isset($savedFields[$key])) {
                $field['required'] = $savedFields[$key]['required'] ?? $field['required'];
                $field['enabled'] = $savedFields[$key]['enabled'] ?? true;
            } else {
                $field['enabled'] = true;
            }
        }

        return $defaultFields;
    }
}