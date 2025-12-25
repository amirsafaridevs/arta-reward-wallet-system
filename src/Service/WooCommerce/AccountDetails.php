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

        // Display incomplete profile popup on all my-account pages
        add_action('wp_footer', [$this, 'displayIncompleteProfilePopup'], 999);
        
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

        // Track if we have any required fields to check
        $hasRequiredFields = false;

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
                $hasRequiredFields = true;
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
                $hasRequiredFields = true;
                $fieldName = 'arta_' . $field['name'];
                $value = get_user_meta($user_id, $fieldName, true);
                
                if (empty($value)) {
                    return false;
                }
            }
        }

        // If no required fields at all, profile is incomplete
        if (!$hasRequiredFields) {
            return false;
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
     * Display incomplete profile popup on my-account pages
     */
    public function displayIncompleteProfilePopup(): void
    {
        // Only show on my-account pages
        if (!is_account_page()) {
            return;
        }

        error_log('displayIncompleteProfilePopup');

        $user_id = get_current_user_id();
        
        if (!$user_id) {
            error_log('No user ID, don\'t show popup');
            return;
        }

        // Get completion bonus amount to check if feature is enabled
        $completionBonus = get_option('arta_completion_bonus_amount', 0);
        
        // If no bonus configured, don't show popup
        if (empty($completionBonus) || $completionBonus <= 0) {
            error_log('No bonus configured, don\'t show popup');
            return;
        }

        // Check if user already received the bonus (profile is complete)
        $bonusReceived = get_user_meta($user_id, 'arta_profile_completion_bonus_received', true);
        
        if ($bonusReceived) {
            error_log('Bonus received, don\'t show popup');
            return; // Profile is complete, don't show popup
        }

        // Check if all required fields are filled
        if ($this->areAllFieldsFilled($user_id)) {
            error_log('All fields filled, don\'t show popup');
            return; // All fields filled, don't show popup
        }

        // Get notification message from settings
        $message = get_option('arta_profile_completion_message', 'با تکمیل اطلاعات حساب خود پاداش بگیرید');
       
        // Get edit account page URL
        $edit_account_url = wc_get_endpoint_url('edit-account', '', wc_get_page_permalink('myaccount'));
        
        // Format bonus amount
        $bonusFormatted = wc_price($completionBonus);

        // Display popup with inline CSS and JavaScript
        ?>
        <style>
            #arta-profile-popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 999999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            
            #arta-profile-popup-overlay.show {
                opacity: 1;
                visibility: visible;
            }
            
            .arta-profile-popup {
                background: white;
                border-radius: 8px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                max-width: 500px;
                width: 90%;
                position: relative;
                transform: translateY(-20px);
                transition: transform 0.3s ease;
                overflow: hidden;
            }
            
            #arta-profile-popup-overlay.show .arta-profile-popup {
                transform: translateY(0);
            }
            
            .arta-popup-header {
                background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
                padding: 24px;
                text-align: center;
                border-bottom: 3px solid #f57c00;
            }
            
            .arta-popup-icon {
                width: 60px;
                height: 60px;
                background: #f57c00;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                box-shadow: 0 4px 12px rgba(245, 124, 0, 0.3);
            }
            
            .arta-popup-icon svg {
                width: 32px;
                height: 32px;
                fill: white;
            }
            
            .arta-popup-title {
                color: #e65100;
                font-size: 18px;
                font-weight: 700;
                margin: 0 0 8px 0;
            }
            
            .arta-popup-bonus {
                color: #f57c00;
                font-size: 24px;
                font-weight: 800;
                margin: 0;
            }
            
            .arta-popup-content {
                padding: 24px;
                text-align: center;
            }
            
            .arta-popup-message {
                color: #555;
                font-size: 15px;
                line-height: 1.6;
                margin-bottom: 24px;
            }
            
            .arta-popup-actions {
                display: flex;
                gap: 12px;
                justify-content: center;
            }
            
            .arta-popup-btn {
                padding: 12px 24px;
                border-radius: 4px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s ease;
                cursor: pointer;
                border: none;
                display: inline-block;
            }
            
            .arta-popup-btn-primary {
                background: #f57c00;
                color: white;
            }
            
            .arta-popup-btn-primary:hover {
                background: #e65100;
                box-shadow: 0 4px 12px rgba(245, 124, 0, 0.3);
            }
            
            .arta-popup-btn-secondary {
                background: #f5f5f5;
                color: #666;
            }
            
            .arta-popup-btn-secondary:hover {
                background: #e0e0e0;
            }
            
            .arta-popup-close {
                position: absolute;
                top: 12px;
                left: 12px;
                width: 32px;
                height: 32px;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                z-index: 1;
            }
            
            .arta-popup-close:hover {
                background: white;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }
            
            .arta-popup-close svg {
                width: 16px;
                height: 16px;
                fill: #666;
            }
            
            @media (max-width: 600px) {
                .arta-profile-popup {
                    width: 95%;
                }
                
                .arta-popup-actions {
                    flex-direction: column;
                }
                
                .arta-popup-btn {
                    width: 100%;
                }
            }
        </style>
        
        <div id="arta-profile-popup-overlay">
            <div class="arta-profile-popup">
                <button class="arta-popup-close" onclick="artaCloseProfilePopup()">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
                
                <div class="arta-popup-header">
                    <div class="arta-popup-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="arta-popup-title">تکمیل پروفایل و دریافت پاداش</h3>
                    <p class="arta-popup-bonus"><?php echo $bonusFormatted; ?></p>
                </div>
                
                <div class="arta-popup-content">
                    <p class="arta-popup-message"><?php echo esc_html($message); ?></p>
                    
                    <div class="arta-popup-actions">
                        <a href="<?php echo esc_url($edit_account_url); ?>" class="arta-popup-btn arta-popup-btn-primary">
                            تکمیل اطلاعات
                        </a>
                        <button onclick="artaDismissProfilePopup()" class="arta-popup-btn arta-popup-btn-secondary">
                            بعداً
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        (function() {
            // Show popup after a short delay
            setTimeout(function() {
                document.getElementById('arta-profile-popup-overlay').classList.add('show');
            }, 500);
            
            // Close popup function
            window.artaCloseProfilePopup = function() {
                document.getElementById('arta-profile-popup-overlay').classList.remove('show');
            };
            
            // Dismiss popup function
            window.artaDismissProfilePopup = function() {
                artaCloseProfilePopup();
            };
            
            // Close on overlay click
            document.getElementById('arta-profile-popup-overlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    artaCloseProfilePopup();
                }
            });
            
            // Close on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    artaCloseProfilePopup();
                }
            });
        })();
        </script>
        <?php
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

