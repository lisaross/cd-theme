<?php
/**
 * Theme functions and definitions
 *
 * @package CottageDreams
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function cottage_dreams_enqueue_scripts() {
	wp_enqueue_style(
		'cottage-dreams-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'cottage_dreams_enqueue_scripts' );

function admin_color_scheme() {
	global $_wp_admin_css_colors;
	$_wp_admin_css_colors = 0;
}
add_action('admin_head', 'admin_color_scheme');


// Callback function to remove default bio field from user profile page & re-title the section
// ------------------------------------------------------------------
// Thanks to original code found here: https://wordpress.org/support/topic/remove-the-bio-section-from-user-profile
// More reference: http://wordpress.stackexchange.com/questions/49643/remove-personal-options-section-from-profile
// Alternate examples: http://wordpress.stackexchange.com/questions/38819/how-to-remove-biography-from-user-profile-admin-page

if(!function_exists('remove_plain_bio')){
	function remove_bio_box($buffer){
		// $buffer = str_replace('<h3>About Yourself</h3>','<h3>User Password</h3>',$buffer);
		$buffer = preg_replace('/<tr class=\"user-description-wrap\"[\s\S]*?<\/tr>/','',$buffer,1);
		return $buffer;
	}
	function user_profile_subject_start(){ ob_start('remove_bio_box'); }
	function user_profile_subject_end(){ ob_end_flush(); }
}
add_action('admin_head-profile.php','user_profile_subject_start');
add_action('admin_footer-profile.php','user_profile_subject_end');

add_action( 'load-profile.php', function()
{
   add_filter( 'option_show_avatars', '__return_false' );
} );

//Disable Default Dashboard Widgets
function remove_dashboard_meta() {
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); //Removes the 'incoming links' widget
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); //Removes the 'plugins' widget
    remove_meta_box('dashboard_primary', 'dashboard', 'normal'); //Removes the 'WordPress News' widget
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal'); //Removes the secondary widget
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); //Removes the 'Quick Draft' widget
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side'); //Removes the 'Recent Drafts' widget
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); //Removes the 'Activity' widget
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); //Removes the 'At a Glance' widget
    remove_meta_box('dashboard_activity', 'dashboard', 'normal'); //Removes the 'Activity' widget (since 3.8)
    remove_meta_box('rg_forms_dashboard', 'dashboard', 'normal'); //Removes the 'Activity' widget (since 3.8)
	remove_meta_box('e-dashboard-overview', 'dashboard', 'normal'); // Removes the Elementor widget
	remove_meta_box('dce-dashboard-overview', 'dashboard', 'normal'); // Removes the Dynamic Content OOO widget
    remove_action('admin_notices', 'update_nag');
}
add_action('admin_init', 'remove_dashboard_meta');

// Create Cottage Dreams Options Page
if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'CD Settings',
		'menu_title'	=> 'CD Settings',
		'menu_slug' 	=> 'cd-settings',
		'capability'	=> 'cdadmin',
		'redirect'		=> false,
		'icon_url' => 'dashicons-images-alt2',
		'position' => 1
	));

}

// remove h2 titles from admin to clean up
add_action('admin_head', 'cd_admin_css');

function cd_admin_css() {
  echo '<style>
    form#your-profile h2{
      display:none;
    }
  </style>';
}

// set default posts per page in admin to 200
add_action( 'pre_get_posts',  'set_posts_per_page'  );
function set_posts_per_page( $query ) {

  if ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_search() ) ) {
    $query->set( 'posts_per_page', 200 );
  }
  elseif ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_archive() ) ) {
    $query->set( 'posts_per_page', 200 );
  }

}
