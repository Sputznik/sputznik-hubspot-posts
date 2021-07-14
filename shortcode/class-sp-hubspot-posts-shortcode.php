<?php
class SP_HUBSPOT_POSTS_SHORTCODE extends SP_HUBSPOT_SHORTCODE{

	function __construct(){

		$this->shortcode 	= 'sp_hubspot_posts';
		$this->template 	= 'hubspot-3grid.php';

		parent::__construct();

	}

	function unique_atts(){
		return array('limit', 'state','sort');
	}

	function get_default_atts(){
		return array(
      'limit' => 3,
      'state' => 'published',
      'sort'  => '-publishDate', // sorts blog posts in descending order based on publishDate
			'cache'	=> 10 	// cache for minutes
		);
	}

}

SP_HUBSPOT_POSTS_SHORTCODE::getInstance();
