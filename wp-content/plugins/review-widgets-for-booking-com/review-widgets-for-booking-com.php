<?php
/*
Plugin Name: Widgets for Booking.com Reviews
Plugin Title: Widgets for Booking.com Reviews Plugin
Plugin URI: https://wordpress.org/plugins/review-widgets-for-booking-com/
Description: Embed Booking.com reviews fast and easily into your WordPress site. Increase SEO, trust and sales using Booking.com reviews.
Tags: booking.com, reviews, widget, slider, booking reviews
Author: Trustindex.io <support@trustindex.io>
Author URI: https://www.trustindex.io/
Contributors: trustindex
License: GPLv2 or later
Version: 6.6.2
Text Domain: review-widgets-for-booking-com
Domain Path: /languages/
Donate link: https://www.trustindex.io/prices/
*/
/*
Copyright 2019 Trustindex Kft (email: support@trustindex.io)
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require(ABSPATH . 'wp-includes/version.php');
$page_slug = isset($_GET['page']) ? explode('/', sanitize_text_field($_GET['page']))[0] : '';
$tmp = explode('/', plugin_dir_path( __FILE__ ));
$plugin_slug = $tmp[ count($tmp) - 2 ];
if(1)
{
require_once plugin_dir_path( __FILE__ ) . 'plugin-load.php';
$trustindex_pm_booking = new TrustindexPlugin("booking", __FILE__, "6.6.2", "Widgets for Booking.com Reviews", "Booking.com");
}
register_activation_hook(__FILE__, array($trustindex_pm_booking, 'activate'));
register_deactivation_hook(__FILE__, array($trustindex_pm_booking, 'deactivate'));
add_action('admin_menu', array($trustindex_pm_booking, 'add_setting_menu'), 10);
add_filter('plugin_action_links', array($trustindex_pm_booking, 'add_plugin_action_links'), 10, 2);
add_filter('plugin_row_meta', array($trustindex_pm_booking, 'add_plugin_meta_links'), 10, 2);
if(!function_exists('register_block_type'))
{
add_action('widgets_init', array($trustindex_pm_booking, 'init_widget'));
add_action('widgets_init', array($trustindex_pm_booking, 'register_widget'));
}
add_action('wp_head', array($trustindex_pm_booking, 'add_noreg_css_head'));
add_action('admin_head', array($trustindex_pm_booking, 'add_noreg_css_head_admin'));
$widget_css_booking = get_option($trustindex_pm_booking->get_option_name('css-content'));
if($trustindex_pm_booking->is_noreg_linked() && $widget_css_booking)
{
add_action('wp_enqueue_scripts', function() {
global $trustindex_pm_booking;
wp_enqueue_script('trustindex-frontend-js-booking', $trustindex_pm_booking->get_plugin_file_url('static/js/frontend.js'), [ 'jquery' ], false, true );
wp_localize_script('trustindex-frontend-js-booking', 'WidgetCssbooking', [
'ajaxurl' => admin_url('admin-ajax.php'),
'security' => wp_create_nonce('frontend-nonce-booking'),
'action' => 'widget_css_booking',
'selector' => '.ti-widget.ti-' . substr($trustindex_pm_booking->shortname, 0, 4)
]);
});
add_action('wp_ajax_nopriv_widget_css_booking', 'trustindex_widget_css_booking');
add_action('wp_ajax_widget_css_booking', 'trustindex_widget_css_booking');
function trustindex_widget_css_booking() {
global $widget_css_booking;
check_ajax_referer('frontend-nonce-booking', 'security');
echo $widget_css_booking;
exit;
}
}
add_action('init', array($trustindex_pm_booking, 'init_shortcode'));
add_filter('script_loader_tag', function($tag, $handle) {
if(strpos($tag, 'trustindex.io/loader.js') !== false && strpos($tag, 'defer async') === false) {
$tag = str_replace(' src', ' defer async src', $tag );
}
return $tag;
}, 10, 2);
add_action('init', array($trustindex_pm_booking, 'register_tinymce_features'));
add_action('init', array($trustindex_pm_booking, 'output_buffer'));
add_action('wp_ajax_list_trustindex_widgets', array($trustindex_pm_booking, 'list_trustindex_widgets_ajax'));
add_action('admin_enqueue_scripts', array($trustindex_pm_booking, 'trustindex_add_scripts'));
add_action('rest_api_init', array($trustindex_pm_booking, 'init_restapi'));
function trustindex_rate_us_booking() {
$rate_us = get_option('trustindex-booking-rate-us', time() - 1);
if($rate_us == 'hide' || (int)$rate_us > time())
{
return;
}
$dir = plugin_dir_path( __FILE__ );
$usage_time = time() + 10;
if(is_dir($dir))
{
$usage_time = filemtime($dir) + (1 * 86400);
}
if($usage_time > time())
{
return;
}
?>
<div class="notice notice-warning is-dismissible trustindex-popup" style="position: fixed; top: 50px; right: 20px; padding-right: 30px; z-index: 1">
<p>
<?php echo TrustindexPlugin::___("Hello, I am happy to see that you've been using our <strong>%s</strong> plugin for a while now!", ["Widgets for Booking.com Reviews"]); ?><br>
<?php echo TrustindexPlugin::___("Could you please help us and give it a 5-star rating on WordPress?"); ?><br><br>
<?php echo TrustindexPlugin::___("-- Thanks, Gabor M."); ?>
</p>
<p>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-booking-com/settings.php&rate_us=open"); ?>" class="trustindex-rateus" style="text-decoration: none" target="_blank">
<button class="button button-primary"><?php echo TrustindexPlugin::___("Sure, you deserve it"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-booking-com/settings.php&rate_us=later"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary"><?php echo TrustindexPlugin::___("Maybe later"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=review-widgets-for-booking-com/settings.php&rate_us=hide"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary" style="float: right"><?php echo TrustindexPlugin::___("Do not remind me again"); ?></button>
</a>
</p>
</div>
<?php
}
add_action('admin_notices', 'trustindex_rate_us_booking');
add_action('plugins_loaded', array($trustindex_pm_booking, 'plugin_loaded'));
?>