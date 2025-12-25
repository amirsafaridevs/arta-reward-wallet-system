<?php
use ArtaRewardWalletSystem\Core\Application;
/**
 * Import Users View
 * Multi-step Excel import with progress tracking
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وارد کردن کاربران - امتیازات و شارژ کیف پول</title>
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

        .import-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .import-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 32px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            border-right: 3px solid #e1f5fe;
        }

        .import-header h1 {
            color: #212121;
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 8px;
            letter-spacing: 0.15px;
        }

        .import-header p {
            color: #757575;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .step-container {
            background: #ffffff;
            padding: 32px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .step-title {
            font-size: 18px;
            font-weight: 500;
            color: #1976d2;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #212121;
            margin-bottom: 8px;
        }

        .form-group input[type="file"] {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
            overflow: hidden;
        }

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            font-family: inherit;
        }

        .file-label {
            display: block;
            width: 100%;
            padding: 48px 24px;
            border: 2px dashed #e0e0e0;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fafafa;
            color: #757575;
            font-size: 14px;
            font-weight: 400;
        }

        .file-label:hover {
            border-color: #1976d2;
            background: #f5f5f5;
            color: #1976d2;
        }

        .file-label:active {
            border-color: #1565c0;
            background: #eeeeee;
        }

        .form-group select:focus {
            outline: none;
            border-color: #1976d2;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-primary {
            background: #1976d2;
            color: white;
        }

        .btn-primary:hover {
            background: #1565c0;
        }

        .btn-primary:disabled {
            background: #bdbdbd;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #f5f5f5;
            color: #212121;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .progress-container {
            margin-top: 24px;
            display: none;
        }

        .progress-bar-wrapper {
            width: 100%;
            height: 24px;
            background: #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #1976d2 0%, #42a5f5 100%);
            width: 0%;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 500;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #757575;
            margin-bottom: 12px;
        }

        .results-container {
            margin-top: 24px;
            display: none;
        }

        .results-success {
            background: #e8f5e9;
            border-right: 3px solid #4caf50;
            padding: 16px;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        .results-error {
            background: #ffebee;
            border-right: 3px solid #f44336;
            padding: 16px;
            border-radius: 4px;
        }

        .results-title {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .results-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 12px;
        }

        .result-item {
            padding: 8px;
            margin-bottom: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
            font-size: 13px;
        }

        .preview-table-wrapper {
            margin-top: 16px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            display: none;
            position: relative;
        }

        .preview-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .preview-table thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .preview-table th,
        .preview-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #e0e0e0;
        }

        .preview-table th {
            background: #f5f5f5;
            font-weight: 500;
            color: #212121;
            position: sticky;
            top: 0;
        }

        .preview-table tr:hover {
            background: #fafafa;
        }

        .notice {
            background: #fff3e0;
            border-right: 3px solid #ff9800;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            color: #e65100;
            font-size: 14px;
        }

        .help-section {
            margin-top: 32px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .help-header {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 16px 20px;
            font-size: 15px;
            font-weight: 500;
            color: #1976d2;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .help-content {
            padding: 20px;
        }

        .help-list {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }

        .help-list li {
            padding: 10px 0;
            padding-right: 24px;
            position: relative;
            font-size: 14px;
            color: #424242;
            line-height: 1.6;
        }

        .help-list li::before {
            content: '•';
            position: absolute;
            right: 0;
            color: #1976d2;
            font-size: 20px;
            font-weight: bold;
        }

        .help-list li strong {
            color: #1976d2;
            font-weight: 600;
        }

        .help-download {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .download-link {
            display: inline-flex;
            align-items: center;
            padding: 12px 20px;
            background: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .download-link:hover {
            background: #1565c0;
            box-shadow: 0 2px 8px rgba(25, 118, 210, 0.3);
        }

        .download-link:active {
            transform: translateY(1px);
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            padding: 0 16px;
        }

        .step-item {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-item::after {
            content: '';
            position: absolute;
            top: 20px;
            right: 50%;
            width: 100%;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step-item:last-child::after {
            display: none;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #757575;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        .step-item.active .step-number {
            background: #1976d2;
            color: white;
        }

        .step-item.completed .step-number {
            background: #4caf50;
            color: white;
        }

        .step-label {
            font-size: 12px;
            color: #757575;
        }

        .step-item.active .step-label {
            color: #1976d2;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .import-container {
                padding: 16px;
            }

            .step-indicator {
                flex-direction: column;
                gap: 16px;
            }

            .step-item::after {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="import-container">
        <div class="import-header">
            <h1>وارد کردن کاربران</h1>
            <p>وارد کردن کاربران از فایل Excel</p>
        </div>

        <div class="step-indicator">
            <div class="step-item active" id="step1-indicator">
                <div class="step-number">1</div>
                <div class="step-label">آپلود فایل</div>
            </div>
            <div class="step-item" id="step2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">انتخاب فیلدها</div>
            </div>
            <div class="step-item" id="step3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">وارد کردن</div>
            </div>
        </div>

        <!-- Step 1: Upload File -->
        <div class="step-container" id="step1">
            <div class="step-title">مرحله 1: آپلود فایل Excel</div>
            <div class="form-group">
                <label for="excel-file" class="file-label">
                    برای انتخاب فایل اینجا کلیک کنید
                </label>
                <input type="file" id="excel-file" accept=".xlsx,.xls" />
                <p id="file-name" style="font-size: 12px; color: #1976d2; margin-top: 12px; display: none; font-weight: 500;"></p>
                <p style="font-size: 12px; color: #757575; margin-top: 8px;">
                    فرمت‌های پشتیبانی شده: .xlsx, .xls
                </p>
            </div>
            <button class="btn btn-primary" onclick="loadExcelFile()">بارگذاری و خواندن فایل</button>
            
            <div class="help-section">
                <div class="help-header">
                    <svg style="width: 20px; height: 20px; margin-left: 8px; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    راهنمای استفاده
                </div>
                <div class="help-content">
                    <ul class="help-list">
                        <li>فایل Excel باید دارای ستون‌های <strong>نام</strong> و <strong>شماره تلفن</strong> باشد</li>
                        <li>شماره تلفن باید به صورت عددی و بدون فاصله وارد شود (مثال: 09123456789)</li>
                        <li>نام کاربری به صورت خودکار از شماره تلفن ایجاد می‌شود</li>
                        <li>کاربران با نقش <strong>مشتری (Customer)</strong> ثبت می‌شوند</li>
                        <li>در صورت تکراری بودن شماره تلفن، کاربر ثبت نمی‌شود</li>
                        <li>می‌توانید برای کاربران جدید SMS ارسال کنید (در مرحله بعدی)</li>
                    </ul>
                    <div class="help-download">
                        <a href="<?php echo Application::assets('example.xlsx'); ?>" 
                           download="example.xlsx" 
                           class="download-link">
                            <svg style="width: 16px; height: 16px; margin-left: 6px; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                            </svg>
                            دانلود فایل نمونه Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Select Fields -->
        <div class="step-container" id="step2" style="display: none;">
            <div class="step-title">مرحله 2: انتخاب فیلدهای نام و شماره تلفن</div>
            <div class="form-group">
                <label for="name-column">ستون نام</label>
                <select id="name-column"></select>
            </div>
            <div class="form-group">
                <label for="phone-column">ستون شماره تلفن</label>
                <select id="phone-column"></select>
            </div>
            <div class="form-group" style="margin-top: 24px;">
                <div class="checkbox-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" 
                           id="send-sms" 
                           checked>
                    <label for="send-sms" style="margin: 0; cursor: pointer; font-weight: 400;">
                        ارسال SMS برای کاربران بعد از ثبت‌نام
                    </label>
                </div>
            </div>
            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button class="btn btn-primary" onclick="startImport()">شروع وارد کردن</button>
                <button class="btn btn-secondary" onclick="goToStep(1)">بازگشت</button>
            </div>
            <div class="preview-table-wrapper">
                <table class="preview-table" id="preview-table">
                    <thead>
                        <tr id="preview-header"></tr>
                    </thead>
                    <tbody id="preview-body"></tbody>
                </table>
            </div>
        </div>

        <!-- Step 3: Import Progress -->
        <div class="step-container" id="step3" style="display: none;">
            <div class="step-title">مرحله 3: در حال وارد کردن کاربران</div>
            <div class="progress-container" id="progress-container">
                <div class="progress-info">
                    <span id="progress-text">در حال پردازش...</span>
                    <span id="progress-count">0 / 0</span>
                </div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar" id="progress-bar">0%</div>
                </div>
            </div>
            <div class="results-container" id="results-container">
                <div class="results-success" id="results-success"></div>
                <div class="results-error" id="results-error"></div>
            </div>
        </div>
    </div>

    <!-- Load SheetJS library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Define ajaxurl for WordPress admin if not already defined
        if (typeof ajaxurl === 'undefined') {
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        }
        
        var excelData = null;
        var columns = [];
        var selectedNameColumn = '';
        var selectedPhoneColumn = '';

        // Show file name when selected
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('excel-file');
            const fileName = document.getElementById('file-name');
            const fileLabel = document.querySelector('.file-label');

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        fileName.textContent = '✓ فایل انتخاب شده: ' + file.name;
                        fileName.style.display = 'block';
                        fileLabel.textContent = 'فایل انتخاب شده: ' + file.name;
                        fileLabel.style.borderColor = '#4caf50';
                        fileLabel.style.color = '#2e7d32';
                    } else {
                        fileName.style.display = 'none';
                        fileLabel.textContent = 'برای انتخاب فایل اینجا کلیک کنید';
                        fileLabel.style.borderColor = '#e0e0e0';
                        fileLabel.style.color = '#757575';
                    }
                });
            }
        });

        window.loadExcelFile = function() {
            const fileInput = document.getElementById('excel-file');
            const file = fileInput.files[0];

            if (!file) {
                alert('لطفاً یک فایل Excel انتخاب کنید');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    excelData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                    if (excelData.length === 0) {
                        alert('فایل Excel خالی است');
                        return;
                    }

                    // Get columns from first row
                    columns = excelData[0] || [];
                    
                    if (columns.length === 0) {
                        alert('فایل Excel معتبر نیست');
                        return;
                    }

                    // Populate column selects
                    const nameSelect = document.getElementById('name-column');
                    const phoneSelect = document.getElementById('phone-column');
                    
                    nameSelect.innerHTML = '<option value="">انتخاب کنید</option>';
                    phoneSelect.innerHTML = '<option value="">انتخاب کنید</option>';

                    columns.forEach((col, index) => {
                        const option1 = document.createElement('option');
                        option1.value = index;
                        option1.textContent = col || `ستون ${index + 1}`;
                        nameSelect.appendChild(option1);

                        const option2 = document.createElement('option');
                        option2.value = index;
                        option2.textContent = col || `ستون ${index + 1}`;
                        phoneSelect.appendChild(option2);
                    });

                    // Show preview
                    showPreview();

                    // Go to step 2
                    goToStep(2);
                } catch (error) {
                    console.error('Error reading Excel:', error);
                    alert('خطا در خواندن فایل Excel: ' + error.message);
                }
            };

            reader.readAsArrayBuffer(file);
        }

        function showPreview() {
            const previewTable = document.getElementById('preview-table');
            const previewTableWrapper = document.querySelector('.preview-table-wrapper');
            const previewHeader = document.getElementById('preview-header');
            const previewBody = document.getElementById('preview-body');

            previewHeader.innerHTML = '';
            previewBody.innerHTML = '';

            // Add header
            columns.forEach(col => {
                const th = document.createElement('th');
                th.textContent = col || 'بدون نام';
                previewHeader.appendChild(th);
            });

            // Add all rows (skip first row which is header)
            const allRows = excelData.slice(1);
            allRows.forEach(row => {
                const tr = document.createElement('tr');
                columns.forEach((_, index) => {
                    const td = document.createElement('td');
                    td.textContent = row[index] || '';
                    tr.appendChild(td);
                });
                previewBody.appendChild(tr);
            });

            previewTableWrapper.style.display = 'block';

            // Update selects on change
            document.getElementById('name-column').addEventListener('change', function() {
                selectedNameColumn = this.value;
            });

            document.getElementById('phone-column').addEventListener('change', function() {
                selectedPhoneColumn = this.value;
            });
        }

        window.startImport = function() {
            selectedNameColumn = document.getElementById('name-column').value;
            selectedPhoneColumn = document.getElementById('phone-column').value;

            if (!selectedNameColumn || !selectedPhoneColumn) {
                alert('لطفاً ستون‌های نام و شماره تلفن را انتخاب کنید');
                return;
            }

            // Prepare users data
            const users = [];
            for (let i = 1; i < excelData.length; i++) {
                const row = excelData[i];
                const name = row[selectedNameColumn] ? String(row[selectedNameColumn]).trim() : '';
                const phone = row[selectedPhoneColumn] ? String(row[selectedPhoneColumn]).trim() : '';

                if (name && phone) {
                    users.push({
                        name: name,
                        phone: phone
                    });
                }
            }

            if (users.length === 0) {
                alert('هیچ کاربر معتبری در فایل پیدا نشد');
                return;
            }

            // Get SMS checkbox value
            const sendSms = document.getElementById('send-sms').checked;

            // Go to step 3
            goToStep(3);

            // Start importing
            importUsers(users, sendSms);
        }

        function importUsers(users, sendSms) {
            const totalUsers = users.length;
            let processedUsers = 0;
            let successCount = 0;
            let failedCount = 0;
            const successList = [];
            const failedList = [];

            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const progressCount = document.getElementById('progress-count');
            const resultsContainer = document.getElementById('results-container');

            progressContainer.style.display = 'block';

            function processBatch(batchIndex) {
                const batch = users.slice(batchIndex, batchIndex + 5);
                
                if (batch.length === 0) {
                    // All done
                    showResults(successList, failedList, successCount, failedCount);
                    return;
                }

                // Send batch to server
                const formData = new FormData();
                formData.append('action', 'arta_import_users');
                formData.append('nonce', '<?php echo wp_create_nonce('arta_import_users_nonce'); ?>');
                formData.append('users', JSON.stringify(batch));
                formData.append('send_sms', sendSms ? '1' : '0');

                const ajaxUrl = (typeof ajaxurl !== 'undefined') ? ajaxurl : '<?php echo admin_url('admin-ajax.php'); ?>';
                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const results = data.data.results;
                        
                        if (results.success) {
                            results.success.forEach(user => {
                                successList.push(user);
                                successCount++;
                            });
                        }
                        
                        if (results.failed) {
                            results.failed.forEach(user => {
                                failedList.push(user);
                                failedCount++;
                            });
                        }
                    } else {
                        batch.forEach(user => {
                            failedList.push({
                                name: user.name,
                                phone: user.phone,
                                reason: data.data?.message || 'خطای نامشخص'
                            });
                            failedCount++;
                        });
                    }

                    processedUsers += batch.length;
                    const progress = (processedUsers / totalUsers) * 100;
                    
                    progressBar.style.width = progress + '%';
                    progressBar.textContent = Math.round(progress) + '%';
                    progressCount.textContent = processedUsers + ' / ' + totalUsers;
                    progressText.textContent = 'در حال پردازش... (' + processedUsers + ' از ' + totalUsers + ')';

                    // Process next batch
                    setTimeout(() => {
                        processBatch(batchIndex + 5);
                    }, 500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    batch.forEach(user => {
                        failedList.push({
                            name: user.name,
                            phone: user.phone,
                            reason: 'خطا در ارتباط با سرور'
                        });
                        failedCount++;
                    });

                    processedUsers += batch.length;
                    const progress = (processedUsers / totalUsers) * 100;
                    
                    progressBar.style.width = progress + '%';
                    progressBar.textContent = Math.round(progress) + '%';
                    progressCount.textContent = processedUsers + ' / ' + totalUsers;

                    // Process next batch
                    setTimeout(() => {
                        processBatch(batchIndex + 5);
                    }, 500);
                });
            }

            // Start processing
            processBatch(0);
        }

        function showResults(successList, failedList, successCount, failedCount) {
            const progressContainer = document.getElementById('progress-container');
            const resultsContainer = document.getElementById('results-container');
            const resultsSuccess = document.getElementById('results-success');
            const resultsError = document.getElementById('results-error');

            progressContainer.style.display = 'none';
            resultsContainer.style.display = 'block';

            // Show success results
            if (successCount > 0) {
                resultsSuccess.innerHTML = `
                    <div class="results-title" style="color: #2e7d32;">✓ ${successCount} کاربر با موفقیت ثبت شد</div>
                    <div class="results-list">
                        ${successList.map(user => `
                            <div class="result-item">
                                <strong>${user.name}</strong> - ${user.phone}
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                resultsSuccess.style.display = 'none';
            }

            // Show failed results
            if (failedCount > 0) {
                resultsError.innerHTML = `
                    <div class="results-title" style="color: #c62828;">✗ ${failedCount} کاربر ثبت نشد</div>
                    <div class="results-list">
                        ${failedList.map(user => `
                            <div class="result-item">
                                <strong>${user.name}</strong> - ${user.phone}<br>
                                <small style="color: #c62828;">${user.reason}</small>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                resultsError.style.display = 'none';
            }
        }

        window.goToStep = function(step) {
            // Hide all steps
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'none';

            // Show selected step
            document.getElementById('step' + step).style.display = 'block';

            // Update indicators
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById('step' + i + '-indicator');
                if (i < step) {
                    indicator.classList.remove('active');
                    indicator.classList.add('completed');
                } else if (i === step) {
                    indicator.classList.remove('completed');
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            }
        }
    </script>
</body>
</html>

