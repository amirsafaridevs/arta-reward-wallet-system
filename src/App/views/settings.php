<?php
/**
 * Settings View
 * Minimal Material Design
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تنظیمات - امتیازات و شارژ کیف پول</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            padding: 0;
            min-height: 100vh;
            direction: rtl;
            color: #212121;
        }

        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .settings-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 32px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            border-right: 3px solid #e1f5fe;
        }

        .settings-header h1 {
            color: #212121;
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 8px;
            letter-spacing: 0.15px;
        }

        .settings-header p {
            color: #757575;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .tabs {
            display: flex;
            background: #ffffff;
            border-radius: 4px 4px 0 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e0e0e0;
            overflow-x: auto;
        }

        .tab-button {
            padding: 16px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #757575;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-button:hover {
            color: #212121;
            background: #fafafa;
        }

        .tab-button.active {
            color: #1976d2;
            border-bottom-color: #90caf9;
        }

        .tab-content {
            display: none;
            background: #ffffff;
            padding: 32px;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tab-content.active {
            display: block;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section h2 {
            color: #212121;
            font-size: 18px;
            font-weight: 400;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f3e5f5;
            letter-spacing: 0.15px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            color: #212121;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="email"],
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #90caf9;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }

        .fields-list {
            margin-top: 16px;
        }

        .field-item {
            display: flex;
            align-items: center;
            padding: 16px;
            background: #fafafa;
            border-radius: 4px;
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .field-item:hover {
            background: #f5f5f5;
            border-color: #90caf9;
        }

        .field-item.disabled {
            opacity: 0.6;
        }

        .field-info {
            flex: 1;
        }

        .field-label {
            font-weight: 500;
            color: #212121;
            margin-bottom: 4px;
        }

        .field-type {
            font-size: 12px;
            color: #757575;
        }

        .field-controls {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #90caf9;
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        .custom-fields-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e0e0e0;
        }

        .custom-field-item {
            background: #fafafa;
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .custom-field-item:hover {
            background: #f5f5f5;
            border-color: #90caf9;
        }

        .custom-field-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .custom-field-main {
            display: grid;
            grid-template-columns: 1fr 1fr 180px;
            gap: 16px;
            align-items: end;
        }

        .custom-field-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .field-name-input {
            font-family: monospace;
            font-size: 12px;
        }

        .add-field-btn {
            background: #1976d2;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s ease;
            margin-top: 16px;
        }

        .add-field-btn:hover {
            background: #1565c0;
        }

        .remove-field-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s ease;
        }

        .remove-field-btn:hover {
            background: #d32f2f;
        }

        .submit-btn {
            background: #1976d2;
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s ease;
            margin-top: 24px;
        }

        .submit-btn:hover {
            background: #1565c0;
        }

        .notice {
            background: #e8f5e9;
            border-right: 3px solid #c5e1a5;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            color: #2e7d32;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .settings-container {
                padding: 16px;
            }

            .custom-field-main {
                grid-template-columns: 1fr;
            }

            .field-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .field-controls {
                margin-top: 12px;
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container">
        <div class="settings-header">
            <h1>تنظیمات</h1>
            <p>مدیریت تنظیمات سیستم امتیازات و شارژ کیف پول</p>
        </div>

        <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
            <div class="notice">
                تنظیمات با موفقیت ذخیره شد.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('arta_settings_nonce'); ?>
            <input type="hidden" name="action" value="arta_save_settings">

            <div class="tabs">
                <button type="button" class="tab-button active" onclick="switchTab('general')">تنظیمات کلی</button>
                <button type="button" class="tab-button" onclick="switchTab('fields')">فیلدهای حساب کاربری</button>
            </div>

            <div id="general-tab" class="tab-content active">
                <div class="form-section">
                    <h2>تنظیمات ثبت نام</h2>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" 
                                   id="enable_registration_bonus" 
                                   name="enable_registration_bonus" 
                                   value="1"
                                   <?php checked($settings['enable_registration_bonus'], 1); ?>>
                            <label for="enable_registration_bonus">اعمال مبلغ به کیف پول </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="registration_bonus_amount">مبلغ اعمال شده (تومان)</label>
                        <input type="number" 
                               id="registration_bonus_amount" 
                               name="registration_bonus_amount" 
                               value="<?php echo esc_attr($settings['registration_bonus_amount']); ?>"
                               step="0.01"
                               min="0">
                    </div>
                </div>

                <div class="form-section">
                    <h2>تنظیمات تکمیل پروفایل</h2>
                    
                    <div class="form-group">
                        <label for="completion_bonus_amount">مبلغ پاداش تکمیل پروفایل (تومان)</label>
                        <input type="number" 
                               id="completion_bonus_amount" 
                               name="completion_bonus_amount" 
                               value="<?php echo esc_attr($settings['completion_bonus_amount']); ?>"
                               step="0.01"
                               min="0">
                        <p style="font-size: 12px; color: #757575; margin-top: 8px;">
                            این مبلغ زمانی به کیف پول کاربر اضافه می‌شود که تمام فیلدهای واجب را پر کند.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="profile_completion_message">متن ناتیف تکمیل پروفایل</label>
                        <input type="text" 
                               id="profile_completion_message" 
                               name="profile_completion_message" 
                               value="<?php echo esc_attr($settings['profile_completion_message']); ?>"
                               placeholder="با تکمیل اطلاعات حساب خود پاداش بگیرید">
                        <p style="font-size: 12px; color: #757575; margin-top: 8px;">
                            این متن در بالای صفحات حساب کاربری برای کاربرانی که پروفایل خود را تکمیل نکرده‌اند نمایش داده می‌شود.
                        </p>
                    </div>
                </div>
            </div>

            <div id="fields-tab" class="tab-content">
                <div class="form-section">
                    <h2>فیلدهای موجود</h2>
                    
                    <div class="fields-list">
                        <?php foreach ($accountFields as $fieldKey => $field): ?>
                            <div class="field-item <?php echo (!isset($field['enabled']) || $field['enabled']) ? '' : 'disabled'; ?>">
                                <div class="field-info">
                                    <div class="field-label"><?php echo esc_html($field['label']); ?></div>
                                    <div class="field-type">نوع: <?php echo esc_html($field['type']); ?></div>
                                </div>
                                <div class="field-controls">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="account_fields[<?php echo esc_attr($fieldKey); ?>][enabled]"
                                               <?php checked(!isset($field['enabled']) || $field['enabled'], true); ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span style="font-size: 12px; color: #757575;">فعال</span>
                                    
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="account_fields[<?php echo esc_attr($fieldKey); ?>][required]"
                                               <?php checked($field['required'], true); ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span style="font-size: 12px; color: #757575;">واجب</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="custom-fields-section">
                    <h2>فیلدهای دلخواه</h2>
                    <div id="custom-fields-container" class="fields-list">
                        <?php 
                        $customFields = get_option('arta_custom_account_fields', []);
                        if (!empty($customFields)):
                            foreach ($customFields as $index => $field): 
                        ?>
                            <div class="custom-field-item">
                                <div class="custom-field-main">
                                    <div class="form-group">
                                        <label>برچسب</label>
                                        <input type="text" 
                                               name="custom_fields[<?php echo $index; ?>][label]" 
                                               value="<?php echo esc_attr($field['label']); ?>" 
                                               required
                                               placeholder="مثال: شماره ملی">
                                    </div>
                                    <div class="form-group">
                                        <label>نام فیلد (انگلیسی)</label>
                                        <input type="text" 
                                               name="custom_fields[<?php echo $index; ?>][name]" 
                                               value="<?php echo esc_attr($field['name']); ?>" 
                                               required
                                               class="field-name-input"
                                               placeholder="مثال: national_id">
                                    </div>
                                    <div class="form-group">
                                        <label>نوع ورودی</label>
                                        <select name="custom_fields[<?php echo $index; ?>][type]">
                                            <option value="text" <?php selected($field['type'], 'text'); ?>>متن</option>
                                            <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>متن چند خطی</option>
                                            <option value="email" <?php selected($field['type'], 'email'); ?>>ایمیل</option>
                                            <option value="tel" <?php selected($field['type'], 'tel'); ?>>تلفن</option>
                                            <option value="number" <?php selected($field['type'], 'number'); ?>>عدد</option>
                                            <option value="url" <?php selected($field['type'], 'url'); ?>>آدرس وب</option>
                                            <option value="date" <?php selected($field['type'], 'date'); ?>>تاریخ</option>
                                            <option value="password" <?php selected($field['type'], 'password'); ?>>رمز عبور</option>
                                            <option value="select" <?php selected($field['type'], 'select'); ?>>انتخاب از لیست</option>
                                            <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>چک باکس</option>
                                            <option value="radio" <?php selected($field['type'], 'radio'); ?>>رادیو</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="custom-field-actions">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="custom_fields[<?php echo $index; ?>][required]" 
                                               value="1"
                                               <?php checked($field['required'], 1); ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span style="font-size: 12px; color: #757575;">واجب</span>
                                    <button type="button" class="remove-field-btn" onclick="removeField(this)">حذف</button>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </div>
                    <button type="button" class="add-field-btn" onclick="addCustomField()">افزودن فیلد جدید</button>
                </div>
            </div>

            <button type="submit" class="submit-btn">ذخیره تنظیمات</button>
        </form>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }

        let fieldIndex = <?php echo isset($customFields) ? count($customFields) : 0; ?>;

        function addCustomField() {
            const container = document.getElementById('custom-fields-container');
            const fieldHtml = `
                <div class="custom-field-item">
                    <div class="custom-field-main">
                        <div class="form-group">
                            <label>برچسب</label>
                            <input type="text" name="custom_fields[${fieldIndex}][label]" required placeholder="مثال: شماره ملی">
                        </div>
                        <div class="form-group">
                            <label>نام فیلد (انگلیسی)</label>
                            <input type="text" name="custom_fields[${fieldIndex}][name]" required class="field-name-input" placeholder="مثال: national_id">
                        </div>
                        <div class="form-group">
                            <label>نوع ورودی</label>
                            <select name="custom_fields[${fieldIndex}][type]">
                                <option value="text">متن</option>
                                <option value="textarea">متن چند خطی</option>
                                <option value="email">ایمیل</option>
                                <option value="tel">تلفن</option>
                                <option value="number">عدد</option>
                                <option value="url">آدرس وب</option>
                                <option value="date">تاریخ</option>
                                <option value="password">رمز عبور</option>
                                <option value="select">انتخاب از لیست</option>
                                <option value="checkbox">چک باکس</option>
                                <option value="radio">رادیو</option>
                            </select>
                        </div>
                    </div>
                    <div class="custom-field-actions">
                        <label class="switch">
                            <input type="checkbox" name="custom_fields[${fieldIndex}][required]" value="1">
                            <span class="slider"></span>
                        </label>
                        <span style="font-size: 12px; color: #757575;">واجب</span>
                        <button type="button" class="remove-field-btn" onclick="removeField(this)">حذف</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', fieldHtml);
            fieldIndex++;
        }

        function removeField(btn) {
            btn.closest('.custom-field-item').remove();
        }
    </script>
</body>
</html>

