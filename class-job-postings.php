<?php
/**
 * Job Postings.
 *
 * @package   Job_Postings
 * @author    Gleb Makarov <gmakarov@blueglass.com>
 * @license   GPL-2.0+
 * @link      http://blueglass.ee/
 * @copyright 2017 BlueGlass Interactive
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Job_Postings extends Job_Postings_Helper{

	public static $fields;
	public static $side_position;
	public static $schema_type;

	public static $default_lang = 'en';
	public static $lang = 'en';
	public static $languages = array();

	public static function load() {

		JobSecurity::init();

		self::getLanguages();
		add_action( 'init', array('Job_Postings', 'getLanguages') );

		do_action( 'job-postings-loaded');

		JobDependencies::include_scripts();
		JobSettings::init();
		JobPostType::init();

		JobAddEdit::init();
		JobEntry::init();

		JobSingleView::init();
		JobApplicationSubmit::init();

		//self::getLanguages();

		//Job_Postings::init();
		add_action( 'init', array('Job_Postings', 'init') );	

		add_filter( 'rest_prepare_jobs',  array('Job_Postings', 'rest_prepare_custom_fields'), 10, 3 );

		add_action(	'post_edit_form_tag', array('Job_Postings_Helper','update_edit_form') );

		add_filter( 'template_include', array('Job_Postings', 'jobs_custom_template'), 10, 1 );

		if( !is_admin() ) {
			add_shortcode( 'job-postings', array('JobList', 'do_job_list') );
			add_shortcode( 'job-categories', array('JobCategory', 'do_job_categories_inline') );
			add_shortcode( 'job-categories-tree', array('JobCategory', 'do_job_category_list') );
			add_shortcode( 'job-search', array('JobSearch', 'do_job_search') );
			add_shortcode( 'job-single', array('JobSingle', 'do_job_single') );
		}


		add_action( 'after_setup_theme', array('Job_Postings', 'after_setup_template') );

		add_action( 'nav_menu_css_class', array('Job_Postings', 'add_current_nav_class'), 10, 2 );

		//add_action( 'wp_ajax_jobs_metrics_notice_seen', array('Job_Postings', 'jobs_metrics_notice_seen') );
		add_action( 'wp_ajax_jobs_metrics_attachemnt_notice_seen', array('Job_Postings', 'jobs_metrics_attachemnt_notice_seen') );

		$jobs_selected_schema = get_option( 'jobs_selected_schema' );
        if( !$jobs_selected_schema || empty($jobs_selected_schema) || $jobs_selected_schema == "default" ){  
			add_filter( 'wpseo_json_ld_output', array('Job_Postings','jp_disable_yoast_seo_schema'), 10, 1 );
		}
        else if( $jobs_selected_schema == "yoast_seo" && is_singular( 'jobs' ) ){
            add_filter( 'job-postings/disable_json_ld',  array('Job_Postings', 'jp_disable_job_postings_schema') );
			add_filter( 'wpseo_json_ld_output', array('Job_Postings','jp_disable_yoast_seo_schema'), 10, 1 );
        }

		add_action('deactivated_plugin', array('Job_Postings', 'set_deafult_on_yoast_deactivation') );
			
	}

	public static function jp_disable_yoast_seo_schema($data){
		$data = [];
		return $data;
	}

	public static function jp_disable_job_postings_schema(){
		return true;
	}

	public static function set_deafult_on_yoast_deactivation($plugin) {
		// Check if Yoast SEO plugin is deactivated
		if ($plugin === 'wordpress-seo/wp-seo.php') {
			update_option( 'jobs_selected_schema', 'default' );
		}
	}

	public static function init_current_language(){

		if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		//if (function_exists('icl_get_languages')) {

			self::$lang = ICL_LANGUAGE_CODE;

			//get list of used languages from WPML
			$langs = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0&orderby=code&order=asc' );
			//Set current language for language based variables in theme.

			if( !empty($langs) ){
				self::$languages = $langs;
			}

		}

		if ( function_exists( 'pll_the_languages' ) ) {
			self::$lang = pll_current_language();
			if( self::$lang == '' ) self::$lang = self::$default_lang;
		}

	}

	public static function init(){

		self::init_current_language();

		$default_fields = array(
				'position_title' => array(
					'type' 	=> 'text',
					'name' 	=> _x('Position title', 'jobs-field', 'job-postings'),
					'need'  => 1,
					'key'	=> 'position_title',
					'required' => true,
					'placeholder' => _x('Start typing or double click to open suggestions', 'jobs-field', 'job-postings'),
					'class' => array('position_title'),
					'sort' 	=> 'sort-left'
					),

				'position_logo' => array(
					'type' 	=> 'empty_hiring_logo',
					'name' 	=> _x('Hiring organization','job-settings', 'job-postings'),
					'need'  => 1,
					'key'	=> 'position_logo',
					'sort' 	=> 'sort-right'
					),
					
				'position_employment_type' => array(
					'type' 	=> 'checkboxes',
					'name' 	=> _x('Employment Type', 'jobs-field', 'job-postings'),
					'need'  => 2,
					'key'	=> 'position_employment_type',
					'placeholder' => _x('Start typing or double click to open suggestions', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right',
					'options' => array(
						"FULL_TIME" => __('Full-time', 'job-postings'),
						"PART_TIME" => __('Part-time', 'job-postings'),
						"CONTRACTOR" => __('Contractor', 'job-postings'),
						"TEMPORARY" => __('Temporary', 'job-postings'),
						"INTERN" => __('Intern', 'job-postings'),
						"VOLUNTEER" => __('Volunteer', 'job-postings'),
						"PER_DIEM" => __('Per diem', 'job-postings'),
						"OTHER" => __('Other', 'job-postings'),
					)
				
					),

				'position_employment_begining' => array(
					'type' 	=> 'textarea',
					'name' 	=> _x('Beginning of employment', 'jobs-field', 'job-postings'),
					'key'	=> 'position_employment_begining',
					'placeholder' => _x('Input beginning of employment', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),

				'position_employment_duration' => array(
					'type' 	=> 'text',
					'name' 	=> _x('Duration of employment', 'jobs-field', 'job-postings'),
					'key'	=> 'position_employment_duration',
					'placeholder' => _x('Input duration of employment', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),

				'position_industry' => array(
					'type' 	=> 'text',
					'name' 	=> _x('Industry', 'jobs-field', 'job-postings'),
					'key'	=> 'position_industry',
					'placeholder' => _x('Start typing or double click to open suggestions', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),

				'position_job_location' => array(
					'type' 	=> 'location',
					'name' 	=> _x('Job Location', 'jobs-field', 'job-postings'),
					'need'  => 1,
					'key'	=> 'position_job_location',
					'placeholder_st' 	=> _x('Street Address', 'jobs-field', 'job-postings'),
					'placeholder' 		=> _x('City', 'jobs-field', 'job-postings'),
					'placeholder_al' 	=> _x('Locality', 'jobs-field', 'job-postings'),
					'placeholder_ar' 	=> _x('Region', 'jobs-field', 'job-postings'),
					'placeholder_cc' 	=> _x('Country', 'jobs-field', 'job-postings'),
					'placeholder_zip' 	=> _x('Postal Code', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),

				'position_description' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Description', 'jobs-field', 'job-postings'),
					'need'  => 1,
					'key'	=> 'position_description',
					'teeny' => false,
					'placeholder' => _x('Input position description', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-left'
					),

				'position_responsibilities' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Responsibilities', 'jobs-field', 'job-postings'),
					'key'	=> 'position_responsibilities',
					'teeny' => false,
					'placeholder' => _x('Input position responsibilities', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-left'
					),

				'position_qualifications' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Qualifications', 'jobs-field', 'job-postings'),
					'key'	=> 'position_qualifications',
					'teeny' => false,
					'placeholder' => _x('Input position qualifications', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-left'
					),

				'position_job_benefits' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Job Benefits', 'jobs-field', 'job-postings'),
					'key'	=> 'position_job_benefits',
					'teeny' => false,
					'placeholder' => _x('Input position benefits', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-left'
					),

				'position_contacts' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Contacts', 'jobs-field', 'job-postings'),
					'key'	=> 'position_contacts',
					'teeny' => false,
					'placeholder' => _x('Input position contacts', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-left'
					),

				'position_work_hours' => array(
					'type' 	=> 'textarea',
					'name' 	=> _x('Working Hours', 'jobs-field', 'job-postings'),
					'key'	=> 'position_work_hours',
					'placeholder' => _x('Input position working hours', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),


				'position_base_salary' => array(
					'type' 	=> 'text',
					'name' 	=> _x('Base Salary', 'jobs-field', 'job-postings'),
					'need'  => 2,
					'key'	=> 'position_base_salary',
					'placeholder' => _x('Input position base salary', 'jobs-field', 'job-postings'),
					'description' => _x('Currency can be changed in <a target="_blank" href="edit.php?post_type=jobs&page=jp-help">Settings</a>.', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right',
					'unitText' => array(
							"HOUR" 	=> __('Per hour', 'job-postings'),
							"DAY" 	=> __('Per day', 'job-postings'),
							"WEEK" 	=> __('Per week', 'job-postings'),
							"MONTH" => __('Per month', 'job-postings'),
							"YEAR" 	=> __('Per year', 'job-postings') 
						)
					),


				'position_date_posted' => array(
					'type' 	=> 'empty_date',
					'name' 	=> _x('Date posted', 'apply-now', 'job-postings'),
					'key'	=> 'position_date_posted',
					'placeholder' => _x('No settings for this field', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),


				'position_valid_through' => array(
					'type' 	=> 'valid_through',
					'name' 	=> _x('Valid through','jobs-field', 'job-postings'),
					'need'  => 2,
					'key'	=> 'position_valid_through',
					'sort' 	=> 'sort-right'
					),

				'position_pdf_export' => array(
					'type' 	=> 'empty_pdf_export',
					'name' 	=> _x('PDF Export','apply-now', 'job-postings'),
					'key'	=> 'position_pdf_export',
					'placeholder' => _x('Export as PDF', 'apply-now', 'job-postings'),
					'sort' 	=> 'sort-right'
					),

				'position_apply_now' => array(
					'type' 	=> 'empty_apply_now',
					'name' 	=> _x('Apply now','apply-now', 'job-postings'),
					'key'	=> 'position_apply_now',
					'placeholder' => _x('Button that open modal window', 'jobs-field', 'job-postings'),
					'description' => _x('If you like to change button text, just edit it below.', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-right'
					),




				//Disabled

				'position_educationRequirements' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Education', 'jobs-field', 'job-postings'),
					'key'	=> 'position_educationRequirements',
					'teeny' => false,
					'sort' 	=> 'sort-disabled'
					),

				'position_experienceRequirements' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Experience', 'jobs-field', 'job-postings'),
					'key'	=> 'position_experienceRequirements',
					'teeny' => false,
					'sort' 	=> 'sort-disabled'
					),

				'position_skills' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Skills', 'jobs-field', 'job-postings'),
					'key'	=> 'position_skills',
					'teeny' => false,
					'sort' 	=> 'sort-disabled'
					),


				'position_button' => array(
					'type' 	=> 'custom_button',
					'name' 	=> _x('Button', 'jobs-field', 'job-postings'),
					'key'	=> 'position_button',
					'sort' 	=> 'sort-disabled'
					),

				'position_pdf_job' => array(
					'type' 	=> 'file',
					'name' 	=> _x('Attachment', 'jobs-field', 'job-postings'),
					'key'	=> 'position_pdf_job',
					'placeholder' 		=> _x('File URL', 'jobs-field', 'job-postings'),
					'placeholder_btn' 	=> _x('Button name', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-disabled'
					),


				'position_custom_text_1' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Custom text 1', 'jobs-field', 'job-postings'),
					'key'	=> 'position_custom_text_1',
					'teeny' => false,
					'sort' 	=> 'sort-disabled'
					),


				'position_custom_text_2' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Custom text 2', 'jobs-field', 'job-postings'),
					'key'	=> 'position_custom_text_2',
					'teeny' => false,
					'sort' 	=> 'sort-disabled'
					),


				'position_custom_text_3' => array(
					'type' 	=> 'tinymce',
					'name' 	=> _x('Custom text 3', 'jobs-field', 'job-postings'),
					'key'	=> 'position_custom_text_3',
					'teeny' => false,
					'sort' 	=> 'sort-disabled'
					),


				'position_inline_apply_now' => array(
					'type' 	=> 'empty_inline_apply_now',
					'key'	=> 'position_inline_apply_now',
					'name' 	=> _x('Inline "Apply now" form','apply-now', 'job-postings'),
					'description' => _x('For editing apply form, go to plugin settings.', 'jobs-field', 'job-postings'),
					'sort' 	=> 'sort-disabled'
					),
		);

		self::$fields = apply_filters('job-postings/position_fields', $default_fields);

		$sideposition = get_option('jobs_sidebar_position');
		self::$side_position = $sideposition ? $sideposition : 'right';

		// depricated
		//$schema_type = get_option( 'jobs_schema_type' );
		//$this->schema_type = $schema_type? $schema_type : 'json-ld';

		self::$schema_type = 'json-ld';


		if( isset($_GET['export-pdf']) && strip_tags($_GET['export-pdf']) != '' ){
			self::ajax_pdf_export( strip_tags($_GET['export-pdf']) );
		}
		  
	}

	public static function rest_prepare_custom_fields( $data, $post, $request ) {

		$_data = $data->data;

		$skip = array('job_confirmation_email', 'job_notify_custom_message_editor', 'job_notify_custom_message', 'job_notification_message');

		$customs 		= get_post_custom( $post->ID );
		if( $customs ){
			foreach ($customs as $key => $custom) {
				if( !in_array($key, $skip) )
					$_data[ $key ] = get_post_meta( $post->ID, $key, true );
			}
		}

		$data->data = $_data;
		return $data;
	}

	public static function getLanguages(){
		$default_lang 	= explode('-', get_bloginfo( 'language' ));
		$dlang 			= $default_lang[0];
		self::$languages[$dlang] = $dlang;

		self::$default_lang = $dlang;
		self::$lang 		= $dlang;

		if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		//if (function_exists('icl_get_languages')) {
			//get list of used languages from WPML
			$langs = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0&orderby=code&order=asc' );
 
			//Set current language for language based variables in theme.

			if( !empty($langs) ){
				self::$languages = $langs;
			}

		}else{
			$default_lang 	= explode('_', get_locale());
			$dlang 			= $default_lang[0];
			self::$languages[$dlang] = $dlang;

			self::$default_lang = $dlang;
			self::$lang 		= $dlang;
		}
		
		if ( function_exists( 'pll_the_languages' ) ) {
			$langs = pll_languages_list( array('raw'=>1) );

			if( !empty($langs) ){
				$langs_proccessed = array();
				foreach($langs as $key => $lang){
					$langs_proccessed[ $lang ] = $lang;
				}
				self::$languages = $langs_proccessed;
			}
		}
	}

	public static function getLang(){

		return self::$lang;
	}

	
	public static function jobs_metrics_notice_seen(){
		global $BlueGlassAnalytics;

		if( isset($_POST['status']) && $_POST['status'] != '' ){
			switch (strip_tags($_POST['status'])) {
				case 1:
					update_option('jobs_metrics_shareable', 'yes');
					break;

				default:
					update_option('jobs_metrics_shareable', 'no');
					break;
			}

			update_option('jobs_metrics_notice_seen_v2', 'seen');

			$BlueGlassAnalytics->track_metrics();

			echo 'ok';

			exit();
		}
	}

	public static function jobs_metrics_attachemnt_notice_seen(){
		update_option('jobs_file_location_notice_seen_v2', 'seen');
		echo 'ok';
		exit();
	}


	public static function jobs_plugin_add_defaults(){
		/*
		$default_lang 	= explode('-', get_bloginfo( 'language' ));
		$dlang 			= $default_lang[0];
		$languages = array();
		$languages[$dlang] = $dlang;

		if (function_exists('icl_get_languages')) {
			//get list of used languages from WPML
			$langs = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
			//Set current language for language based variables in theme.

			if( !empty($langs) ){
				$languages = $langs;
			}
		}

		foreach ($languages as $lang) {
			if( get_option('jobs_currency_position'.'_'.$lang) == '' ){
				update_option('jobs_currency_position'.'_'.$lang, 'before' );
			}

			if( get_option('jobs_currency_symbol'.'_'.$lang) == '' ){
				update_option('jobs_currency_symbol'.'_'.$lang, '€' );
			}

			if( get_option('jobs_hiring_organization'.'_'.$lang) == '' ){
				update_option('jobs_hiring_organization'.'_'.$lang, get_option('blogname') );
			}


			if( get_option('jobs_preview_cta'.'_'.$lang) == '' ){
				update_option('jobs_preview_cta'.'_'.$lang, _x('View', 'job-postings') );
			}
		}
		*/

		if( get_option('jobs_posts_per_page') == '' ){
			update_option('jobs_posts_per_page', '10' );
		}

		if( get_option('jobs_preview_cta') == '' ){
			update_option('jobs_preview_cta', 'View' );
		}

		if( get_option('jobs_schema_type') == '' ){
			update_option('jobs_schema_type', 'json-ld' );
		}

	}

	public static function after_setup_template(){
		add_theme_support( 'post-thumbnails' );
		add_image_size(
			'job-image',
			apply_filters('job-image-width', 1000),
			apply_filters('job-image-height', 400),
			true );
	}

	public static function jobs_custom_template( $template ) {
		global $wp_query, $post;

		if( !$post ) return $template;
		if( is_search() ) return $template;

		if ($post->post_type == "jobs"){

			$jobs_single_template = locate_template( array( 'jobs/single-jobs.php' ) );
			if ( '' != $jobs_single_template ) {
				return $jobs_single_template;
			}else{
				$default_jobs_single_template = JOBPOSTINGSPATH . 'templates/single-jobs.php';
				return $default_jobs_single_template;
			}

		}

		return $template;

	}

	


	public static function ajax_pdf_export( $post_id ){
		if( !$post_id ) return;

		if( !class_exists('TCPDF') ) require_once(plugin_dir_path( __FILE__ ) . 'tcpdf/tcpdf.php');
		require_once(plugin_dir_path( __FILE__ ) . 'include/class-pdf-export.php');

		$jobpdf = new jobPDFExport( $post_id, self::$fields, self::$lang );
	}




	public static function customStyles(){
		//maybe include: || !is_singular()
		if( defined("REST_REQUEST") && REST_REQUEST == true ) return;

		$jobs_button_bg_color 		= get_option( 'jobs_button_bg_color' );
		$jobs_button_bg_color_hover = get_option( 'jobs_button_bg_color_hover' );
		$jobs_button_text_color 	= get_option( 'jobs_button_text_color' );
		$jobs_heading_text_color 	= get_option( 'jobs_heading_text_color' );
		$jobs_subheading_text_color = get_option( 'jobs_subheading_text_color' );
		$jobs_list_item_bg 			= get_option( 'jobs_list_item_bg' );
		$jobs_list_item_border 		= get_option( 'jobs_list_item_border' );
		$jobs_content_heading_color = get_option( 'jobs_content_heading_color' );
		$jobs_content_text_color 	= get_option( 'jobs_content_text_color' );

		$jobs_button_roundness 		= get_option( 'jobs_button_roundness' );
		$jobs_box_roundness 		= get_option( 'jobs_box_roundness' );

		$jobs_custom_css 			= get_option( 'jobs_custom_css' );

		$style = '<style id="job-postings-css" type="text/css">';

			if( $jobs_button_roundness )
				$style .= 'body .job-listing .job-preview .job-cta .apply-btn,
							body .jp-apply-button,
							body .job-listing-categories .job-category,
							body .jobs-search .job-search,
							body .jobs-modal-form .job-submit,
							body .jobs-modal-form .choose_file,
							body .jobs-modal-form .choose_file_multi,
							body .jobs-modal-form .choose_file_multi_add	{
								border-radius: '.$jobs_button_roundness.';
							}';


			if( $jobs_box_roundness )
				$style .= 'body .job-listing .job-preview,
							body .job-post .job-side .job-content-wrap{
								border-radius: '.$jobs_box_roundness.';
							}';


			if( $jobs_button_bg_color_hover )
				$style .= 'body .jp-apply-button:hover,
							body .primary-style:hover,
							body .job-listing .job-preview .job-cta .apply-btn:hover,
							body .jobs-modal-form .job-submit:hover,
							body .jobs-modal-form .choose_file:hover,
							body .jobs-modal-form .choose_file_multi:hover,
							body .jobs-modal-form .choose_file_multi_add:hover,
							body .job-listing-categories .job-category.active:hover,
							body .jobs-modal-form .progress-button button:hover,
							body .jobs-modal-form .progress-button button:active	{
								background-color: '.$jobs_button_bg_color_hover.';
							}
							body .job-listing-categories .job-category.active	{
								border-color: '.$jobs_button_bg_color_hover.';
							}
							';

			// button background color
			if( $jobs_button_bg_color ) {
				$style .= 'body .jp-apply-button,
							body .primary-style,
							body .job-listing .job-preview .job-cta .apply-btn,
							body .jobs-modal-form .job-submit,
							body .job-submit,
							body .jobs-modal-form .choose_file,
							body .jobs-modal-form .choose_file_multi,
							body .jobs-modal-form .choose_file_multi_add,
							body .job-listing-categories .job-category.active,
							body .jobs-modal-form .progress-button button	{
								background-color: '.$jobs_button_bg_color.';
							}

							body .select2-results__option--highlighted	{
								background-color: '.$jobs_button_bg_color.' !important;
							}

							body .jobs-search .job-search-submit svg	{
								fill: '.$jobs_button_bg_color.';
							}

							body .jobs-modal-form .progress-button svg.progress-circle path {
								stroke: #1ecd97;
							}
							';
			}


			// button text color
			if( $jobs_button_text_color )
				$style .= 'body .jp-apply-button, body .primary-style,
							body .job-listing .job-preview .job-cta .apply-btn,
							body .job-listing-categories .job-category.active,
							body .jobs-modal-form .job-submit,
							body .job-content-wrap .jobs-row-apply .job-submit	{
								color: '.$jobs_button_text_color.';
							}
							body .select2-results__option--highlighted	{
								color: '.$jobs_button_text_color.' !important;
							}
							';


			// heading text color
			if( $jobs_heading_text_color )
				$style .= 'body .job-post .jobs-row .jobs-row-label span,
							body .job-listing .job-preview .job-content h5 a,
							body .jobs-row-apply .jobs-modal-form h3,
							body .jobs-modal-form .jobs-section-row .jobs-section-heading,
							body .jobs-modal-form .jobs-modal-input .input-label,
							body .jobs-modal-form h4{
								color: '.$jobs_heading_text_color.';
							}';



			// subheading text color
			if( $jobs_subheading_text_color ){
				$style .= 'body .job-side .jobs-row-input,
							body .job-side .job-pdf-export,
							body .job-listing .job-preview .job-content .job-additional-information,
							body .jobs-modal-form .checkbox_field .checkbox-label .checkbox-text, 
							body .jobs-modal-form .radio_field .radio-label .radio-text{
								color: '.$jobs_subheading_text_color.';
							}';
				$style .= 'body .job-side .jobs-row-input svg path{
								fill: '.$jobs_subheading_text_color.';
							}';
			}


			// box background color
			if( $jobs_list_item_bg )
				$style .= 'body .job-listing .job-preview,
							body .job-post .job-side .job-content-wrap{
								background-color: '.$jobs_list_item_bg.';
							}';

			// box border color
			if( $jobs_list_item_border )
				$style .= 'body .job-listing .job-preview,
							body .job-post .job-side .job-content-wrap,
							body .job-post .jobs-row .jobs-row-label span,
							body .job-listing-categories .job-category,
							body .jobs-row-apply .jobs-modal-form h3{
								border-color: '.$jobs_list_item_border.';
							}';


			// content heading text color
			if( $jobs_content_heading_color )
				$style .= 'body .job-post .job-content .jobs-row .jobs-row-label span{
								color: '.$jobs_content_heading_color.';
							}';

			// content text color
			if( $jobs_content_text_color )
				$style .= 'body .job-post .job-content .jobs-row .jobs-row-input,
							body .job-post .job-content .jobs-row .jobs-row-input p,
							body .job-post .job-content .jobs-row .jobs-row-input ol,
							body .job-post .job-content .jobs-row .jobs-row-input ul{
								color: '.$jobs_content_text_color.';
							}
							body .job-post .job-content .jobs-row .jobs-row-input svg path{
								fill: '.$jobs_content_text_color.';
							}
							';


			// Custom CSS
			if( $jobs_custom_css )
				$style .= strip_tags($jobs_custom_css);

		$style .= '</style>';

		echo $style;
	}
}

