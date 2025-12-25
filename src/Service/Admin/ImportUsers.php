<?php
namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;
use ArtaRewardWalletSystem\Core\Application;
use ArtaRewardWalletSystem\Helper\Sms;

class ImportUsers extends AbstractService
{
    public function boot(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('wp_ajax_arta_import_users', [$this, 'handleImportUsers']);
    }

    public function addMenu(): void
    {
        add_submenu_page(
            'arta-reward-wallet-system',
            'وارد کردن کاربران',
            'وارد کردن کاربران',
            'manage_options',
            'arta-import-users',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        Application::view('import-users');
    }

    public function handleImportUsers(): void
    {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'arta_import_users_nonce')) {
            wp_send_json_error(['message' => 'خطای امنیتی']);
            return;
        }

        // Check capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'دسترسی غیرمجاز']);
            return;
        }

        // Get users data
        $users = isset($_POST['users']) ? json_decode(stripslashes($_POST['users']), true) : [];

        if (empty($users) || !is_array($users)) {
            wp_send_json_error(['message' => 'اطلاعات کاربران نامعتبر است']);
            return;
        }

        // Get SMS option
        $sendSms = isset($_POST['send_sms']) && $_POST['send_sms'] === '1';

        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($users as $userData) {
            $name = isset($userData['name']) ? sanitize_text_field($userData['name']) : '';
            $phone = isset($userData['phone']) ? sanitize_text_field($userData['phone']) : '';

            if (empty($name) || empty($phone)) {
                $results['failed'][] = [
                    'name' => $name,
                    'phone' => $phone,
                    'reason' => 'نام یا شماره تلفن خالی است'
                ];
                continue;
            }

            // Check if user with this phone already exists
            $existingUser = get_users([
                'meta_key' => 'digits_phone',
                'meta_value' => $phone,
                'number' => 1
            ]);

            if (!empty($existingUser)) {
                $results['failed'][] = [
                    'name' => $name,
                    'phone' => $phone,
                    'reason' => 'کاربر با این شماره تلفن قبلاً ثبت شده است'
                ];
                continue;
            }

            // Generate username from phone (exactly the phone number)
            $username = preg_replace('/[^0-9]/', '', $phone);
            
            // Check if username exists
            if (username_exists($username)) {
                $username = $username . '_' . time();
            }

            // Generate password
            $password = wp_generate_password(12, false);

            // Create user
            $user_id = wp_create_user($username, $password, '');

            if (is_wp_error($user_id)) {
                $results['failed'][] = [
                    'name' => $name,
                    'phone' => $phone,
                    'reason' => $user_id->get_error_message()
                ];
                continue;
            }

            // Set user role to customer
            $user = new \WP_User($user_id);
            $user->set_role('customer');

            // Update user display name
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $name
            ]);

            // Save phone number in all digits-related meta fields
            update_user_meta($user_id, 'digits_phone', $phone);
            update_user_meta($user_id, 'digt_phone_no', $phone);
            update_user_meta($user_id, 'digits_phone_no', $phone);
            update_user_meta($user_id, 'billing_phone', $phone);
            update_user_meta($user_id, 'shipping_phone', $phone);

            // Save name
            update_user_meta($user_id, 'first_name', $name);
            update_user_meta($user_id, 'billing_first_name', $name);
            update_user_meta($user_id, 'shipping_first_name', $name);

            // Send SMS if enabled
            if ($sendSms) {
                try {
                    $smsMessage = get_option('arta_sms_welcome_message', 'خوش آمدید! حساب کاربری شما با موفقیت ایجاد شد.');
                    if (empty($smsMessage)) {
                        $smsMessage = 'خوش آمدید! حساب کاربری شما با موفقیت ایجاد شد.';
                    }
                    
                    // Replace placeholders
                    $smsMessage = str_replace('{name}', $name, $smsMessage);
                    $smsMessage = str_replace('{username}', $username, $smsMessage);
                    $smsMessage = str_replace('{password}', $password, $smsMessage);
                    
                    // Send SMS using helper class
                    // Note: send is static method
                    Sms::send($phone, $smsMessage);
                } catch (\Exception $e) {
                    // Log error but don't fail the import
                    error_log('SMS sending failed for user ' . $user_id . ': ' . $e->getMessage());
                }
            }

            $results['success'][] = [
                'name' => $name,
                'phone' => $phone,
                'user_id' => $user_id
            ];
        }

        wp_send_json_success([
            'message' => 'کاربران با موفقیت ثبت شدند',
            'results' => $results
        ]);
    }
}