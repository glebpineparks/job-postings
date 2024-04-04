<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobAddEdit
{
	public static $tabs;

    public static function init(){

		do_action( 'job-postings/add-edit-loaded');

		self::$tabs = array(
			'job_form' => _x('Position details', 'job-meta-box', 'job-postings'),
			'job_settings' => _x('Settings', 'job-meta-box', 'job-postings'),
			'job_confirmation' => _x('Confirmation', 'job-meta-box', 'job-postings'),
			'job_notification' => _x('Notification', 'job-meta-box', 'job-postings'),
		);
		
		self::$tabs = apply_filters('job-postings/job-tabs', self::$tabs);

		add_filter( 'job-postings/position_fields', array('JobAddEdit', 'jobs_default_field_sorting') );
		
		add_action( 'add_meta_boxes', array('JobAddEdit', 'add_meta_boxes'), 10, 2 );
		add_action( 'save_post', array('JobAddEdit', 'save') );
	}
	
	/* 
		Apply default sorting from setting page
	*/
	public static function jobs_default_field_sorting( $fields ){
		$default_name = 'jobs_default_field_selection';
		$default_fields = get_option($default_name);
		
		if( !empty($default_fields) && is_array($default_fields) ){
			foreach ($default_fields as $key => $default_sorting) {
				if(isset($fields[$key])) $fields[$key]['sort'] = $default_sorting;
			}
		}
	
		return $fields;
	}

    public static function add_meta_boxes(){

        add_meta_box(
            'jobs-postings-meta-box',
            'Jobs for WP - ' . __( 'Position details', 'job-postings' ),
            array('JobAddEdit', 'render_job_fileds_metabox'),
            'jobs',
            'normal',
            'high'
        );

        add_meta_box(
            'jobs-postings-guide-meta-box',
            __( 'Structured Data', 'job-postings' ),
            array('JobAddEdit', 'render_job_guide_metabox'),
            'jobs',
            'side',
            'default'
		);
		
        add_meta_box(
            'jobs-postings-disabled-meta-box',
            __( 'Inactive widgets', 'job-postings' ),
            array('JobAddEdit', 'render_job_disabled_fields_metabox'),
            'jobs',
            'side',
            'default'
        );
    }


	public static function render_job_guide_metabox( $post ){
		echo '<div id="job-postings-guide">';
			echo '<h4>'.__('Completeness', 'job-postings').'</h4>';

			echo '<div class="job-completeness job-completeness-required"><span class="required-fields">'.__('Required fields', 'job-postings').'</span><span class="bar"></span></div>';

			echo '<div class="job-completeness job-completeness-recommended"><span class="recommended-fields">'.__('Recommended fields', 'job-postings').'</span><span class="bar"></span></div>';

			echo '<div class="job-completeness job-completeness-all"><span class="all-fields">'.__('All fields', 'job-postings').'</span><span class="bar"></span></div>';
			
			echo '<p class="jfw_hint">'.__("Bars indicate the completeness of job offer", 'job-postings').'</p>';
		echo '</div>';

		echo '<div id="job-postings-testing-tool">';
			echo '<h4>'.__('Structured Data Testing Tool', 'job-postings').'</h4>';

			$test = 'https://search.google.com/structured-data/testing-tool/u/0/#url='.urlencode( get_permalink($post->ID) );
			echo '<a href="'.$test.'" target="_blank" class="button">'.__('Go to Testing Tool', 'job-postings').'</a>';
			echo '<p class="jfw_hint">'.__("Opens in new window. Job offer must be published.", 'job-postings').'</p>';
		echo '</div>';
	}

    public static function render_job_disabled_fields_metabox( $post ){

        switch (Job_Postings::$side_position) {
            case 'left':
                $append_class_1 = 'jobs-wrapper-sortable-right';
                $append_class_2 = 'jobs-wrapper-sortable-left';
                break;

            default:
                $append_class_1 = 'jobs-wrapper-sortable-left';
                $append_class_2 = 'jobs-wrapper-sortable-right';
                break;
        }

        echo '<div class="jobs-wrapper menu-instructions-inactive jobs-wrapper-disabled jobs-wrapper-sortable-disabled connectedSortable" data-sort="sort-disabled">';
            self::renderFields( Job_Postings::$fields, $post, 'sort-disabled', $append_class_1, $append_class_2 );
        echo '</div>';
	}
	

	public static function add_tabs(){
		if( !empty(self::$tabs) && is_array(self::$tabs) ){
			$out = '';
			foreach( self::$tabs as $id => $name ){
				$current = '';
				if($id == 'job_form') $current = 'current';
				$out .= '<li class="tab_'.$id.'"><a href="#'.$id.'" class="'.$current.'">'.esc_attr($name).'</a> </li>';
			}
			return $out;
		}
	}


    public static function render_job_fileds_metabox( $post ){

        wp_nonce_field( 'job_postings_meta_box_nonce', 'jp-meta_box_nonce' );

		$post_id = $post->ID;

        $out = '<div class="job_tabs">';
        $out .= '<div class="wp-filter">';
            $out .= '<ul class="job_tab filter-links">';
				$out .= self::add_tabs();
			$out .= '</ul>';
        $out .= '</div>';

        switch (Job_Postings::$side_position) {
            case 'left':
                $append_class_1 = 'jobs-wrapper-sortable-right';
                $append_class_2 = 'jobs-wrapper-sortable-left';

                $class_1 		= 'jobs-wrapper-right jobs-wrapper-sortable-right';
                $class_2 		= 'jobs-wrapper-left jobs-wrapper-sortable-left';

                $sort_type_1 	= 'sort-right';
                $sort_type_2 	= 'sort-left';
                break;

            default:
                $append_class_1 = 'jobs-wrapper-sortable-left';
                $append_class_2 = 'jobs-wrapper-sortable-right';

                $class_1 		= 'jobs-wrapper-left jobs-wrapper-sortable-left';
                $class_2 		= 'jobs-wrapper-right jobs-wrapper-sortable-right';

                $sort_type_1 	= 'sort-left';
                $sort_type_2 	= 'sort-right';
                break;
        }

		// Output tabs
		echo $out;

		if( !empty(self::$tabs) && is_array(self::$tabs) ){
			$out = '';
			foreach( self::$tabs as $id => $name ){
				switch($id){
					case 'job_form':
						echo '<div id="job_form" class="job_tab_content clearfix '.$sort_type_2.'">';
							echo '<div class="jobs-wrapper menu-instructions-inactive jobs_match_height '.$class_1.' connectedSortable" data-sort="'.$sort_type_1.'">';
								self::renderFields( Job_Postings::$fields, $post, $sort_type_1, $append_class_1, $append_class_2 );
							echo '</div>';
							echo '<div class="jobs-wrapper menu-instructions-inactive jobs_match_height '.$class_2.' connectedSortable" data-sort="'.$sort_type_2.'">';
								self::renderFields( Job_Postings::$fields, $post, $sort_type_2, $append_class_1, $append_class_2 );
							echo '</div>';
						echo '</div>';
						echo '<div class="clearfix" style="clear:both"></div>';
					break;

					case 'job_settings':
						echo '<div id="job_settings" class="job_tab_content" style="display: none;">';
							echo '<div class="jobs-wrapper">';
								include_once( JOBPOSTINGSPATH . 'include/views/job-settings.php');
							echo '</div>';
						echo '</div>';
					break;

					case 'job_confirmation':
						echo '<div id="job_confirmation" class="job_tab_content" style="display: none;">';
							echo '<div class="jobs-wrapper">';
								include_once( JOBPOSTINGSPATH . 'include/views/job-confirmation.php');
							echo '</div>';
						echo '</div>';
					break;
					
					case 'job_notification':
						echo '<div id="job_notification" class="job_tab_content" style="display: none;">';
							echo '<div class="jobs-wrapper">';
								include_once( JOBPOSTINGSPATH . 'include/views/job-notification.php');
							echo '</div>';
						echo '</div>';
					break;

					default:
						echo '<div id="'.$id.'" class="job_tab_content" style="display: none;">';
							echo '<div class="jobs-wrapper">';
								do_action("job-postings/custom-tab-{$id}", $post_id, $post);
							echo '</div>';
						echo '</div>';
					break;
				}
			}
		}
            

        echo '</div>';
	}
	

	public static function prepareSortedFields( $fields, $position, $adding_new_job = false, $post_id = null ){
		$i = 0;
		foreach ($fields as $index => $field) {
			//$name 		= isset($field['name']) ? $field['name'] : '';
			$key 		= isset($field['key']) ? $field['key'] : false;
			if( !$key ) continue;

			if( !$adding_new_job && $post_id != null ){
				$sort_index = get_post_meta( $post_id, 'sort-'.$key, true );
				if( !$sort_index ) $sort_index = $fields[$index]['sort'];
			}else{
				$sort_index = $fields[$index]['sort'];
			}

			// if( strpos($sort_index, $position) === false ){
			// 	unset( $fields[$index] );
			// 	continue;
			// }

			if( $position == 'sort-disabled' ){
				if( strpos($sort_index, $position) === false && strpos($sort_index, 'str') !== -1 ){
					unset( $fields[$index] );
					continue;
				}
			}else{
				if( strpos($sort_index, $position) === false ){
					unset( $fields[$index] );
					continue;
				}
			}

			$sort_index = str_replace($position.'-', '', $sort_index);

			if( $sort_index == '' || $sort_index == 'sort-disabled' || $sort_index == 'sort-right' || $sort_index == 'sort-left' ) $sort_index = $i;

			$fields[$index]['sort'] = (int) $sort_index;

			$i++;
		}
		

		usort($fields, array('Job_Postings_Helper', 'sortByOrder') );

		return $fields;
	}



    public static function renderFields( $fields, $post, $position = 'sort-left', $append_class_1 = '', $append_class_2 = ''  ){
    	global $pagenow, $typenow;
		$post_id 	= $post->ID;

		$adding_new_job = false;
		if (in_array( $pagenow, array( 'post-new.php' ) ) && "jobs" == $typenow){
			$adding_new_job = true;
		}

    	$post_id 	= $post->ID;

    	if( $fields ){

    		$fields_copy = $fields;


			$tpl = '';

			$tpl .= '<input type="file" name="{key}" id="{key}" class="inputfile inputfile-6" />';
			$tpl .= '<label for="{key}"><span>{filename}</span> <strong><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> Choose a file...</strong></label>';

   		 	$script = '<script type="x-tmpl-mustache" id="file-upload-tpl">';
   		 		$script .= $tpl;
   		 	$script .= '</script>';

   		 	echo $script;

			// SORT
			$fields = self::prepareSortedFields( $fields, $position, $adding_new_job, $post_id );
			   
			/*
	   		print_r('<pre>');
	   		print_r($fields);
	   		print_r('</pre>');
	   		//die();
	   		*/



	   		// RENDER
    		foreach ($fields as $index => $field) {


    			$value 		= '';
    			$type 		= isset($field['type']) ? $field['type'] : 'input';
    			$name 		= isset($field['name']) ? $field['name'] : 'Field';
    			$need 		= isset($field['need']) ? $field['need'] : 0;
    			$key 		= isset($field['key']) ? $field['key'] : false;
    			$required 	= isset($field['required']) ? $field['required'] : false;

    			$placeholder 		= isset($field['placeholder']) ? $field['placeholder'] : '';
    			$placeholder_st 	= isset($field['placeholder_st']) ? $field['placeholder_st'] : '';
    			$placeholder_al 	= isset($field['placeholder_al']) ? $field['placeholder_al'] : '';
    			$placeholder_ar 	= isset($field['placeholder_ar']) ? $field['placeholder_ar'] : '';
    			$placeholder_cc 	= isset($field['placeholder_cc']) ? $field['placeholder_cc'] : '';
    			$placeholder_zip 	= isset($field['placeholder_zip']) ? $field['placeholder_zip'] : '';
				$placeholder_btn 	= isset($field['placeholder_btn']) ? $field['placeholder_btn'] : '';
				
				$datalists 			= apply_filters('job-postings/datalists', true);
				$datalists_key 		= apply_filters('job-postings/datalists/'.$key, true);
				$datalists_fields 	= array('position_title', 'position_industry');

				if( ($datalists == false && in_array($key, $datalists_fields)) || $datalists_key == false ){
					$placeholder = '';
				}

    			$options 	= isset($field['options']) ? $field['options'] : array();
    			$teeny 		= isset($field['teeny']) ? $field['teeny'] : false;
    			$description = isset($field['description']) ? $field['description'] : false;

    			$teeny 		= apply_filters('jobs-postings/tinymce_teeny', $teeny);

    			if( !$key ) continue;

				$values = get_post_custom( $post_id );
				
				switch($need){
					case 1:
						$need_text = _x('Required', 'jobs-field', 'job-postings');
						$need_class = 'jobs-field-required';
					break;

					case 2:
						$need_text = _x('Recommended', 'jobs-field', 'job-postings');
						$need_class = 'jobs-field-recommended';
					break;

					default:
						$need_text = '';
						$need_class = '';
					break;
				}

				if( $need_text ) $need_text = '<span class="jobs-field-need-by-g '.$need_class.'" title="by Google">'.$need_text.'</span>';


				$currency_symbol = get_option( 'jobs_currency_symbol'.'_'.Job_Postings::$lang );
				if(!$currency_symbol) $currency_symbol = '€';

			    $req 		= $required ? 'required' : '';
			    $req_star 	= $required ? ' *' : '';

			    $dir = plugin_dir_url(__FILE__).'../';

    			echo '<div class="jobs-row clearfix type-'.esc_attr($type).' job-field-'.esc_attr($key).' job-field-is-'.esc_attr($req).'" data-type="'.esc_attr($type).'" data-need="'.esc_attr($need).'">';
    				echo '<div class="jobs-row-label">';
    					echo '<img class="jobs-sort-icon" src="'.$dir.'/images/sort.svg" width="15" title="Grab and sort">';
		   		 		echo '<label for="'.esc_attr($key).'">'.$name.$need_text.'</label>';

						echo '<img class="jobs-remove-icon" src="'.$dir.'/images/bin.svg" width="15" title="Disable field">';

						if ( $type != 'empty_apply_now' ) {
							echo '<img class="jobs-gear-icon" src="'.$dir.'/images/gear.svg" width="15" title="'. __( 'Settings', 'job-postings' ) . '">';
						}


						echo '<img class="jobs-setright" data-append="'.esc_attr($append_class_2).'" src="'.$dir.'/images/set-right.svg" width="15" title="Add to right column">';
						echo '<img class="jobs-setleft" data-append="'.esc_attr($append_class_1).'" src="'.$dir.'/images/set-left.svg" width="15" title="Add to left column">';

					echo '</div>';

		   		 	echo self::renderFieldSettings( $post_id, $key );

		   		 	echo '<div class="jobs-row-input">';

		   		 	if( $description ){
		   		 		echo '<p class="jobs-field-description">'.$description.'</p>';
		   		 	}

				    echo '<input class="item-sort-value" type="hidden" name="sort-'.esc_attr($key).'" value="'.esc_attr($index).'"/>';
				
				
    			switch ( $type ) {

    				case 'empty_hiring_logo':
						# INPUT

							$global_company_logo 		= get_option('jobs_company_logo');
							$global_hiring_organization = get_option('jobs_hiring_organization'.'_'.Job_Postings::$lang);
							$global_hiring_organization = htmlspecialchars($global_hiring_organization);

							$horg_placeholder = '';
							if( $global_hiring_organization != '' ) $horg_placeholder = 'Global: '. $global_hiring_organization;

							$horg_logo_placeholder = '';
							if( $global_company_logo != '' ) $horg_logo_placeholder = $global_company_logo;

							$single_company_logo 		= isset( $values[$key] ) && $values[$key][0] != '' ? esc_attr( $values[$key][0] ) : '';
							
							$hiring_organization 		= isset( $values['position_hiring_organization_name'] ) && $values['position_hiring_organization_name'][0] != '' ? esc_attr( $values['position_hiring_organization_name'][0] ) : '';
							$hiring_organization 		= htmlspecialchars($hiring_organization);
							//if(!$hiring_organization) $hiring_organization = get_option('blogname');

							// if( $single_company_logo ){
							// 	$hiring_organization 	= get_option('jobs_hiring_organization'.'_'.Job_Postings::$lang);
							// 	if(!$hiring_organization) $hiring_organization = get_option('blogname');
							// 	$out = '<img class="jobs_hiring_logo" src="'.$single_company_logo.'" alt="'.$hiring_organization.'" title="'.$hiring_organization.'">';
							// }else{
							// 	$out = _x('Logo can be added/changed in <a target="_blank" href="edit.php?post_type=jobs&page=jp-help">Settings</a>.', 'job-settings', 'job-postings');
							// }

							if($global_hiring_organization) $out = _x('Global Hiring Organization is set and can be added/changed in <a target="_blank" href="edit.php?post_type=jobs&page=jp-help">Settings</a>.', 'job-settings', 'job-postings') . '<br>';
							
							if( $single_company_logo == '' && $horg_logo_placeholder != '' ) $single_company_logo = $horg_logo_placeholder;

							$hiring_org_img_class = 'jobs-no-image';
							if( $single_company_logo ){
								$hiring_org_img_class = '';
							}

							$out = '<img id="'.esc_attr($key).'_uploaded_image" class="jobs_hiring_logo '.$hiring_org_img_class.'" src="'.$single_company_logo.'" alt="'.$hiring_organization.'" title="'.$hiring_organization.'">';

							if( $single_company_logo == $horg_logo_placeholder ) $single_company_logo = '';

							$out .= '<input id="'.esc_attr($key).'_upload_file" type="text" name="'.esc_attr($key).'" value="'.$single_company_logo.'" placeholder="'.$horg_logo_placeholder.'">';
							$out .= '<input id="'.esc_attr($key).'_upload_file_button" class="button button-primary" type="button" value="'.__('Upload/Select file', 'job-postings').'" />';
							$custom_uploader = '<script type="text/javascript">
								jQuery(document).ready(function(){
									var '.esc_js($key).'_custom_uploader;
										jQuery("#'.esc_js($key).'_upload_file_button").click(function(e) {
											e.preventDefault();
											if ('.esc_js($key).'_custom_uploader) {
												'.esc_js($key).'_custom_uploader.open();
												return;
											}
											'.esc_js($key).'_custom_uploader = wp.media.frames.file_frame = wp.media({
												title: "Choose Image",
												button: {
													text: "Choose file"
												},
												multiple: false,
												frame: "post",
    											state: "insert"
											});
											'.esc_js($key).'_custom_uploader.on("insert", function(selection) {
												var state = '.esc_js($key).'_custom_uploader.state();
												selection = selection || state.get("selection");
												if (! selection) return;
												
												// We set multiple to false so only get one image from the uploader
												var attachment = selection.first();
												var display = state.display(attachment).toJSON();  // <-- additional properties
												attachment = attachment.toJSON();
												
												// Do something with attachment.id and/or attachment.url here
												var imgurl = attachment.sizes[display.size].url;

												jQuery("#'.esc_js($key).'_upload_file").val(imgurl);
												jQuery("#'.esc_js($key).'_uploaded_image").attr("src", imgurl).removeClass("jobs-Recommendedno-image");
											});
											'.esc_js($key).'_custom_uploader.open();
										});
								});
							</script>';
							echo $custom_uploader;
							
							$out .= '<div class="hiring_organization_name">';
							$out .= '<label>' . $name . ':</label>';
							$out .= '<input type="text" class="jp-input" name="position_hiring_organization_name" value="'.esc_attr($hiring_organization).'" placeholder="'.esc_attr($horg_placeholder).'">';
							$out .= '</div>';

				    		echo $out;
    					break;

    				case 'empty_date':
    					# INPUT
    						$job_date = get_the_date( get_option('date_format'), $post_id );
				    		echo $job_date;
    					break;

    				case 'empty_pdf_export':
    					# INPUT

    						$value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
							$value = htmlspecialchars($value);

							$pdf_ico = '<img class="pdf-icon" src="'.plugin_dir_url( __FILE__ ).'../images/pdf.svg" width="15">';

							echo $pdf_ico. '<input class="jp-input inline-input" list="datalist-'.esc_attr($key).'" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
    					break;

    				case 'empty_apply_now':
    					# INPUT

    						$value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : $name;
							$value = htmlspecialchars($value);

				    		echo '<input class="jp-input" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
				    		//echo '<p>'.esc_attr($placeholder).'</p>';
    					break;

    				case 'empty_inline_apply_now':
    					# INPUT

    						$value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : $name;
							$value = htmlspecialchars($value);

				    		//echo '<input class="jp-input" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
				    		//echo '<p>'.esc_attr($placeholder).'</p>';
    					break;

    				case 'custom_button':
    					# INPUT

    						$value = isset( $values[$key] ) ? strip_tags( $values[$key][0] ) : $name;
							$value = htmlspecialchars($value);
			    			$style 	= isset( $values[$key.'-style'] ) ? esc_attr( $values[$key.'-style'][0] ) : 'primary-style';

				    		echo '<input class="jp-input '.esc_attr($style).'" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
				    		//echo '<p>'.esc_attr($placeholder).'</p>';
    					break;

    				case 'valid_through':
    					# INPUT

    						$value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
							$value = htmlspecialchars($value);

				    		echo '<input class="jp-input js-datepicker" autocomplete="off" type="text" name="'.esc_attr($key).'" id="js-datepicker" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
				    		//echo '<p>'.esc_attr($placeholder).'</p>';
    					break;


    				case 'textarea':
    					# INPUT
    						$value = isset( $values[$key] ) ? $values[$key][0] : '';
							$value = htmlspecialchars($value);

				    		echo '<textarea class="jp-textarea" resize="none" rows="2" name="'.esc_attr($key).'" id="'.esc_attr($key).'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'>'.$value.'</textarea>';
    					break;


    				case 'location':
    					# INPUT

							// Help switch
							echo Job_Postings_Helper::get_onoff_switch( __('Need help?', 'job-postings'), $key . '_help', '', false, 'wrap_job_location_help', '', false, 'empty help' );

							echo '<div id="wrap_job_location_help">';
								echo '<p class="jfw_hint">'.__('<b>Physical work on site only:</b> Input address location, but leave "Job is remote" switch off.', 'job-postings').'</p>';

								echo '<p class="jfw_hint">'.__('<b>Physical work on site OR remote within the same country:</b> Input address location and enable "Job is remote" switch.', 'job-postings').'</p>';

								echo '<p class="jfw_hint">'.__('<b>Remote work only:</b> Leave inputs below empty and enable "Job is remote" switch.', 'job-postings').'</p>';

								echo '<p class="jfw_hint">'.__('<b>Remote work only and limited to State/Country:</b> Leave inputs below empty, enable "Job is remote" switch and add geographical restrictions for where the remote work can be performed.', 'job-postings').'</p>';

								echo '<p class="jfw_hint">'.__('<b>Physical work on site OR remote with restrictions:</b> Input address location, enable "Job is remote" switch and add geographical restrictions for where the remote work can be performed.', 'job-postings').'</p>';
							echo '</div>';

							//print_r( $values );
							
							echo '<div id="wrap_job_location" class="wrap_job_location">';

								echo JobAddEdit::getDatalist( $key );
								echo JobAddEdit::getDatalist( $key . '_streetAddress' );
								echo JobAddEdit::getDatalist( $key . '_postalCode' );
								echo JobAddEdit::getDatalist( $key . '_addressLocality' );
								echo JobAddEdit::getDatalist( $key . '_addressRegion' );

								$streetAddress = isset( $values[$key.'_streetAddress'] ) ? esc_attr( $values[$key.'_streetAddress'][0] ) : '';
								$streetAddress = htmlspecialchars($streetAddress);
								echo '<input class="jp-input" list="datalist-'.esc_attr($key).'_streetAddress" autocomplete="off" type="text" name="'.esc_attr($key).'_streetAddress" id="'.esc_attr($key).'_streetAddress" value="'.$streetAddress.'" placeholder="'.esc_attr($placeholder_st).'" />';


								$postalCode = isset( $values[$key.'_postalCode'] ) ? esc_attr( $values[$key.'_postalCode'][0] ) : '';
								$postalCode = htmlspecialchars($postalCode);
								echo '<input class="jp-input" list="datalist-'.esc_attr($key).'_postalCode" autocomplete="off" type="text" name="'.esc_attr($key).'_postalCode" id="'.esc_attr($key).'_postalCode" value="'.esc_attr($postalCode).'" placeholder="'.$placeholder_zip.'" />';


								$city = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
								$city = htmlspecialchars($city);
								echo '<input class="jp-input" list="datalist-'.esc_attr($key).'" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$city.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';


								// $addressLocality = isset( $values[$key.'_addressLocality'] ) ? esc_attr( $values[$key.'_addressLocality'][0] ) : '';
								// echo '<input class="jp-input" list="datalist-'.esc_attr($key).'_addressLocality" autocomplete="off" type="text" name="'.esc_attr($key).'_addressLocality" id="'.esc_attr($key).'_addressLocality" value="'.$addressLocality.'" placeholder="'.$placeholder_al.'" />';


								$addressRegion = isset( $values[$key.'_addressRegion'] ) ? esc_attr( $values[$key.'_addressRegion'][0] ) : '';
								$addressRegion = htmlspecialchars($addressRegion);
								echo '<input class="jp-input" list="datalist-'.esc_attr($key).'_addressRegion" autocomplete="off" type="text" name="'.esc_attr($key).'_addressRegion" id="'.esc_attr($key).'_addressRegion" value="'.$addressRegion.'" placeholder="'.$placeholder_ar.'" />';

								$addressCountry = isset( $values[$key.'_addressCountry'] ) ? esc_attr( $values[$key.'_addressCountry'][0] ) : '';
								$addressCountry = htmlspecialchars($addressCountry);
								echo '<input class="jp-input" list="datalist-'.esc_attr($key).'_addressCountry" autocomplete="off" type="text" name="'.esc_attr($key).'_addressCountry" id="'.esc_attr($key).'_addressCountry" value="'.$addressCountry.'" placeholder="'.$placeholder_cc.'" />';

							echo '</div>';


							// Remote job switch
							echo Job_Postings_Helper::get_onoff_switch( __('Job is remote', 'job-postings'), $key . '_remote', '', false, 'wrap_job_location_remote' );

							$job_remote_data = get_post_meta($post_id, 'job_remote_data', true);


							// Remote job fields
							echo '<div id="wrap_job_location_remote" class="wrap_job_location_remote">';
								echo '<h4>'.__('Remote job restrictions', 'job-postings').'</h4>';

								echo '<p class="jfw_hint">'.__('You can add geographical restrictions for where the remote work can be performed. (Optional)', 'job-postings').'</p>';

								echo '<div class="jfw_repeater job_remote_repeater">';
									echo '<div class="jfw_sortable" data-repeater-list="job_remote_data">';

									if( !empty($job_remote_data) && is_array($job_remote_data) ){
									foreach ($job_remote_data as $key => $value) {
										echo '<div class="jfw_repeater_row clearfix" data-repeater-item>';

											$val = isset($job_remote_data[$key]['type']) ? $job_remote_data[$key]['type'] : '';
											$val = htmlspecialchars($val);
											echo '<label for="type-field-'.esc_attr($key).'">'.__('Type', 'job-postings').'</label>';
											echo '<select id="type-field-'.esc_attr($key).'" name="type" class="job_remote_data_type" data-hint-country="'.__('Example: USA').'" data-hint-state="'.__('Example: Texas, USA').'">';
												echo '<option value="">-</option>';
												echo '<option value="Country" '.selected($val, 'Country', false).'>Country</option>';
												echo '<option value="State" '.selected($val, 'State', false).'>State</option>';
											echo '</select>';

											$val2 = isset($job_remote_data[$key]['name']) ? $job_remote_data[$key]['name'] : '';
											$val2 = htmlspecialchars($val2);
											echo '<label for="name-field-'.esc_attr($key).'">'.__('Location', 'job-postings').'<span class="example"></span></label>';
											echo '<input id="name-field-'.esc_attr($key).'" class="job-input-field job_remote_data_name" name="name" type="text" value="'.$val2.'">';

											echo '<input data-repeater-delete type="button" class="button button-delete" value="Delete"/>';
										echo '</div>';
									}
									}else{
										echo '<div class="jfw_repeater_row clearfix" data-repeater-item>';
											echo '<label for="type-field-">'.__('Type', 'job-postings').'</label>';
											echo '<select id="type-field-" name="type" class="job_remote_data_type" data-hint-country="'.__('Example: USA').'" data-hint-state="'.__('Example: Texas, USA').'">';
												echo '<option value="">-</option>';
												echo '<option value="Country">Country</option>';
												echo '<option value="State">State</option>';
											echo '</select>';

											echo '<label for="name-field-">'.__('Location', 'job-postings').'<span class="example"></span></label>';
											echo '<input id="name-field-" class="job-input-field job_remote_data_name" name="name" type="text" value="">';

											echo '<input data-repeater-delete type="button" class="button button-delete" value="Delete"/>';
										echo '</div>';
									}

									echo '</div>';
									echo '<input data-repeater-create type="button" class="button button-primary hg_addRemove" value="+"/>';
								echo '</div>';
							echo '</div>';

    					break;



    				case 'tinymce':
    					# INPUT
    						$value = isset( $values[$key] ) ? $values[$key][0] : '';
							//$value = htmlspecialchars($value);

				    		wp_editor( $value, $key, array(
    							'textarea_rows' => 5,
    							'teeny' => $teeny
    							) );
    					break;



    				case 'file':
    					# INPUT
    						$num = 1;

					    	$html = '<div class="file-upload-list" data-num="1">';
    							$html .= '<div class="file-upload-item">';
		    						$value 	= isset( $values[$key] ) ? $values[$key][0]: '';
		    						$btnname 	= isset( $values[$key.'_name'] ) ? $values[$key.'_name'][0]: '';



									$html .= '<input id="'.esc_attr($key).'_upload_file" class="jp-input" type="text" size="36" name="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" />';
									$html .= '<input id="'.esc_attr($key).'_upload_button_name" class="jp-input" type="text" size="36" name="'.esc_attr($key).'_name" value="'.esc_attr($btnname).'" placeholder="'.esc_attr($placeholder_btn).'" />';


									$html .= '<input id="'.esc_attr($key).'_upload_file_button" class="button" type="button" value="'.__('Upload/Select file', 'job-postings').'" />';


								$html .= '</div>';

							$html .= '</div>';

							$html .= '<script type="text/javascript">
								jQuery(document).ready(function(){
									var '.esc_attr($key).'_custom_uploader;
										jQuery("#'.esc_attr($key).'_upload_file_button").click(function(e) {
											e.preventDefault();
											console.log("click");
											if ('.esc_attr($key).'_custom_uploader) {
												'.esc_attr($key).'_custom_uploader.open();
												return;
											}
											'.esc_attr($key).'_custom_uploader = wp.media.frames.file_frame = wp.media({
												title: "Choose Image",
												button: {
													text: "Choose file"
												},
												multiple: false
											});
											'.esc_attr($key).'_custom_uploader.on("select", function() {
												attachment = '.esc_attr($key).'_custom_uploader.state().get("selection").first().toJSON();
												jQuery("#'.esc_attr($key).'_upload_file").val(attachment.url);
												jQuery("#'.esc_attr($key).'_upload_button_name").val(attachment.title);
											});
											'.esc_attr($key).'_custom_uploader.open();

										});
								});
							</script>';

							echo $html;
    					break;

					case 'checkboxes':

						$value = get_post_meta( $post_id, $key, true );
						$style 	= isset( $values[$key.'-style'] ) ? esc_attr( $values[$key.'-style'][0] ) : 'primary-style';

						if( !empty($options) ){
							echo '<div class="options_group">';
							foreach ($options as $option_key => $option_name) {
								$checked = '';
								$option_key = htmlspecialchars($option_key);
								$option_name = htmlspecialchars($option_name);
								if( is_array($value) && in_array($option_key, $value) ) $checked = 'checked';
								echo '<label for="checkbox-'.esc_attr($option_key).'">';
									echo '<input '.$checked.' class="jp-checkbox '.$style.'" type="checkbox" name="'.esc_attr($key).'[]" id="checkbox-'.esc_attr($option_key).'" value="'.esc_attr($option_key).'" '.esc_attr($req).'/>';
									echo $option_name;
								echo '</label>';
							}

							if( is_array($value) ){
								$other_input_value = isset($value['other_input']) ? $value['other_input']:"";
								$other_input_value = htmlspecialchars($other_input_value);
							}else{
								$value = htmlspecialchars($value);
								$other_input_value = $value;
							}
							
							echo '<label class="other_input" for="checkbox-other_input">';
									echo __('Other', 'job-postings');
									echo '<input class="jp-checkbox '.$style.'" type="text" name="'.esc_attr($key).'[other_input]" id="checkbox-other_input" value="'.$other_input_value.'"/>';
								echo '</label>';
							echo '</div>';
						}


						
						break;

					
					default:
    					# INPUT
							$unitText 			= isset($field['unitText']) ? $field['unitText'] : array();
    						$value 				= isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
							$value 				= htmlspecialchars($value);
    						$upto 				= isset( $values[$key.'_upto'] ) ? esc_attr( $values[$key.'_upto'][0] ) : '';
							$upto 				= htmlspecialchars($upto);
    						$unittext_value 	= isset( $values[$key.'_unittext'] ) ? esc_attr( $values[$key.'_unittext'][0] ) : '';
							$unittext_value 	= htmlspecialchars($unittext_value);

							$currency_before = '';
							$currency_after = '';

							$input_class = '';
							if( $key == 'position_base_salary' && $currency_symbol ){
								$currency_position = get_option( 'jobs_currency_position'.'_'.Job_Postings::$lang );
								if(!$currency_position) $currency_position = 'before';

								switch ($currency_position) {
									case 'after':
										$symbol = '';
										$symbol .= '<span class="jobs-currency right-align">';
											$symbol .= $currency_symbol;
										$symbol .= '</span>';
										$currency_after = $symbol;
										$input_class = 'currency-in-right';
										break;

									default:
										$symbol = '';
										$symbol .= '<span class="jobs-currency left-align">';
											$symbol .= $currency_symbol;
										$symbol .= '</span>';
										$currency_before = $symbol;
										$input_class = 'currency-in-left';
										break;
								}

								echo '<div class="job-input-field-wrap">';
									// list="datalist-'.esc_attr($key).'"
									echo '<label>' . _x('Starting', 'jobs-field', 'job-postings') . ':</label>';
									echo $currency_before;
						    		echo '<input class="jp-input '.esc_attr($input_class).'" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
									echo $currency_after;
								echo '</div>';


								echo '<div class="job-input-field-wrap">';
									echo '<label>' . _x('Up to', 'jobs-field', 'job-postings') . ':</label>';
									echo $currency_before;
						    		echo '<input class="jp-input '.esc_attr($input_class).'" autocomplete="off" type="text" name="'.esc_attr($key).'_upto" id="'.esc_attr($key).'_upto" value="'.esc_attr($upto).'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
									echo $currency_after;
								echo '</div>';

								if( !empty($unitText) ){
									echo '<div class="job-input-field-wrap">';
										echo '<label for="'.esc_attr($key).'_unittext">' . _x('Unit', 'jobs-field', 'job-postings') . ':</label><br>';
										echo '<select name="'.esc_attr($key).'_unittext" id="'.esc_attr($key).'_unittext" class="jp-select '.esc_attr($input_class).'" style="width: 100%;">';
										echo '<option value="">'.__('None', 'job-postings').'</option>';
										foreach ($unitText as $unit_key => $unit_name) {
											echo '<option value="'.$unit_key.'" '.selected($unit_key, $unittext_value, false).'>'.$unit_name.'</option>';
										}
										echo '</select>';
									echo '</div>';
								}

					    		//echo  $this->getDatalist( $key );


							}else{
								$value = htmlspecialchars($value);
								echo $currency_before;
					    		echo '<input class="jp-input '.esc_attr($input_class).'" list="datalist-'.esc_attr($key).'" autocomplete="off" type="text" name="'.esc_attr($key).'" id="'.esc_attr($key).'" value="'.$value.'" placeholder="'.esc_attr($placeholder).'" '.esc_attr($req).'/>';
								echo $currency_after;

					    		echo  JobAddEdit::getDatalist( $key );
							}

    					break;
    			}

				    echo '</div>';

			    echo '</div>';


    		}

    	}


    }

    public static function renderFieldSettings( $post_id, $key ){
    	$out = '';

		$out .= '<div class="jobs-row-settings">';

    		/* CUSTOM TITLE */
    		$out .= '<div class="field-settings-row">';

    			$name = $key.'-custom-title';
    			$value = get_post_meta($post_id, $name, true);
				$value = htmlspecialchars($value);

    			$out .= '<label for="'.esc_attr($name).'">';
    				$out .= _x('Custom title', 'jobs-field', 'job-postings');
    			$out .= '</label>';
    			$out .= '<input id="'.esc_attr($name).'" type="text" name="'.esc_attr($name).'" value="'.$value.'">';
    		$out .= '</div>';
    		/**/

    		/* HIDE TITLE */
    		$out .= '<div class="field-settings-row">';

    			$name = $key.'-hide-title';
    			$value = get_post_meta($post_id, $name, true);
				$value = htmlspecialchars($value);

    			$out .= '<label for="'.esc_attr($name).'">';
    				$out .= _x('Hide title', 'jobs-field', 'job-postings');
    			$out .= '</label>';
				//$out .= '<input id="'.esc_attr($name).'" type="checkbox" name="'.esc_attr($name).'" value="on" '.checked($value, 'on', false).'>';
				
				$out .= Job_Postings_Helper::get_onoff_switch( '', $name, '', false, '' );
    		$out .= '</div>';
    		/**/

    		/* HIDE FIELD */
				$out .= '<div class="field-settings-row">';

				$name = $key.'-hide-field';
				$value = get_post_meta($post_id, $name, true);
				$value = htmlspecialchars($value);

				$out .= '<label for="'.esc_attr($name).'">';
					$out .= _x('Hide field', 'jobs-field', 'job-postings');
				$out .= '</label>';
				//$out .= '<input id="'.esc_attr($name).'" type="checkbox" name="'.esc_attr($name).'" value="on" '.checked($value, 'on', false).'>';
				
				$out .= Job_Postings_Helper::get_onoff_switch( '', $name, '', false, '' );

				$out .= '<p class="jobs-field-description clearfix">' . __( 'Only hides it from job offer on your site, but Google still sees and validates it. If you want to completely remove it, use "trash" icon in the widgets top right corner.', 'job-postings' ) . '</p>';
			$out .= '</div>';
			/**/


			$out .= '<div class="field-settings-row">';

				$name = $key.'-field-tag-title';
				$value = get_post_meta($post_id, $name, true);
				$value = htmlspecialchars($value);

				$out .= '<label for="'.esc_attr($name).'">';
					$out .= _x('Heading HTML Tag', 'jobs-field', 'job-postings');
				$out .= '</label>';
				$out .= '<select id="'.esc_attr($name).'" name="'.esc_attr($name).'">';
					$out .= '<option value="div" ' . selected( $value, 'div', false ) . '>DIV</option>';
					$out .= '<option value="h1" ' . selected( $value, 'h1', false ) . '>H1</option>';
					$out .= '<option value="h2" ' . selected( $value, 'h2', false ) . '>H2</option>';
					$out .= '<option value="h3" ' . selected( $value, 'h3', false ) . '>H3</option>';
					$out .= '<option value="h4" ' . selected( $value, 'h4', false ) . '>H4</option>';
					$out .= '<option value="h5" ' . selected( $value, 'h5', false ) . '>H5</option>';
					$out .= '<option value="h6" ' . selected( $value, 'h6', false ) . '>H6</option>';
					$out .= '<option value="span" ' . selected( $value, 'span', false ) . '>SPAN</option>';
				$out .= '</select>';
			$out .= '</div>';

			$out .= '<div class="field-settings-row">';

				$name = $key.'-field-tag';
				$value = get_post_meta($post_id, $name, true);
				$value = htmlspecialchars($value);

				$out .= '<label for="'.esc_attr($name).'">';
					$out .= _x('Content HTML Tag', 'jobs-field', 'job-postings');
				$out .= '</label>';
				$out .= '<select id="'.esc_attr($name).'" name="'.esc_attr($name).'">';
					$out .= '<option value="div" ' . selected( $value, 'div', false ) . '>DIV</option>';
					$out .= '<option value="h1" ' . selected( $value, 'h1', false ) . '>H1</option>';
					$out .= '<option value="h2" ' . selected( $value, 'h2', false ) . '>H2</option>';
					$out .= '<option value="h3" ' . selected( $value, 'h3', false ) . '>H3</option>';
					$out .= '<option value="h4" ' . selected( $value, 'h4', false ) . '>H4</option>';
					$out .= '<option value="h5" ' . selected( $value, 'h5', false ) . '>H5</option>';
					$out .= '<option value="h6" ' . selected( $value, 'h6', false ) . '>H6</option>';
					$out .= '<option value="span" ' . selected( $value, 'span', false ) . '>SPAN</option>';
				$out .= '</select>';
			$out .= '</div>';

			$out .= '<div class="field-settings-row">';
				//
				$name = $key.'-field-class';
				$value = get_post_meta($post_id, $name, true);
				$value = htmlspecialchars($value);

				$out .= '<label for="'.esc_attr($name).'">';
					$out .= _x('Custom CSS class', 'jobs-field', 'job-postings');
				$out .= '</label>';
				$out .= '<input type="text" id="'.esc_attr($name).'" name="'.esc_attr($name).'" value="'.$value.'">';
			$out .= '</div>';

    		switch ($key) {
    			case 'position_button':
    				$out .= '<div class="field-settings-row">';

		    			$name = $key.'-url';
		    			$value = get_post_meta($post_id, $name, true);
						$value = htmlspecialchars($value);

		    			$out .= '<label for="'.esc_attr($name).'">';
		    				$out .= _x('URL', 'jobs-field', 'job-postings');
		    			$out .= '</label>';
    					$out .= '<input id="'.esc_attr($name).'" type="text" name="'.esc_attr($name).'" value="'.$value.'" placeholder="https://">';
		    		$out .= '</div>';

    				$out .= '<div class="field-settings-row">';

		    			$name = $key.'-url-target';
		    			$value = get_post_meta($post_id, $name, true);
						$value = htmlspecialchars($value);

		    			$out .= '<label for="'.esc_attr($name).'">';
		    				$out .= _x('Link target', 'jobs-field', 'job-postings');
		    			$out .= '</label>';
		    			$out .= '<select id="'.esc_attr($name).'" name="'.esc_attr($name).'">';
		    				$out .= '<option value="_blank" '.selected($value, '_blank', false).'>'.__('New tab/window', 'jobs-field', 'job-postings').'</option>';
		    				$out .= '<option value="_self" '.selected($value, '_self', false).'>'.__('Same tab/window', 'jobs-field', 'job-postings').'</option>';
		    			$out .= '</select>';
		    		$out .= '</div>';

    				$out .= '<div class="field-settings-row">';

		    			$name = $key.'-style';
		    			$value = get_post_meta($post_id, $name, true);
						$value = htmlspecialchars($value);

		    			$out .= '<label for="'.esc_attr($name).'">';
		    				$out .= _x('Style', 'jobs-field', 'job-postings');
		    			$out .= '</label>';
		    			$out .= '<select id="'.esc_attr($name).'" name="'.esc_attr($name).'">';
		    				$out .= '<option value="primary-style" '.selected($value, 'primary-style', false).'>'.__('Primary', 'jobs-field', 'job-postings').'</option>';
		    				$out .= '<option value="secondary-style" '.selected($value, 'secondary-style', false).'>'.__('Secondary', 'jobs-field', 'job-postings').'</option>';
		    			$out .= '</select>';
		    		$out .= '</div>';
					break;
					
				case 'position_title':
					break;

    			default:
					# code...
					
					
    				break;
			}
			


			$out .= '<div class="jobs-for-dev-header">'._x('For Developers', 'jobs-field', 'job-postings').'</div>';
			$out .= '<div class="field-for-developers-rows">';
				/* CUSTOM TITLE */
				$out .= '<div class="field-settings-row field-settings-inforow">';

					$out .= '<label>';
						$out .= _x('Field Key', 'jobs-field', 'job-postings');
					$out .= '</label>';
					$out .= '<span>';
						$out .= $key;
					$out .= '</span>';
				$out .= '</div>';
				/**/
			$out .= '</div>';


    	$out .= '</div>';

    	return $out;
    }


    public static function save( $post_id ){
		global $BlueGlassAnalytics;

		//$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Bail if we're doing an auto save
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // now we can actually save the data
        $allowed = array(
            'a' => array( // on allow a tags
                'href' => array() // and those anchors can only have href attribute
            ),
            'p'=> array(
                'style' => array()
			),
			'h1'=> array(), 
			'h2'=> array(), 
			'h3'=> array(), 
			'h4'=> array(), 
			'h5'=> array(), 
			'h6'=> array(), 
            'br'=> array(),
            'b'=> array(),
            'strong'=> array(),
            'i'=> array(),
            'em'=> array(),
            'u'=> array(),
            'ul'=> array(),
            'ol'=> array(),
            'li'=> array(),
            'span'=> array(
                'style' => array()
            ),
            'sub'=> array(),
			'sup'=> array(),
			'pre'=> array(),
            'script'=> array(),
            'img' => array(
                    'src' => array(),
                    'width' => array(),
                    'height' => array(),
                    'class' => array(),
                )
        );

        // if our nonce isn't there, or we can't verify it, bail
        if( !isset( $_POST['jp-meta_box_nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_POST['jp-meta_box_nonce'] ), 'job_postings_meta_box_nonce' ) ) return;

        // if our current user can't edit this post, bail
		if( !current_user_can( 'edit_posts' ) ) return;
		

		$_POST = apply_filters('job-postings/modify_request', $_POST);
		

        if( isset( $_POST['job_custom_message'] ) ){
            $notification_message = $_POST['job_custom_message'];
            update_post_meta( $post_id, 'job_custom_message', $notification_message );
        }

        if( isset( $_POST['job_notification_message'] ) ){
            $notification_message = $_POST['job_notification_message'];
            update_post_meta( $post_id, 'job_notification_message', $notification_message );
		}
		
		/* Custom notificaton fields */

		// On/Off switch
		$value = isset( $_POST['job_notify_custom_message'] ) ? strip_tags( $_POST['job_notify_custom_message'] ) : '';
		update_post_meta( $post_id, 'job_notify_custom_message', $value );
	
		// Editor text
		$notify_custom_message = isset( $_POST['job_notify_custom_message_editor'] ) ? $_POST['job_notify_custom_message_editor'] : '';
		update_post_meta( $post_id, 'job_notify_custom_message_editor', $notify_custom_message );

		/* - */

        if( isset( $_POST['job_confirmation_email'] ) ){
            $confirmation_email = strip_tags( $_POST['job_confirmation_email'] );

            if (strpos($confirmation_email, ',') !== false) {
                $confirmation_emails = explode(',', $confirmation_email);
                if(!empty($confirmation_emails)){
                    $sanitized_emails = array();
                    foreach ($confirmation_emails as $key => $email) {
                        $sanitized_emails[] = sanitize_email( $email );
                    }
                    $confirmation_email = implode(',',$sanitized_emails);
                }
            }else{
                $confirmation_email = sanitize_email( $_POST['job_confirmation_email'] );
            }

            update_post_meta( $post_id, 'job_confirmation_email', $confirmation_email );
        }



        $fields = Job_Postings::$fields;
        if( $fields ){
			

            foreach ($fields as $index => $field) {

                $type 	= isset($field['type']) ? $field['type'] : 'input';
                $key 	= isset($field['key']) ? $field['key'] : false;

                // continue if key is missing
				if( !$key ) continue;

				// echo "<pre>";
				// print_r( $key );
				//echo $type." - ".$key."<br>";

				switch ($type) {
					case 'checkboxes':
						if( isset( $_POST[$key] ) ){
							if( isset($_POST[$key]['other_input']) && !in_array('OTHER', $_POST[$key]) ) unset($_POST[$key]['other_input']);

							if( !empty($_POST[$key]) ){
								update_post_meta( $post_id, $key, $_POST[$key] );
							}else{
								delete_post_meta( $post_id, $key );
							}
						}
						break;
					
					default:
						if( isset( $_POST[$key] ) ){
							$field_key = $_POST[$key];
							update_post_meta( $post_id, $key, $field_key );
						}
						break;
				}

                

                if( isset( $_POST['sort-'.$key] ) ){
                    $field_sort_key = sanitize_title( $_POST['sort-'.$key] );
                    update_post_meta( $post_id, 'sort-'.$key, sanitize_text_field($field_sort_key) );
                }

                if( isset( $_POST[$key . '-custom-title'] ) ){
                    $field_key_title = sanitize_text_field( $_POST[$key . '-custom-title'] );
                    update_post_meta( $post_id, $key . '-custom-title', sanitize_text_field($field_key_title) );
                }


                $hide_title = isset($_POST[$key . '-hide-title']) ? 'on':'off';
                update_post_meta( $post_id, $key . '-hide-title', sanitize_text_field($hide_title) );

                $hide_title = isset($_POST[$key . '-hide-field']) ? 'on':'off';
                update_post_meta( $post_id, $key . '-hide-field', sanitize_text_field($hide_title) );

                $field_tag = isset($_POST[$key . '-field-tag']) ? sanitize_text_field( $_POST[$key . '-field-tag'] ):'div';
				update_post_meta( $post_id, $key . '-field-tag', sanitize_text_field($field_tag) );
				
                $field_tag_title = isset($_POST[$key . '-field-tag-title']) ? sanitize_text_field( $_POST[$key . '-field-tag-title'] ):'div';
                update_post_meta( $post_id, $key . '-field-tag-title', sanitize_text_field($field_tag_title) );

                $field_class = isset($_POST[$key . '-field-class']) ? sanitize_text_field( $_POST[$key . '-field-class'] ):'';
                update_post_meta( $post_id, $key . '-field-class', sanitize_text_field($field_class) );

                if( isset( $_POST[$key . '-url'] ) ){
                    $field_key_title = esc_url( $_POST[$key . '-url'] );
                    update_post_meta( $post_id, $key . '-url', $field_key_title );
                }

                if( isset( $_POST[$key . '-url-target'] ) ){
                    $field_key_title = sanitize_text_field( $_POST[$key . '-url-target'] );
                    update_post_meta( $post_id, $key . '-url-target', sanitize_text_field($field_key_title) );
                }


                if( isset( $_POST[$key . '-style'] ) ){
                    $field_key_title = sanitize_text_field( $_POST[$key . '-style'] );
                    update_post_meta( $post_id, $key . '-style', sanitize_text_field($field_key_title) );
                }

            }
        }

		/**
		 * Sanitization of all text fields in Job Position Main Form
		 */
		if( isset( $_POST['position_title'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_title'] );
            update_post_meta( $post_id, 'position_title', $field_key_title );
		}

		if( isset( $_POST['position_description'] ) ){
			$sanitized_content = wp_kses( $_POST['position_description'], $allowed );
			update_post_meta( $post_id, 'position_description', $sanitized_content );
        }

		if( isset( $_POST['position_responsibilities'] ) ){
			$sanitized_content = wp_kses( $_POST['position_responsibilities'], $allowed );
			update_post_meta( $post_id, 'position_responsibilities', $sanitized_content );
        }

		if( isset( $_POST['position_qualifications'] ) ){
			$sanitized_content = wp_kses( $_POST['position_qualifications'], $allowed );
			update_post_meta( $post_id, 'position_qualifications', $sanitized_content );
        }

		if( isset( $_POST['position_job_benefits'] ) ){
			$sanitized_content = wp_kses( $_POST['position_job_benefits'], $allowed );
			update_post_meta( $post_id, 'position_job_benefits', $sanitized_content );
        }

		if( isset( $_POST['position_contacts'] ) ){
			$sanitized_content = wp_kses( $_POST['position_contacts'], $allowed );
			update_post_meta( $post_id, 'position_contacts', $sanitized_content );
        }

		if( isset( $_POST['position_logo'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_logo'] );
            update_post_meta( $post_id, 'position_logo', $field_key_title );
		}

		if( isset( $_POST['position_hiring_organization_name'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_hiring_organization_name'] );
            update_post_meta( $post_id, 'position_hiring_organization_name', $field_key_title );
        }

		
        if( isset( $_POST['position_job_location_streetAddress'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_job_location_streetAddress'] );
            update_post_meta( $post_id, 'position_job_location_streetAddress', sanitize_text_field($field_key_title) );
        }

        if( isset( $_POST['position_job_location_postalCode'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_job_location_postalCode'] );
            update_post_meta( $post_id, 'position_job_location_postalCode', $field_key_title );
		}

		// Save city as locality
		if( isset( $_POST['position_job_location'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_job_location'] );
			update_post_meta( $post_id, 'position_job_location', $field_key_title );
            update_post_meta( $post_id, 'position_job_location_addressLocality', $field_key_title );
        }

        // if( isset( $_POST['position_job_location_addressLocality'] ) ){
        //     $field_key_title = sanitize_text_field( $_POST['position_job_location_addressLocality'] );
        //     update_post_meta( $post_id, 'position_job_location_addressLocality', sanitize_text_field($field_key_title) );
        // }

        if( isset( $_POST['position_job_location_addressRegion'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_job_location_addressRegion'] );
            update_post_meta( $post_id, 'position_job_location_addressRegion', $field_key_title );
        }

        if( isset( $_POST['position_job_location_addressCountry'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_job_location_addressCountry'] );
            update_post_meta( $post_id, 'position_job_location_addressCountry', $field_key_title );
        }

		// Remote Job On/Off switch
		$value = isset( $_POST['position_job_location_remote'] ) ? strip_tags( sanitize_text_field($_POST['position_job_location_remote']) ) : '';
		update_post_meta( $post_id, 'position_job_location_remote', $value );

		//type
		$job_remote_data = [];
		if( isset($_POST['job_remote_data']) ){
			if( count($_POST['job_remote_data']) > 0 ){
				foreach( $_POST['job_remote_data'] as $single_remote_date ){
					$job_remote_type = $single_remote_date['type'];
					$job_remoate_location = sanitize_text_field($single_remote_date['name']);
					$job_remote_data[] = [
						'type' => $job_remote_type,
						'name' => $job_remoate_location
					];
				}

				update_post_meta( $post_id, 'job_remote_data', $job_remote_data );
			}
		}
		
		if( isset( $_POST['position_employment_begining'] ) ){
            $field_key_title = sanitize_textarea_field( $_POST['position_employment_begining'] );
            update_post_meta( $post_id, 'position_employment_begining', $field_key_title );
        }

		$position_employment_type = $_POST['position_employment_type'];
		if( isset( $position_employment_type ) ){
			if( count( $position_employment_type ) > 0 ){
				foreach( $position_employment_type as $key => $single_emp_type ){
					if( $key == "other_input" ){
						$position_employment_type[$key] = sanitize_text_field( $single_emp_type );
						break;
					}
				}

				update_post_meta( $post_id, 'position_employment_type', $position_employment_type );
			}
		}

		if( isset( $_POST['position_pdf_job'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_pdf_job'] );
            update_post_meta( $post_id, 'position_pdf_job', $field_key_title );
		}

		if( isset( $_POST['position_pdf_job_name'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_pdf_job_name'] );
            update_post_meta( $post_id, 'position_pdf_job_name', $field_key_title );
		}

		if( isset( $_POST['position_work_hours'] ) ){
            $field_key_title = sanitize_textarea_field( $_POST['position_work_hours'] );
            update_post_meta( $post_id, 'position_work_hours', $field_key_title );
		}

		if( isset( $_POST['position_industry'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_industry'] );
            update_post_meta( $post_id, 'position_industry', $field_key_title );
		}

		if( isset( $_POST['position_employment_duration'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_employment_duration'] );
            update_post_meta( $post_id, 'position_employment_duration', $field_key_title );
		}

		if( isset( $_POST['position_pdf_export'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_pdf_export'] );
            update_post_meta( $post_id, 'position_pdf_export', $field_key_title );
		}

		if( isset( $_POST['position_base_salary'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_base_salary'] );
            update_post_meta( $post_id, 'position_base_salary', $field_key_title );
		}

		if( isset( $_POST['position_base_salary_upto'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_base_salary_upto'] );
            update_post_meta( $post_id, 'position_base_salary_upto', $field_key_title );
		}

		if( isset( $_POST['position_base_salary_unittext'] ) ){
            $field_key_title = sanitize_text_field( $_POST['position_base_salary_unittext'] );
            update_post_meta( $post_id, 'position_base_salary_unittext', $field_key_title );
        }
		
		if( isset( $_POST['position_valid_through'] ) ){
			$valid_through = sanitize_text_field( $_POST['position_valid_through'] );
			update_post_meta( $post_id, 'position_valid_through', $valid_through );
			$valid_through = date('Y-m-d', strtotime($valid_through));
            update_post_meta( $post_id, 'position_valid_through_date', $valid_through );
        }
		
		if( isset( $_POST['position_skills'] ) ){
			$sanitized_content = wp_kses( $_POST['position_skills'], $allowed );
			update_post_meta( $post_id, 'position_skills', $sanitized_content );
        }

		if( isset( $_POST['position_educationRequirements'] ) ){
			$sanitized_content = wp_kses( $_POST['position_educationRequirements'], $allowed );
			update_post_meta( $post_id, 'position_educationRequirements', $sanitized_content );
        }

		if( isset( $_POST['position_experienceRequirements'] ) ){
			$sanitized_content = wp_kses( $_POST['position_experienceRequirements'], $allowed );
			update_post_meta( $post_id, 'position_experienceRequirements', $sanitized_content );
        }

		if( isset( $_POST['position_custom_text_1'] ) ){
			$sanitized_content = wp_kses( $_POST['position_custom_text_1'], $allowed );
			update_post_meta( $post_id, 'position_custom_text_1', $sanitized_content );
        }

		if( isset( $_POST['position_custom_text_2'] ) ){
			$sanitized_content = wp_kses( $_POST['position_custom_text_2'], $allowed );
			update_post_meta( $post_id, 'position_custom_text_2', $sanitized_content );
        }

		if( isset( $_POST['position_custom_text_3'] ) ){
			$sanitized_content = wp_kses( $_POST['position_custom_text_3'], $allowed );
			update_post_meta( $post_id, 'position_custom_text_3', $sanitized_content );
        }
		/**
		 *  End - Sanitization of all text fields in Job Position Main Form
		 */
		

        // Update metrics data
        $metrics_counted = get_post_meta( $post_id, 'jobs_post_metrics_counted', true );
        $post_status = get_post_status( $post_id );

        if( $post_status == 'publish' && !$metrics_counted ){
            $active_jobs = get_option('jobs_metrics_active_postings');
            $active_jobs = $active_jobs + 1;

            update_option('jobs_metrics_active_postings', $active_jobs);
            update_post_meta( $post_id, 'jobs_post_metrics_counted', 'yes' );

            $BlueGlassAnalytics->track_metrics();
		}
		
		do_action('job-postings/save', $_POST, $post_id);
		
        $post_type  = get_post_type( $post_id );
        $updating 	= false;
        $post_parent = '';

        $archive_page = get_option('jobs_archive_page'.'_'.Job_Postings::$lang);

        switch ($post_type) {
            case 'jobs':
                $updating = true;
                $post_parent = $archive_page;
                break;

            default:
                $updating = false;
                break;
        }



        if( $updating ){
            // unhook this function so it doesn't loop infinitely
            remove_action( 'save_post', array('JobAddEdit', 'save'), 10, 2 );

            // update the post, which calls save_post again
            wp_update_post( array( 'ID' => $post_id, 'post_parent' => $post_parent ) );

            // re-hook this function
            add_action( 'save_post', array('JobAddEdit', 'save'), 10, 2 );
        }
    }


    public static function getDatalist( $key ){
		global $wpdb;
		
		$datalists = apply_filters('job-postings/datalists', true);
		$datalists_key = apply_filters('job-postings/datalists/'.$key, true);

		if( $datalists && $datalists_key ){
			$out	= '';
			$rows = $wpdb->get_results( 
				$wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = '%s' AND meta_value != ''", $key ) 
			);
			if( !empty($rows) ){
				$out .= '<datalist id="datalist-'.esc_attr($key).'">';
					foreach ($rows as $key => $row) {
						$out .= '<option value="'.htmlspecialchars($row->meta_value).'" />';
					}
				$out .= '</datalist>';
				return $out;
			}
		}

		return '';
    }

}