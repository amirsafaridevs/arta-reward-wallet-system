<?php
/**
 * Dashboard View
 * Minimal Material Design
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد - امتیازات و شارژ کیف پول</title>
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

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 32px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            border-right: 3px solid #e1f5fe;
        }

        .dashboard-header h1 {
            color: #212121;
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 8px;
            letter-spacing: 0.15px;
        }

        .dashboard-header p {
            color: #757575;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 4px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.2s ease;
            border-top: 3px solid transparent;
            position: relative;
        }

        .stat-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .stat-card.primary {
            border-top-color: #90caf9;
        }

        .stat-card.secondary {
            border-top-color: #f8bbd0;
        }

        .stat-card.accent {
            border-top-color: #ffe0b2;
        }

        .stat-card.info {
            border-top-color: #c5e1a5;
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            color: #616161;
            font-size: 20px;
        }

        .stat-card.primary .stat-icon {
            background: #e1f5fe;
            color: #0288d1;
        }

        .stat-card.secondary .stat-icon {
            background: #fce4ec;
            color: #c2185b;
        }

        .stat-card.accent .stat-icon {
            background: #fff3e0;
            color: #f57c00;
        }

        .stat-card.info .stat-icon {
            background: #f1f8e9;
            color: #689f38;
        }

        .stat-label {
            font-size: 13px;
            color: #757575;
            font-weight: 400;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 300;
            color: #212121;
            line-height: 1.2;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .stat-description {
            font-size: 12px;
            color: #9e9e9e;
            font-weight: 400;
            line-height: 16px;
        }

        .content-section {
            background: #ffffff;
            border-radius: 4px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 24px;
        }

        .content-section h2 {
            color: #212121;
            font-size: 20px;
            font-weight: 400;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f3e5f5;
            letter-spacing: 0.15px;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #9e9e9e;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            opacity: 0.4;
            color: #bdbdbd;
        }

        .empty-state p {
            font-size: 14px;
            color: #757575;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-header {
                padding: 24px;
            }

            .dashboard-header h1 {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .stat-value {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>داشبورد مدیریت</h1>
            <p>امتیازات و شارژ کیف پول</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-header">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 7c0-2.21-1.79-4-4-4S8 4.79 8 7s1.79 4 4 4 4-1.79 4-4zm-4 6c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-label">کل کاربران</div>
                <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                <div class="stat-description">تعداد کل کاربران ثبت‌نام شده</div>
            </div>

            <div class="stat-card secondary">
                <div class="stat-header">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 18c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM1 2v2h2l3.6 7.59-1.35 2.45c-.15.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-label">مشتریان</div>
                <div class="stat-value"><?php echo number_format($stats['customers']); ?></div>
                <div class="stat-description">کاربران با نقش مشتری</div>
            </div>

            <div class="stat-card accent">
                <div class="stat-header">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-label">مشترکین</div>
                <div class="stat-value"><?php echo number_format($stats['subscribers']); ?></div>
                <div class="stat-description">کاربران با نقش مشترک</div>
            </div>

            <div class="stat-card info">
                <div class="stat-header">
                    <div class="stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-label">آمار کلی</div>
                <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                <div class="stat-description">نمایش اطلاعات آماری</div>
            </div>
        </div>

        <div class="content-section">
            <h2>اطلاعات بیشتر</h2>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <p>اطلاعات بیشتر به زودی اضافه خواهد شد</p>
            </div>
        </div>
    </div>
</body>
</html>
