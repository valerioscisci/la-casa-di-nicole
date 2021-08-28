<?php
require_once plugin_dir_path( __FILE__ ) . 'plugin-load.php';
$trustindex_pm_booking = new TrustindexPlugin("booking", __FILE__, "6.9", "Widgets for Booking.com Reviews", "Booking.com");
$trustindex_pm_booking->uninstall();
?>