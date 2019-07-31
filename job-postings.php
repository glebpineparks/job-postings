<?php
/*
Plugin Name: Jobs for Wordpress
Plugin URI: https://wordpress.org/plugins/job-postings/
Description: WordPress plugin that make it easy to add job postings to your company’s website in a structured way.
Author: BlueGlass
Version: 2.2.9
Author URI: http://blueglass.ee/en/
Text Domain: job-postings
Domain Path: /languages
*/


if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('JOBPOSTINGSVERSION', '2.2.9');
define('JOBPOSTINGSPATH', plugin_dir_path( __FILE__ ));
define('JOBPOSTINGSURL', plugin_dir_url(__FILE__));

// Load text domain for translations
function job_postings_plugin_langs_init() {
	load_plugin_textdomain( 'job-postings', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('init', 'job_postings_plugin_langs_init');


include_once('include/class-bg-analytics.php');

//do analytics
$BlueGlassAnalytics = new BG_Analytics();

/*
spl_autoload_register(function($className) {
	$className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
	$file = JOBPOSTINGSPATH . 'include/class-' . $className . '.php';
	if( file_exists($file) ) include_once $file;
});
*/

include_once('verify/class-jobs-module-verify.php');

include_once('include/class-job-security.php');

include_once('include/class-job-posting-helper.php');
include_once('include/class-job-settings.php');
include_once('include/class-job-post-type.php');
include_once('include/class-job-dependencies.php');
include_once('include/class-job-add-edit.php');
include_once('include/class-job-entry.php');

// Include main class
include_once('class-job-postings.php');

include_once('include/class-job-single-view.php');
include_once('include/class-job-apply-form.php');
include_once('include/class-job-notifications.php');
include_once('include/class-job-application-submit.php');

//Shortcodes
include_once('include/shortcodes/class-job-category.php');
include_once('include/shortcodes/class-job-search.php');
include_once('include/shortcodes/class-job-list.php');
include_once('include/shortcodes/class-job-single.php');


Job_Postings::load();
//add_action( 'init', array( 'Job_Postings', 'load' ), 100);


// Install defaults on plugin activation
register_activation_hook( __FILE__, array('Job_Postings', 'jobs_plugin_add_defaults') );

?>
