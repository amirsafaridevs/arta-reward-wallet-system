<?php

namespace ArtaRewardWalletSystem\Service\WooCommerce;

use ArtaRewardWalletSystem\Contract\Abstract\AbstractService;

class AccountDetails extends AbstractService
{
    public function boot(): void
    {
        // Add custom fields to edit account form
        add_action('woocommerce_edit_account_form_start', [$this, 'addCustomFields']);
        
        // Hide disabled fields
        add_action('woocommerce_edit_account_form_start', [$this, 'hideDisabledFields'], 1);
        
        // Modify existing fields requirements
        add_filter('woocommerce_save_account_details_required_fields', [$this, 'modifyRequiredFields'], 10, 1);
        
        // Save custom fields
        add_action('woocommerce_save_account_details', [$this, 'saveCustomFields'], 10, 1);
        
        // Check and add wallet credit if all fields are filled
        add_action('woocommerce_save_account_details', [$this, 'checkAndAddWalletCredit'], 20, 1);
        
        // Display notification
        add_action('woocommerce_account_edit-account_endpoint', [$this, 'displayNotification'], 5);

        
        
        // Display incomplete profile banner on all my-account pages
        add_action('woocommerce_before_my_account', [$this, 'displayIncompleteProfileBanner'], 10);
        add_action('woocommerce_after_my_account', [$this, 'displayIncompleteProfileBanner'], 1);
        
        // Add registration bonus
        add_action('user_register', [$this, 'addRegistrationBonus'], 10, 1);
    }

