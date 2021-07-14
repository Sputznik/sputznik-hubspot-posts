<?php

$inc_files = array(
  "class-sp-hubspot-shortcode.php",
  "class-sp-hubspot-posts-shortcode.php"
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}
