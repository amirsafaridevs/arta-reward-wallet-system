<?php
/**
 * SMS Logs View
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لاگ‌های SMS - امتیازات و شارژ کیف پول</title>
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

        .logs-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .logs-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 32px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            border-right: 3px solid #e1f5fe;
        }

        .logs-header h1 {
            color: #212121;
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 8px;
            letter-spacing: 0.15px;
        }

        .logs-header p {
            color: #757575;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .logs-table-container {
            background: #ffffff;
            padding: 32px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logs-table th,
        .logs-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #e0e0e0;
        }

        .logs-table th {
            background: #f5f5f5;
            font-weight: 500;
            color: #212121;
            font-size: 14px;
        }

        .logs-table td {
            font-size: 13px;
            color: #757575;
        }

        .logs-table tr:hover {
            background: #fafafa;
        }

        .status-success {
            color: #2e7d32;
            font-weight: 500;
        }

        .status-failed {
            color: #c62828;
            font-weight: 500;
        }

        .no-logs {
            text-align: center;
            padding: 48px;
            color: #757575;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .logs-container {
                padding: 16px;
            }

            .logs-table {
                font-size: 12px;
            }

            .logs-table th,
            .logs-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="logs-container">
        <div class="logs-header">
            <h1>لاگ‌های SMS</h1>
            <p>تاریخچه ارسال پیامک‌ها</p>
        </div>

        <div class="logs-table-container">
            <?php if (empty($logs)): ?>
                <div class="no-logs">
                    هنوز لاگی ثبت نشده است.
                </div>
            <?php else: ?>
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>تاریخ و ساعت</th>
                            <th>شماره گیرنده</th>
                            <th>پیام</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <?php 
                            $response = $log['response'] ?? [];
                            $to = $response['to'] ?? '-';
                            $message = $response['message'] ?? '-';
                            $status = $response['status'] ?? 'unknown';
                            ?>
                            <tr>
                                <td><?php echo esc_html($log['datetime'] ?? '-'); ?></td>
                                <td><?php echo esc_html($to); ?></td>
                                <td style="max-width: 400px; word-wrap: break-word;">
                                    <?php echo esc_html(mb_substr($message, 0, 100)) . (mb_strlen($message) > 100 ? '...' : ''); ?>
                                </td>
                                <td>
                                    <span class="status-<?php echo $status === 'success' ? 'success' : 'failed'; ?>">
                                        <?php echo $status === 'success' ? '✓ موفق' : '✗ ناموفق'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