    /**
     * Hide disabled fields using CSS
     */
    public function hideDisabledFields(): void
    {
        $accountFields = get_option('arta_account_fields', []);
        
        if (empty($accountFields)) {
            return;
        }

        $disabledFields = [];
        foreach ($accountFields as $fieldKey => $fieldData) {
            if (isset($fieldData['enabled']) && !$fieldData['enabled']) {
                $selectors = $this->getFieldSelectors($fieldKey);
                if (!empty($selectors)) {
                    $disabledFields = array_merge($disabledFields, $selectors);
                }
            }
        }

        if (!empty($disabledFields)) {
            echo '<style id="arta-hide-fields">';
            foreach ($disabledFields as $selector) {
                if (!empty($selector)) {
                    echo $selector . ' { display: none !important; }';
                }
            }
            echo '</style>';
            
            // Also use JavaScript as fallback
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var fieldsToHide = ' . json_encode($this->getDisabledFieldIds($accountFields)) . ';
                fieldsToHide.forEach(function(fieldId) {
                    var field = document.getElementById(fieldId);
                    if (field) {
                        var parent = field.closest("p");
                        if (parent) {
                            parent.style.display = "none";
                        }
                    }
                });
            });
            </script>';
        }
    }

    /**
     * Get CSS selectors for field
     */
    private function getFieldSelectors($fieldKey): array
    {
        $selectors = [
            'account_first_name' => [
                '.woocommerce-EditAccountForm .woocommerce-form-row--first',
                '.woocommerce-EditAccountForm p:has(label[for="account_first_name"])',
                '.woocommerce-EditAccountForm input#account_first_name',
            ],
            'account_last_name' => [
                '.woocommerce-EditAccountForm .woocommerce-form-row--last',
                '.woocommerce-EditAccountForm p:has(label[for="account_last_name"])',
                '.woocommerce-EditAccountForm input#account_last_name',
            ],
            'account_display_name' => [
                '.woocommerce-EditAccountForm .woocommerce-form-row--display-name',
                '.woocommerce-EditAccountForm p:has(label[for="account_display_name"])',
                '.woocommerce-EditAccountForm input#account_display_name',
            ],
            'account_email' => [
                '.woocommerce-EditAccountForm .woocommerce-form-row--email',
                '.woocommerce-EditAccountForm p:has(label[for="account_email"])',
                '.woocommerce-EditAccountForm input#account_email',
            ],
        ];
        
        return $selectors[$fieldKey] ?? [];
    }

    /**
     * Get disabled field IDs
     */
    private function getDisabledFieldIds($accountFields): array
    {
        $fieldIds = [
            'account_first_name' => 'account_first_name',
            'account_last_name' => 'account_last_name',
            'account_display_name' => 'account_display_name',
            'account_email' => 'account_email',
        ];
        
        $disabled = [];
        foreach ($accountFields as $fieldKey => $fieldData) {
            if (isset($fieldData['enabled']) && !$fieldData['enabled']) {
                if (isset($fieldIds[$fieldKey])) {
                    $disabled[] = $fieldIds[$fieldKey];
                }
            }
        }
        
        return $disabled;
    }

    /**
     * Add custom fields to edit account form
     */
    public function addCustomFields(): void
    {
        $customFields = get_option('arta_custom_account_fields', []);
        
        if (empty($customFields)) {
            return;
        }

        $user = wp_get_current_user();
        
        foreach ($customFields as $field) {
            $fieldName = 'arta_' . $field['name'];
            $value = get_user_meta($user->ID, $fieldName, true);
            
            $required = !empty($field['required']) ? ' <span class="required">*</span>' : '';
            
            echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
            echo '<label for="' . esc_attr($fieldName) . '">' . esc_html($field['label']) . $required . '</label>';
            
            switch ($field['type']) {
                case 'textarea':
                    echo '<textarea name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" class="woocommerce-Input woocommerce-Input--text input-text" rows="4">' . esc_textarea($value) . '</textarea>';
                    break;
                    
                case 'select':
                    echo '<select name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" class="woocommerce-Input woocommerce-Input--select input-select">';
                    echo '<option value="">انتخاب کنید</option>';
                    // You can add options here if needed
                    echo '</select>';
                    break;
                    
                case 'checkbox':
                    echo '<input type="checkbox" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="1" ' . checked($value, '1', false) . ' class="woocommerce-Input woocommerce-Input--checkbox input-checkbox">';
                    break;
                    
                case 'radio':
                    // Radio buttons would need options - simplified for now
                    echo '<input type="radio" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="1" ' . checked($value, '1', false) . ' class="woocommerce-Input woocommerce-Input--radio input-radio">';
                    break;
                    
                case 'date':
                    echo '<input type="date" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="' . esc_attr($value) . '" class="woocommerce-Input woocommerce-Input--text input-text" />';
                    break;
                    
                case 'number':
                    echo '<input type="number" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="' . esc_attr($value) . '" class="woocommerce-Input woocommerce-Input--text input-text" />';
                    break;
                    
                case 'email':
                    echo '<input type="email" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="' . esc_attr($value) . '" class="woocommerce-Input woocommerce-Input--email input-text" />';
                    break;
                    
                case 'tel':
                    echo '<input type="tel" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="' . esc_attr($value) . '" class="woocommerce-Input woocommerce-Input--text input-text" />';
                    break;
                    
                case 'url':
                    echo '<input type="url" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="' . esc_attr($value) . '" class="woocommerce-Input woocommerce-Input--text input-text" />';
                    break;
                    
                case 'password':
                    echo '<input type="password" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" class="woocommerce-Input woocommerce-Input--password input-text" />';
                    break;
                    
                default: // text
                    echo '<input type="text" name="' . esc_attr($fieldName) . '" id="' . esc_attr($fieldName) . '" value="' . esc_attr($value) . '" class="woocommerce-Input woocommerce-Input--text input-text" />';
                    break;
            }
            
            echo '</p>';
        }
    }

    /**
     * Modify required fields based on admin settings
     */
    public function modifyRequiredFields($requiredFields): array
    {
        $accountFields = get_option('arta_account_fields', []);
        
        if (empty($accountFields)) {
            return $requiredFields;
        }
        
        // Get all default field keys
        $defaultFieldKeys = ['account_first_name', 'account_last_name', 'account_display_name', 'account_email'];
        
        // Process each field based on settings
        foreach ($defaultFieldKeys as $fieldKey) {
            // Skip if field is not in accountFields settings (use default behavior)
            if (!isset($accountFields[$fieldKey])) {
                continue;
            }
            
            $fieldData = $accountFields[$fieldKey];
            
            // If field is disabled, remove from required
            if (isset($fieldData['enabled']) && !$fieldData['enabled']) {
                unset($requiredFields[$fieldKey]);
                continue;
            }
            
            // If field is explicitly set as not required, remove from required
            if (isset($fieldData['required']) && !$fieldData['required']) {
                unset($requiredFields[$fieldKey]);
                continue;
            }
            
            // If field is explicitly set as required, ensure it's in required list
            if (isset($fieldData['required']) && $fieldData['required']) {
                $requiredFields[$fieldKey] = $this->getFieldLabel($fieldKey);
            }
        }
        
        return $requiredFields;
    }

    /**
     * Save custom fields to user meta
     */
    public function saveCustomFields($user_id): void
    {
        $customFields = get_option('arta_custom_account_fields', []);
        
        if (empty($customFields)) {
            return;
        }

        foreach ($customFields as $field) {
            $fieldName = 'arta_' . $field['name'];
            
            if (isset($_POST[$fieldName])) {
                $value = sanitize_text_field($_POST[$fieldName]);
                
                // Handle different field types
                if ($field['type'] === 'textarea') {
                    $value = sanitize_textarea_field($_POST[$fieldName]);
                } elseif ($field['type'] === 'email') {
                    $value = sanitize_email($_POST[$fieldName]);
                } elseif ($field['type'] === 'url') {
                    $value = esc_url_raw($_POST[$fieldName]);
                } elseif ($field['type'] === 'number') {
                    $value = floatval($_POST[$fieldName]);
                } elseif ($field['type'] === 'checkbox') {
                    $value = isset($_POST[$fieldName]) ? '1' : '0';
                }
                
                update_user_meta($user_id, $fieldName, $value);
            }
        }
    }

    /**
     * Check if all fields are filled and add wallet credit
     */
    public function checkAndAddWalletCredit($user_id): void
    {
        // Check if user already received the bonus
        $bonusReceived = get_user_meta($user_id, 'arta_profile_completion_bonus_received', true);
        
        if ($bonusReceived) {
            return; // Already received bonus
        }

        // Get settings
        $settings = get_option('arta_completion_bonus_amount', 0);
        
        if (empty($settings) || $settings <= 0) {
            return; // No bonus configured
        }

        // Check if all required fields are filled
        if (!$this->areAllFieldsFilled($user_id)) {
            return; // Not all fields filled
        }

        // Check if woo-wallet is active
        if (!function_exists('woo_wallet')) {
            return;
        }

        // Add credit to wallet
        $amount = floatval($settings);
        $transaction_id = woo_wallet()->wallet->credit($user_id, $amount, __('پاداش تکمیل پروفایل', 'arta-reward-wallet-system'));
        
        if ($transaction_id) {
            // Mark bonus as received
            update_user_meta($user_id, 'arta_profile_completion_bonus_received', true);
            update_user_meta($user_id, 'arta_profile_completion_bonus_amount', $amount);
            update_user_meta($user_id, 'arta_profile_completion_bonus_date', current_time('mysql'));
            
            // Set notification
            set_transient('arta_wallet_notification_' . $user_id, [
                'type' => 'success',
                'message' => sprintf(__('تبریک! مبلغ %s تومان به کیف پول شما اضافه شد.', 'arta-reward-wallet-system'), number_format($amount))
            ], 300); // 5 minutes
        }
    }

    /**
     * Check if all required fields are filled
     */
    private function areAllFieldsFilled($user_id): bool
    {
        // Check default account fields
        $accountFields = get_option('arta_account_fields', []);
        $defaultFields = [
            'account_first_name' => 'first_name',
            'account_last_name' => 'last_name',
            'account_display_name' => 'display_name',
            'account_email' => 'user_email',
        ];

        $user = get_userdata($user_id);
        
        if (!$user) {
            return false;
        }

        // If no account fields configured, check default required fields
        if (empty($accountFields)) {
            // Check default WooCommerce required fields
            if (empty($user->first_name) || empty($user->last_name) || empty($user->user_email)) {
                return false;
            }
            return true;
        }
        
        foreach ($accountFields as $fieldKey => $fieldData) {
            if (isset($fieldData['enabled']) && !$fieldData['enabled']) {
                continue; // Skip disabled fields
            }
            
            if (isset($fieldData['required']) && $fieldData['required']) {
                $metaKey = $defaultFields[$fieldKey] ?? '';
                if ($metaKey) {
                    $value = $user->$metaKey ?? '';
                    if (empty($value)) {
                        return false;
                    }
                }
            }
        }

        // Check custom fields
        $customFields = get_option('arta_custom_account_fields', []);
        
        foreach ($customFields as $field) {
            if (!empty($field['required'])) {
                $fieldName = 'arta_' . $field['name'];
                $value = get_user_meta($user_id, $fieldName, true);
                
                if (empty($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Display notification
     */
    public function displayNotification(): void
    {
        $user_id = get_current_user_id();
        $notification = get_transient('arta_wallet_notification_' . $user_id);
        
        if ($notification) {
            delete_transient('arta_wallet_notification_' . $user_id);
            
            $class = $notification['type'] === 'success' ? 'woocommerce-message' : 'woocommerce-error';
            echo '<div class="' . esc_attr($class) . '" style="margin-bottom: 20px;">';
            echo esc_html($notification['message']);
            echo '</div>';
        }
    }

    /**
     * Display incomplete profile banner
     */
    public function displayIncompleteProfileBanner(): void
    {

        echo '<!-- displayIncompleteProfileBanner -->';
        echo '<!-- user_id: ' . $user_id . ' -->';
        die();
        // Only show once per page load
        static $shown = false;
        if ($shown) {
            return;
        }
        $shown = true;

        $user_id = get_current_user_id();
        
        if (!$user_id) {
            return;
        }

        // Check if user already received the bonus (profile is complete)
        $bonusReceived = get_user_meta($user_id, 'arta_profile_completion_bonus_received', true);
        
        if ($bonusReceived) {
            return; // Profile is complete, don't show banner
        }

        // Check if all required fields are filled
        if ($this->areAllFieldsFilled($user_id)) {
            return; // All fields filled, don't show banner
        }

        // Get notification message from settings
        $message = get_option('arta_profile_completion_message', 'با تکمیل اطلاعات حساب خود پاداش بگیرید');
        
        if (empty($message)) {
            $message = 'با تکمیل اطلاعات حساب خود پاداش بگیرید';
        }

        // Get edit account page URL
        $edit_account_url = wc_get_endpoint_url('edit-account', '', wc_get_page_permalink('myaccount'));

        // Display banner
        echo '<div class="arta-profile-completion-banner" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-right: 3px solid #f57c00; padding: 16px 20px; margin-bottom: 20px; border-radius: 4px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">';
        echo '<div style="flex: 1; color: #e65100; font-size: 14px; font-weight: 500;">';
        echo esc_html($message);
        echo '</div>';
        echo '<a href="' . esc_url($edit_account_url) . '" class="button" style="background: #f57c00; color: white; border: none; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; font-weight: 500; transition: background 0.2s ease;">';
        echo 'تکمیل اطلاعات';
        echo '</a>';
        echo '</div>';
    }

    /**
     * Add registration bonus to wallet
     */
    public function addRegistrationBonus($user_id): void
    {
        // Check if registration bonus is enabled
        $enableBonus = get_option('arta_enable_registration_bonus', 0);
        
        if (!$enableBonus) {
            return;
        }

        // Check if user already received the bonus
        $bonusReceived = get_user_meta($user_id, 'arta_registration_bonus_received', true);
        
        if ($bonusReceived) {
            return; // Already received bonus
        }

        // Get bonus amount
        $amount = floatval(get_option('arta_registration_bonus_amount', 0));
        
        if (empty($amount) || $amount <= 0) {
            return; // No bonus configured
        }

        // Check if woo-wallet is active
        if (!function_exists('woo_wallet')) {
            return;
        }

        // Add credit to wallet
        $transaction_id = woo_wallet()->wallet->credit($user_id, $amount, __('پاداش ثبت نام', 'arta-reward-wallet-system'));
        
        if ($transaction_id) {
            // Mark bonus as received
            update_user_meta($user_id, 'arta_registration_bonus_received', true);
            update_user_meta($user_id, 'arta_registration_bonus_amount', $amount);
            update_user_meta($user_id, 'arta_registration_bonus_date', current_time('mysql'));
        }
    }

    /**
     * Get field label
     */
    private function getFieldLabel($fieldKey): string
    {
        $labels = [
            'account_first_name' => 'نام',
            'account_last_name' => 'نام خانوادگی',
            'account_display_name' => 'نام نمایشی',
            'account_email' => 'ایمیل',
        ];
        
        return $labels[$fieldKey] ?? $fieldKey;
    }
}

