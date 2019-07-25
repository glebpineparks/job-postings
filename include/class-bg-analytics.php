<?php 
/**
 * BG_Analytics
 *
 * @package   BG_Analytics
 * @author    Gleb Makarov <gmakarov@blueglass.com>
 * @license   GPL-2.0+
 * @link      http://blueglass.ee/
 * @copyright 2018 BlueGlass Interactive
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BG_Analytics{

	public $url 		= 'https://metrics.blueglass.ee/';
	private $key 		= 'amZ3LTg3YmIyNGJhLTM5MWMtNDZkYi1hY2EzLWYxOWU5NDJjZTRiNA==';
	private $api_key 	= '0261e1ff-e4af-4111-aa52-f5c30ed0806b';

	function __construct() {

		$this->metrics_notice_seen 	= get_option('jobs_metrics_notice_seen');
		$this->metrics_shareable 	= get_option('jobs_metrics_shareable');

		//var_dump($this->metrics_notice_seen);

		if( !$this->metrics_notice_seen ) 
			add_action( 'admin_notices', array($this, 'maybe_display_notice') );
		
		if( $this->send_analytics_data() ){
			$this->track_metrics();
		}

		//$this->track_metrics();
	}


	/**
	 * Determines if we should send the analytics data
	 *
	 * @since 2.11
	 * @author Remy Perona
	 *
	 * @return bool True if we should send them, false otherwise
	 */
	public function send_analytics_data() {
		// if ( !$this->metrics_shareable || $this->metrics_shareable == 'no' ) {
		// 	return false;
		// }

		// if ( ! current_user_can( 'administrator' ) ) {
		// 	return false;
		// }

		if ( false === get_transient( 'jobs_send_analytics_data' ) ) {
			set_transient( 'jobs_send_analytics_data', 1, 3 * DAY_IN_SECONDS );
			//set_transient( 'jobs_send_analytics_data', 1, MINUTE_IN_SECONDS / 6 );
			return true;
		}

		return false;
	}

	public function maybe_display_notice(){
		//$this->track_metrics();
		
		$class = 'notice notice-info jobs-notice';
		$message = '<div class="jobs-notice-wrap">';

			$message .= '<h3>' . __( 'Job for WordPress', 'job-posting' ) . '</h3>';

			$message .= __( 'We would like to collect some anonymous data about usage of the plugin. This will help us to improve the plugin in future releases. ', 'job-posting' );

			$message .= '</br>';

			$message .= sprintf(__( '<a href="%s">What info will we collect?</a>', 'job-posting'), esc_attr( site_url('/wp-admin/edit.php?post_type=jobs&page=jp-help#anonymous_metrics') ), $message ); ;

			$message .= '<div class="jobs-notice-actions">';
				$message .= __( '<a href="#agree" class="jobs-metrics-agree">I agree to share anonymous data</a>', 'job-posting' );
				$message .= __( '<a href="#agree" class="jobs-metrics-cancel">No, next time</a>', 'job-posting' );
				$message .= '<span class="spinner"></span>';
			$message .= '</div>';

		$message .= '</div>';

		printf( '<div class="%1$s">%2$s</div>', esc_attr( $class ), $message ); 

	}

	public function get_analytics_data() {
		global $wp_version, $is_nginx, $is_apache, $is_iis7, $is_IIS;

		$jobs_customer_id = get_option('jobs_metrics_customer_id');

		if( !$jobs_customer_id ){
			$jobs_customer_id = uniqid('jfw-') . time();
			update_option('jobs_metrics_customer_id', $jobs_customer_id);
		}

		if ( !$this->metrics_shareable || $this->metrics_shareable == 'no' ) {
			$theme              		= 'Unknown';
			$data['domain'] 			= 'Unknown';

			$data['customer_id'] 		= $jobs_customer_id;

			$data['plugin_version'] 	= JOBPOSTINGSVERSION;

			$data['web_server'] 		= 'Unknown';

			$data['php_version']       	= 'Unknown';
			$data['wordpress_version'] 	= preg_replace( '@^(\d\.\d+).*@', '\1', $wp_version );
			$data['current_theme']     	= 'Unknown';
			$data['locale']            	= 'Unknown';
			$data['multisite']         	= 'Unknown';
			$data['active_jobs']        = $this->get_active_jobs();

		}else{
			$theme              		= wp_get_theme();
			$locale             		= explode( '_', get_locale() );

			$parse_site_url 			= parse_url(site_url());

			$data['domain'] 			= $parse_site_url['host'];

			$data['customer_id'] 		= $jobs_customer_id;

			$data['plugin_version'] 	= JOBPOSTINGSVERSION;

			$data['web_server'] 		= 'Unknown';
			if ( $is_nginx ) {
				$data['web_server'] = 'NGINX';
			} elseif ( $is_apache ) {
				$data['web_server'] = 'Apache';
			} elseif ( $is_iis7 ) {
				$data['web_server'] = 'IIS 7';
			} elseif ( $is_IIS ) {
				$data['web_server'] = 'IIS';
			}

			$data['php_version']       	= preg_replace( '@^(\d\.\d+).*@', '\1', phpversion() );
			$data['wordpress_version'] 	= preg_replace( '@^(\d\.\d+).*@', '\1', $wp_version );
			$data['current_theme']     	= $theme->get( 'Name' );
			$data['locale']            	= $locale[0];
			$data['multisite']         	= is_multisite();
			$data['active_jobs']        = $this->get_active_jobs();
		}

		return $data;
	}


	public function get_active_jobs(){

		$active_jobs = get_option('jobs_metrics_active_postings');

		if( !$active_jobs ){
			$active_jobs = get_posts(array(
				'post_type' 		=> 'jobs',
				'post_status' 		=> 'publish',
				'posts_per_page' 	=> -1
			));

			$active_jobs = count($active_jobs);

			update_option('jobs_metrics_active_postings', $active_jobs);
		}

		return $active_jobs;
	}

	public function ssl_encrypt($data, $key) {
		if( !$key ) return $data;
	    // Remove the base64 encoding from our key
	    $encryption_key = base64_decode($key);
	    // Generate an initialization vector
	    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	    // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
	    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
	    // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
	    return base64_encode($encrypted . '::' . $iv);
	}


	public function track_metrics(){

		$metrics = $this->get_analytics_data();
		$metrics['date'] = date('Y-m-d');

		$data_json = json_encode($metrics, true);
		//$metrics = $this->ssl_encrypt($data_json, $this->key);

		$response = wp_remote_post( $this->url . 'track/'.'?key='.$this->api_key, array(
				'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
				'timeout'	=> 60000,
				'body' 		=> $data_json
		    )
		);

		//print_r('<pre>');
		// //print_r( $metrics );
		//print_r( wp_remote_retrieve_body($response) );
		//print_r('</pre>');
	}

}


?>