<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if (!current_user_can('edit_pages'))
{
die('The account you\'re logged in to doesn\'t have permission to access this page.');
}
if(isset($_GET['rate_us']))
{
switch(sanitize_text_field($_GET['rate_us']))
{
case 'open':
update_option($trustindex_pm_booking->get_option_name('rate-us') , 'hide', false);
$url = 'https://wordpress.org/support/plugin/'. $trustindex_pm_booking->get_plugin_slug() . '/reviews/?rate=5#new-post';
header('Location: '. $url);
die;
case 'later':
$time = time() + (30 * 86400);
update_option($trustindex_pm_booking->get_option_name('rate-us') , $time, false);
break;
case 'hide':
update_option($trustindex_pm_booking->get_option_name('rate-us') , 'hide', false);
break;
}
echo "<script type='text/javascript'>self.close();</script>";
die;
}
$tabs = [];
if($trustindex_pm_booking->is_trustindex_connected())
{
$default_tab = 'setup_trustindex_join';
$tabs[ 'Trustindex admin' ] = "setup_trustindex_join";
$tabs[ TrustindexPlugin::___("Free Widget Configurator") ] = "setup_no_reg";
}
else
{
$default_tab = 'setup_no_reg';
$tabs[ TrustindexPlugin::___("Free Widget Configurator") ] = "setup_no_reg";
}
if($trustindex_pm_booking->is_noreg_linked())
{
$tabs[ TrustindexPlugin::___("My Reviews") ] = "my_reviews";
}
$tabs[ TrustindexPlugin::___('Get Reviews') ] = "get_reviews";
$tabs[ TrustindexPlugin::___('Rate Us') ] = "rate";
if(!$trustindex_pm_booking->is_trustindex_connected())
{
$tabs[ TrustindexPlugin::___('Get more Features') ] = "setup_trustindex";
$tabs[ TrustindexPlugin::___('Log In') ] = "setup_trustindex_join";
}
$tabs[ TrustindexPlugin::___('Troubleshooting') ] = "troubleshooting";
$selected_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : null;
$subtabs = null;
$found = false;
foreach($tabs as $tab)
{
if(is_array($tab))
{
if(array_search($selected_tab, $tab) !== FALSE)
{
$found = true;
break;
}
}
else
{
if($selected_tab == $tab)
{
$found = true;
break;
}
}
}
if(!$found)
{
$selected_tab = $default_tab;
}
$http_blocked = false;
if(defined('WP_HTTP_BLOCK_EXTERNAL') && WP_HTTP_BLOCK_EXTERNAL)
{
if(!defined('WP_ACCESSIBLE_HOSTS') || strpos(WP_ACCESSIBLE_HOSTS, '*.trustindex.io') === FALSE)
{
$http_blocked = true;
}
}
?>
<div id="trustindex-plugin-settings-page">
<h1 class="ti-free-title">
<?php echo TrustindexPlugin::___("Widgets for Booking.com Reviews"); ?>
<a href="https://www.trustindex.io/ti-redirect.php?a=sys&c=wp-booking-l" target="_blank" title="Trustindex" class="ti-pull-right">
<img src="<?php echo $trustindex_pm_booking->get_plugin_file_url('static/img/trustindex.svg'); ?>" />
</a>
</h1>
<div class="container_wrapper">
<div class="container_cell" id="container-main">
<?php if($http_blocked): ?>
<div class="ti-box ti-notice-error">
<p>
<?php echo TrustindexPlugin::___("Your site cannot download our widget templates, because of your server settings not allowing that:"); ?><br /><a href="https://wordpress.org/support/article/editing-wp-config-php/#block-external-url-requests" target="_blank">https://wordpress.org/support/article/editing-wp-config-php/#block-external-url-requests</a><br /><br />
<strong><?php echo TrustindexPlugin::___("Solution"); ?></strong><br />
<?php echo TrustindexPlugin::___("a) You should define <strong>WP_HTTP_BLOCK_EXTERNAL</strong> as false"); ?><br />
<?php echo TrustindexPlugin::___("b) or you should add Trustindex as an <strong>WP_ACCESSIBLE_HOSTS</strong>: \"*.trustindex.io\""); ?><br />
</p>
</div>
<?php endif; ?>
<div class="nav-tab-wrapper">
<?php foreach($tabs as $tab_name => $tab): ?>
<?php
$is_active = $selected_tab == $tab;
$action = $tab;
if(is_array($tab))
{
$is_active = array_search($selected_tab, $tab) !== FALSE;
$action = array_shift(array_values($tab));
if($is_active)
{
$subtabs = $tab;
}
}
?>
<a
id="link-tab-<?php echo $action; ?>"
class="nav-tab<?php if($is_active): ?> nav-tab-active<?php endif; ?><?php if($tab == 'troubleshooting'): ?> nav-tab-right<?php endif; ?>"
href="<?php echo admin_url('admin.php?page='.$trustindex_pm_booking->get_plugin_slug().'/settings.php&tab='.$action); ?>"
><?php echo esc_html($tab_name); ?></a>
<?php endforeach; ?>
</div>
<?php if($subtabs): ?>
<div class="nav-tab-wrapper sub-nav">
<?php foreach($subtabs as $tab_name => $tab): ?>
<a
id="link-tab-<?php echo $tab; ?>"
class="nav-tab<?php if($selected_tab == $tab): ?> nav-tab-active<?php endif; ?>"
href="<?php echo admin_url('admin.php?page='.$trustindex_pm_booking->get_plugin_slug().'/settings.php&tab='.$tab); ?>"
><?php echo $tab_name; ?></a>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div id="tab-<?php echo $selected_tab; ?>">
<?php include( plugin_dir_path(__FILE__ ) . "tabs/".$selected_tab.".php" ); ?>
</div>
</div>

</div>
</div>