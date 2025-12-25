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
        $completionBonusAmount = isset($_POST['completion_bonus_amount']) ? floatval($_POST['completion_bonus_amount']) : 0;
        $profileCompletionMessage = isset($_POST['profile_completion_message']) ? sanitize_text_field($_POST['profile_completion_message']) : '';

        update_option('arta_enable_registration_bonus', $enableRegistrationBonus);
        update_option('arta_registration_bonus_amount', $registrationBonusAmount);
        update_option('arta_completion_bonus_amount', $completionBonusAmount);
        update_option('arta_profile_completion_message', $profileCompletionMessage);

        // Save account fields settings
        // Get default fields to ensure we save all of them
        $defaultFieldKeys = ['account_first_name', 'account_last_name', 'account_display_name', 'account_email'];
        
        if (isset($_POST['account_fields'])) {
            $fields = [];
            // Process all default fields
            foreach ($defaultFieldKeys as $fieldKey) {
                $fields[$fieldKey] = [
                    'required' => isset($_POST['account_fields'][$fieldKey]['required']) ? 1 : 0,
                    'enabled' => isset($_POST['account_fields'][$fieldKey]['enabled']) ? 1 : 0,
                ];
            }
            update_option('arta_account_fields', $fields);
        } else {
            // If account_fields not in POST, keep existing settings
            // This happens when user only changes general settings
            $existingFields = get_option('arta_account_fields', []);
            if (!empty($existingFields)) {
                update_option('arta_account_fields', $existingFields);
            }
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
            'completion_bonus_amount' => get_option('arta_completion_bonus_amount', 0),
            'profile_completion_message' => get_option('arta_profile_completion_message', 'با تکمیل اطلاعات حساب خود پاداش بگیرید'),
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
                // Use saved settings if they exist
                $field['required'] = isset($savedFields[$key]['required']) ? (bool)$savedFields[$key]['required'] : $field['required'];
                $field['enabled'] = isset($savedFields[$key]['enabled']) ? (bool)$savedFields[$key]['enabled'] : true;
            } else {
                // Use default values if no saved settings
                $field['enabled'] = true;
            }
        }

        return $defaultFields;
    }
}