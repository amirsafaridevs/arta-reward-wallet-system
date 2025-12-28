<?php

namespace ArtaRewardWalletSystem\Service\Admin;

use ArtaRewardWalletSystem\Contract\Abstracts\AbstractService;

class UserProfile extends AbstractService
{
    public function boot(): void
    {
        // Add custom fields to user profile page (both viewing own profile and editing other users)
        add_action('show_user_profile', [$this, 'displayCustomFields'], 10);
        add_action('edit_user_profile', [$this, 'displayCustomFields'], 10);
    }

    /**
     * Display custom fields in user profile edit page
     *
     * @param \WP_User $user The user object
     * @return void
     */
    public function displayCustomFields(\WP_User $user): void
    {
        $customFields = get_option('arta_custom_account_fields', []);
        
        // Debug: Check if custom fields exist
        if (empty($customFields) || !is_array($customFields)) {
            return;
        }

        ?>
        <h2><?php echo esc_html__('فیلدهای دلخواه', 'arta-reward-wallet-system'); ?></h2>
        <p class="description" style="margin-bottom: 15px; color: #646970;">
            <?php echo esc_html__('این فیلدها فقط برای نمایش هستند و قابل ویرایش نمی‌باشند.', 'arta-reward-wallet-system'); ?>
        </p>
        <table class="form-table" role="presentation">
            <tbody>
        <?php
        
        foreach ($customFields as $field) {
            if (empty($field['name']) || empty($field['label'])) {
                continue;
            }
            
            $fieldName = 'arta_' . $field['name'];
            
            // Get user meta - same method as AccountDetails.php uses
            $value = get_user_meta($user->ID, $fieldName, true);
            
            // get_user_meta returns:
            // - false if meta doesn't exist (never been saved)
            // - empty string '' if meta exists but is empty
            // - the actual value if meta exists with a value
            // - '0' or 0 are valid values (especially for checkbox)
            
            // Determine if value should be shown as empty
            // false = meta doesn't exist (user never filled this field)
            // '' = meta exists but is empty (user cleared the field)
            // null = same as false
            $isEmpty = false;
            
            if ($value === false || $value === null) {
                // Meta doesn't exist - user never filled this field
                $isEmpty = true;
            } elseif ($value === '') {
                // Meta exists but is empty - user cleared the field
                $isEmpty = true;
            } else {
                // Value exists - show it
                $isEmpty = false;
            }
            
            // Special handling for checkbox: '0' means unchecked, but it's still a value
            if ($field['type'] === 'checkbox') {
                if ($value === '0' || $value === 0) {
                    $isEmpty = false; // Show as unchecked
                } elseif ($value === false || $value === null || $value === '') {
                    $isEmpty = false; // Show as unchecked (default state)
                }
            }
            
            ?>
            <tr>
                <th>
                    <label><?php echo esc_html($field['label']); ?></label>
                </th>
                <td>
            <?php
            
            if ($isEmpty) {
                // Show empty state
                echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; color: #646970; font-size: 14px; font-style: italic; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">';
                echo esc_html__('خالی', 'arta-reward-wallet-system');
                echo '</div>';
            } else {
                // Display value based on field type
                switch ($field['type']) {
                    case 'textarea':
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; min-height: 60px; white-space: pre-wrap; color: #2c3338; font-size: 14px; line-height: 1.5; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">' . esc_html($value) . '</div>';
                        break;
                        
                    case 'checkbox':
                        $checked = ($value === '1' || $value === 1 || $value === true);
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; color: #2c3338; font-size: 14px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">';
                        echo $checked ? '<span style="color: #00a32a;">✓ بله</span>' : '<span style="color: #d63638;">✗ خیر</span>';
                        echo '</div>';
                        break;
                        
                    case 'email':
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">';
                        echo '<a href="mailto:' . esc_attr($value) . '" style="color: #2271b1; text-decoration: none; font-size: 14px;">' . esc_html($value) . '</a>';
                        echo '</div>';
                        break;
                        
                    case 'url':
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">';
                        echo '<a href="' . esc_url($value) . '" target="_blank" rel="noopener noreferrer" style="color: #2271b1; text-decoration: none; font-size: 14px;">' . esc_html($value) . '</a>';
                        echo '</div>';
                        break;
                        
                    case 'tel':
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">';
                        echo '<a href="tel:' . esc_attr($value) . '" style="color: #2271b1; text-decoration: none; font-size: 14px;">' . esc_html($value) . '</a>';
                        echo '</div>';
                        break;
                        
                    case 'date':
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; color: #2c3338; font-size: 14px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">' . esc_html($value) . '</div>';
                        break;
                        
                    case 'number':
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; color: #2c3338; font-size: 14px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">' . esc_html(number_format_i18n($value)) . '</div>';
                        break;
                        
                    case 'select':
                    case 'radio':
                    default: // text
                        echo '<div style="padding: 10px 14px; background: #fafbfc; border: 1px solid #e8eaed; border-radius: 4px; color: #2c3338; font-size: 14px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">' . esc_html($value) . '</div>';
                        break;
                }
            }
            
            ?>
                </td>
            </tr>
            <?php
        }
        
        ?>
            </tbody>
        </table>
        <?php
    }
}

