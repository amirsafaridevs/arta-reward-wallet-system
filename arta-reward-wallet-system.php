<?php

/**
 * Plugin Name: افزونه امتیازات و شارژ کیف پول 
 * Description: این افزونه برای مدیریت امتیازات و شارژ کیف پول در وردپرس ایجاد شده است.
 * Version: 0.0.1
 * Author: Amir Safari
 * Author URI: https://artacode.net
 * License: MIT
 * Text Domain: arta-reward-wallet-system
 * Requires Plugins: woocommerce,woo-wallet
 * Requires at least: 6.5
 */

// اتولودر composer را لود کن
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use ArtaRewardWalletSystem\App\App;
App::get();