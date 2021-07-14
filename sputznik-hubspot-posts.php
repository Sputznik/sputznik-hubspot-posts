<?php
/*
  Plugin Name: Sputznik Hubspot Posts
  Plugin URI: https://sputznik.com/
  Description: A simple plugin for fetching blog posts from hubspot.
  Version: 1.0.0
  Author: Stephen Anil, Sputznik
  Author URI: https://sputznik.com/
  Text Domain: sp-hubspot-posts
*/

defined( 'ABSPATH' ) or die( 'Hey you cannot access this plugin, you silly human' );

define( 'SP_HUBSPOT_DIR_URL', plugin_dir_url(__FILE__) );
define( 'SP_HUBSPOT_DIR_PATH', plugin_dir_path(__FILE__) );

$inc_files = array(
  "class-sp-hubspot-base.php",
  "class-sp-hubspot-admin.php",
  "shortcode/shortcode.php"
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}
