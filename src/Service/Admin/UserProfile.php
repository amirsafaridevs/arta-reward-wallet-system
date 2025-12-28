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
            // get_user_meta($user_id, $meta_key, true) returns:
            // - false if meta doesn't exist (never been saved)
            // - empty string '' if meta exists but is empty
            // - the actual value (string, number, etc.) if meta exists with a value
            $value = get_user_meta($user->ID, $fieldName, true);
            
            // Check if value should be considered empty
            // We only consider it empty if:
            // 1. false (meta key doesn't exist in database)
            // 2. null (shouldn't happen but just in case)
            // 3. empty string '' (meta exists but is empty)
            // 
            // Important: '0' and 0 are valid values and should be displayed!
            // For checkbox, '0' means unchecked but it's still a value to show
            
            // Use strict comparison to check for empty values
            // Only false, null, or empty string are considered empty
            // Everything else (including '0', 0, spaces, etc.) should be displayed
            $isEmpty = ($value === false || $value === null || $value === '');
            
            // Additional check: if value is an array (shouldn't happen but just in case)
            if (is_array($value)) {
                if (empty($value)) {
                    $isEmpty = true;
                } else {
                    // If it's an array with values, get the first value
                    $value = reset($value);
                    $isEmpty = false;
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