//$job_postings = new Job_Postings();
//$job_postings::load();


function jfw_buildSelect( $name, $value ){
	$out = '';
	$out .= '<select class="field_type_select" name="'.$name.'">';
		$out .= '<option value="name" '.selected($value, 'name', false).'>'. _x("Name", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="email" '.selected($value, 'email', false).'>'. _x("E-mail", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="phone" '.selected($value, 'phone', false).'>'. _x("Phone", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="text" '.selected($value, 'text', false).'>'. _x("Text", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="textarea" '.selected($value, 'textarea', false).'>'. _x("Textarea", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="file" '.selected($value, 'file', false).'>'. _x("File upload", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="file_multi" '.selected($value, 'file_multi', false).'>'. _x("Multi file upload", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="checkbox" '.selected($value, 'checkbox', false).'>'. _x("Checkbox", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="radio" '.selected($value, 'radio', false).'>'. _x("Radio", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="select" '.selected($value, 'select', false).'>'. _x("Select", "job-modal-field", "job-postings") .'</option>';
		$out .= '<option value="section" '.selected($value, 'section', false).'>'. _x("Section", "job-modal-field", "job-postings") .'</option>';
	$out .= '</select>';

	return $out;
}

function jfw_get_lang(){
	if( defined('ICL_LANGUAGE_CODE') ){
		return ICL_LANGUAGE_CODE;
	}
	$default_lang 	= explode('-', get_bloginfo( 'language' ));
	return $default_lang[0];
}

function jfw_get_languages(){
	if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	//if( function_exists('icl_get_languages')){
		$return = array();
		$langs = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0&orderby=code&order=asc' );

		if( ! empty( $languages ) ) {
			foreach( $languages as $l ){
				$return[ $l['language_code'] ] = $l['translated_name'];
			}

		}
	}else{
		$default_lang 	= explode('-', get_bloginfo( 'language' ));
		$dlang 			= $default_lang[0];

		$return = array($dlang => 'Default language ('.$dlang.')');
	}


	return $return;
}

function jfw_find_key($array, $key, $value){
	$results = array();
	jfw_search_r($array, $key, $value, $results);
	return $results;
}

function jfw_search_r($array, $key, $value, &$results){
	if (!is_array($array)) {
		return;
	}

	if (isset($array[$key]) && $array[$key] == $value) {
		$results[] = $array;
	}
	
	foreach ($array as $subarray) {
		jfw_search_r($subarray, $key, $value, $results);
	}
}


if (!function_exists('jfwp_write_log')) {
    function jfwp_write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}