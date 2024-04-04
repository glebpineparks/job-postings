<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobSingleView
{

    public static $json_ld          = array();
    public static $fields_sorted    = array();
    public static $modal_in         = false;
    public static $inline_form_in   = false;
    public static $modal_form_in    = false;
    
    public static function init(){

        self::$json_ld = array(
            "@context" => "http://schema.org",
            "@type"	=> "JobPosting",
        );

    }


    public static function get_job_fields( $post_id, $position = 'sort-left' ){
        if( !$post_id ) return;

        wp_enqueue_script('jquery');
		wp_enqueue_script('jp-front-select2');
		wp_enqueue_script('jp-google-recaptcha');
        wp_enqueue_script('jp-front-scripts');
		wp_enqueue_style('jp-front-select2');
        wp_enqueue_style('jp-front-styles');


        $fields 	= Job_Postings::$fields;

        $validThrough = true;

        if( $fields ){
            $out 		= '';

            // SORT
            foreach ($fields as $index => $field) {
                $key 		= isset($field['key']) ? $field['key'] : false;
                if( !$key ) continue;

                $sort_index_f = get_post_meta( $post_id, 'sort-'.$key, true );

                if( strpos($sort_index_f, $position) === false ){

                    if( $key == 'position_valid_through' )
                        $validThrough = false;

                    unset( $fields[$index] );

                    continue;
                }

                $sort_index = str_replace($position.'-', '', $sort_index_f);

                if( $sort_index == '' ) $sort_index = $index;

                $fields[$index]['old_sort'] 	= $field['sort'];
                $fields[$index]['curr_sort'] 	= $sort_index_f;
                $fields[$index]['sort'] 		= $sort_index;

            }

            usort($fields, array('Job_Postings_Helper','sortByOrder') );

            self::$fields_sorted[] = $fields;

            $currency_symbol = get_option( 'jobs_currency_symbol'.'_'.Job_Postings::getLang() );
            if(!$currency_symbol) $currency_symbol = '€';

            
            JobSingleView::$inline_form_in 	= false;
            JobSingleView::$modal_form_in 	= true;
            
            // Check what form type we use on this page
            foreach ($fields as $key => $field) {

                $key 		= isset($field['key']) ? $field['key'] : false;

                if( $key == 'position_inline_apply_now' ){
                    JobSingleView::$inline_form_in 	= true;
                    JobSingleView::$modal_form_in 	= false;
                    break;
                }
            }


            // RENDER
            foreach ($fields as $key => $field) {


                $values 	= get_post_custom( $post_id );

                $type 		= isset($field['type']) ? $field['type'] : 'input';
                $name 		= isset($field['name']) ? $field['name'] : 'Field';
                $key 		= isset($field['key']) ? $field['key'] : false;
                $required 	= isset($field['required']) ? $field['required'] : false;
                $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
                $options 	= isset($field['options']) ? $field['options'] : array();
                $teeny 		= isset($field['teeny']) ? $field['teeny'] : false;
                $class 		= isset($field['class']) ? $field['class'] : array();

                $teeny 		= apply_filters('jobs-postings/tinymce_teeny', $teeny);


                $custom_title = isset( $values[$key.'-custom-title'] ) ? esc_attr($values[$key.'-custom-title'][0]) : '';
                $custom_title = htmlspecialchars($custom_title);
                if( $custom_title ) $name = sanitize_text_field( $custom_title );

                $show_title = true;

                if( strpos($key, 'position_custom_text') !== false )
                    $show_title = false;

                if( $custom_title != '' )
                    $show_title = true;


                if( isset( $values[$key.'-hide-title'] ) && $values[$key.'-hide-title'][0] == 'on' )
                    $show_title = false;


                // $hide_field = false;
                // if( isset( $values[$key.'-hide-field'] ) && $values[$key.'-hide-title'][0] == 'on' )
                //     $hide_field = true;

                $meta = '';
                $meta_2 = '';
                switch($key){

                    case 'position_title';
                        $meta = 'itemprop="title"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['title'] = esc_html($value);
                    break;

                    case 'position_description';
                        $meta = 'itemprop="description"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['description'] = esc_html($value);
                    break;

                    case 'position_responsibilities';
                        $meta 	= 'itemprop="responsibilities"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['responsibilities'] = esc_html($value);
                    break;

                    case 'position_qualifications';
                        $meta 	= 'itemprop="qualifications"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['qualifications'] = esc_html($value);
                    break;

                    case 'position_job_benefits';
                        $meta 	= 'itemprop="jobBenefits"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['jobBenefits'] = esc_html($value);
                    break;

                    case 'position_work_hours';
                        $meta 	= 'itemprop="workHours"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['workHours'] = esc_html($value);
                    break;

                    case 'position_base_salary';
                        $meta 	= 'itemprop="baseSalary"';

                        $value      = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value      = htmlspecialchars($value);
                        $upto       = isset( $values[$key.'_upto'] ) ? esc_attr( $values[$key.'_upto'][0] ) : '';
                        $upto       = htmlspecialchars($upto);
                        $unittext_value   = isset( $values[$key.'_unittext'] ) ? esc_attr( $values[$key.'_unittext'][0] ) : '';
                        $unittext_value = htmlspecialchars($unittext_value);

                        $baseSalary = Job_Postings_Helper::numbers_only($value);
                        if($upto) $upto =  Job_Postings_Helper::numbers_only($upto);

                        if( $value ) {

                            $baseSallaryArr = array(
                                    "@type" => "MonetaryAmount"
                                );
                            if($currency_symbol){
                                $baseSallaryArr["currency"] = $currency_symbol;
                            }

                            if( $unittext_value ){
                                $baseSallaryArr['value']['@type'] = 'QuantitativeValue';

                                if($upto){
                                    $baseSallaryArr['value']['minValue'] = $baseSalary;
                                    $baseSallaryArr['value']['maxValue'] = $upto;
                                }else{
                                    $baseSallaryArr['value']['value'] = $baseSalary;
                                }
                                $baseSallaryArr['value']['unitText'] = $unittext_value;
                            }else{
                                if($upto){
                                    $baseSallaryArr['minValue'] = $baseSalary;
                                    $baseSallaryArr['maxValue'] = $upto;
                                }else{
                                    $baseSallaryArr['value'] = $baseSalary;
                                }
                            }

                            


                            self::$json_ld['baseSalary'] = $baseSallaryArr;
                            if($currency_symbol) self::$json_ld['salaryCurrency'] = $currency_symbol;
                        }
                    break;

                    case 'position_industry';
                        $meta = 'itemprop="industry"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['industry'] = $value;
                    break;

                    case 'position_employment_type';
                        $meta = 'itemprop="employmentType"';

                        //$value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = get_post_meta( $post_id, $key, true );
                        $value = is_array($value) ? array_values($value) : $value;
                        if( $value && !is_array($value) ) $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['employmentType'] = $value;
                    break;



                    case 'position_educationRequirements';
                        $meta = 'itemprop="educationRequirements"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['educationRequirements'] = $value;
                    break;

                    case 'position_experienceRequirements';
                        $meta = 'itemprop="experienceRequirements"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['experienceRequirements'] = $value;
                    break;

                    case 'position_skills';
                        $meta = 'itemprop="skills"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) self::$json_ld['skills'] = $value;
                    break;


                    case 'position_job_location';
                        $meta 	= 'itemprop="jobLocation" itemscope itemtype="http://schema.org/Place"';
                        $meta_2 = 'itemprop="address" itemprop="addressLocality"';


                        $city = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $city = htmlspecialchars($city);

                        $streetAddress = isset( $values[$key.'_streetAddress'] ) ? esc_attr( $values[$key.'_streetAddress'][0] ) : '';
                        $streetAddress = htmlspecialchars($streetAddress);

                        $postalCode = isset( $values[$key.'_postalCode'] ) ? esc_attr( $values[$key.'_postalCode'][0] ) : '';
                        $postalCode = htmlspecialchars($postalCode);

                        $addressLocality = (isset( $values[$key.'_addressLocality'] ) && $values[$key.'_addressLocality'][0] != '') ? esc_attr( $values[$key.'_addressLocality'][0] ) : '';
                        $addressLocality = htmlspecialchars($addressLocality);

                        $addressRegion = (isset( $values[$key.'_addressRegion'] ) && $values[$key.'_addressRegion'][0] != '') ? esc_attr( $values[$key.'_addressRegion'][0] ) : '';
                        $addressRegion = htmlspecialchars($addressRegion);

                        $addressCountry = (isset( $values[$key.'_addressCountry'] ) && $values[$key.'_addressCountry'][0] != '') ? esc_attr( $values[$key.'_addressCountry'][0] ) : '';
                        $addressCountry = htmlspecialchars($addressCountry);


                        $remote = isset( $values[$key.'_remote'] ) ? $values[$key.'_remote'][0] : '';

                        $remote_data = isset( $values['job_remote_data'] ) ? $values['job_remote_data'][0] : '';
                        

                        if( $city || $addressLocality || $addressRegion || $postalCode || $addressCountry || $streetAddress ) {
                            // self::$json_ld['jobLocation'] = array(
                            // 		"@type" 	=> "Place",
                            // 		"address" 	=> array(
                            // 				"@type" 			=> "PostalAddress",
                            // 				"name"				=> $city,
                            // 				"addressLocality"	=> $addressLocality,
                            // 				"addressRegion"		=> $addressRegion,
                            // 				"postalCode" 		=> $postalCode,
                            // 				"streetAddress" 	=> $streetAddress
                            // 			)
                            // 	);

                            self::$json_ld['jobLocation'] = array(
                                    "@type" 	=> "Place",
                                    "address" 	=> array(
                                                "@type" 			=> "PostalAddress"
                                            )
                                );

                            if($city) self::$json_ld['jobLocation']['address']['name'] = $city;

                            if($addressLocality) self::$json_ld['jobLocation']['address']['addressLocality'] = $addressLocality;

                            if($addressRegion) self::$json_ld['jobLocation']['address']['addressRegion'] = $addressRegion;

                            if($postalCode) self::$json_ld['jobLocation']['address']['postalCode'] = $postalCode;

                            if($streetAddress) self::$json_ld['jobLocation']['address']['streetAddress'] = $streetAddress;

                            if($addressCountry) self::$json_ld['jobLocation']['address']['addressCountry'] = $addressCountry;

                        }

                        if( $remote == 'on' && $remote_data ){
                            $remote_data = unserialize($remote_data);

                            if( count($remote_data) >= 1 && $remote_data[0]['type'] != '' && $remote_data[0]['name'] != '' ){

                                if(count($remote_data) == 1){
                                    self::$json_ld['applicantLocationRequirements'] = json_decode(json_encode(array('@type' => $remote_data[0]['type'], 'name' => $remote_data[0]['name'])), FALSE);
                                }else{
                                    $data_array = array();
                                    foreach($remote_data as $data){
                                        $data_array[] = array('@type' => $data['type'], 'name' => $data['name']);
                                    }
                                    self::$json_ld['applicantLocationRequirements'] = json_decode(json_encode($data_array), FALSE);
                                }
                            }
                        }

                        if( $remote == 'on' ){
                            self::$json_ld['jobLocationType'] = "TELECOMMUTE";
                        }
                    break;


                    case 'position_valid_through';
                        $meta = 'itemprop="validThrough"';

                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                        $value = htmlspecialchars($value);
                        if( $value ) {
                            $value = date('c', strtotime($value));
                            if($value) self::$json_ld['validThrough'] = $value;
                        }
                    break;


                    case 'position_logo';
                        $company_logo 			    = get_option('jobs_company_logo');
                        $hiring_organization_logo 	= isset( $values[$key] ) && $values[$key][0] != '' ? esc_attr( $values[$key][0] ) : $company_logo;
        
                        $hiring_organization 	= isset( $values['position_hiring_organization_name'] ) && $values['position_hiring_organization_name'][0] != '' ? esc_attr( $values['position_hiring_organization_name'][0] ) : get_option('jobs_hiring_organization'.'_'.Job_Postings::$lang);
                        $hiring_organization = htmlspecialchars($hiring_organization);
                        if(!$hiring_organization) $hiring_organization = get_option('blogname');
    
                        if ( $hiring_organization ) {
                            self::$json_ld['hiringOrganization']['@type'] = 'Organization';
                            self::$json_ld['hiringOrganization']['name'] = esc_attr($hiring_organization);
                            if($hiring_organization_logo) self::$json_ld['hiringOrganization']['logo'] = esc_attr($hiring_organization_logo);
                            self::$json_ld['identifier']['@type'] = 'PropertyValue';
                            self::$json_ld['identifier']['name'] = esc_attr($hiring_organization);
                            self::$json_ld['identifier']['value'] = esc_attr($hiring_organization);
                        }
                    break;
                }

                // JSON+LD Date
                if($post_id){
                    $date = get_the_date('c', $post_id );
                    if($date) self::$json_ld['datePosted'] = $date;
                }


                // if we use json-ld, clear itemprop attributes
                //if($this->schema_type == 'json-ld'){
                    $meta = '';
                    $meta_2 = '';
                //}
                
                $class = implode(' ', $class);

                // Move to next field if we want to hide this field from job offer
                if( isset( $values[$key.'-hide-field'] ) && $values[$key.'-hide-field'][0] == 'on' ) 
                    continue;
                
                //field html tag
                $field_tag = isset( $values[$key.'-field-tag-title'] ) ? esc_attr($values[$key.'-field-tag-title'][0]) : 'div';
                $t_tag = apply_filters('job-postings/field/tag-title/'.$key, $field_tag);

                $field_tag = isset( $values[$key.'-field-tag'] ) ? esc_attr($values[$key.'-field-tag'][0]) : 'div';
                $tag = apply_filters('job-postings/field/tag/'.$key, $field_tag);

                //field custom class
                if( isset( $values[$key.'-field-class'][0] ) ){
                    $class = $class . ' ' . esc_attr($values[$key.'-field-class'][0]);
                }

                $value = isset( $values[$key] ) ? esc_attr($values[$key][0]) : '';

                if( $value || $type == 'location' ){


                    $skip = array('position_apply_now', 'position_button', 'position_pdf_export', 'position_valid_through', 'position_logo');

                    if( !in_array($key, $skip) ){

                        $out .= '<div class="jobs-row clearfix '.$key.' type-'.$type.' '.$class.'">';

                            if( $show_title ){
                                $out .= '<'.$t_tag.' class="jobs-row-label">';
                                    $out .= '<span>'. $name . '</span>';
                                $out .= '</'.$t_tag.'>';
                            }




                            $out .= '<'.$tag.' '.$meta.' class="jobs-row-input">';
                            if( $meta_2 ) $out .= '<span '.$meta_2.'>';



                            if( $key != 'position_base_salary' ){
                                
                                switch ( $type ) {
                                    case 'textarea':
                                        # INPUT
                                        $value = isset( $values[$key] ) ? $values[$key][0] : '';
                                        $value = htmlspecialchars_decode($value);
                                        $icon = '';

                                        if($key == 'position_work_hours'){
                                            $icon = Job_Postings_Helper::getRawSvg( 'clock.svg' );
                                        }

                                        $out .= $icon.$value;
                                        break;


                                    case 'location':
                                        # INPUT

                                        $city = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                                        $city = htmlspecialchars($city);

                                        $streetAddress = isset( $values[$key.'_streetAddress'] ) ? esc_attr( $values[$key.'_streetAddress'][0] ) : '';
                                        $streetAddress = htmlspecialchars($streetAddress);

                                        $postalCode = isset( $values[$key.'_postalCode'] ) ? esc_attr( $values[$key.'_postalCode'][0] ) : '';
                                        $postalCode = htmlspecialchars($postalCode);

                                        // $addressLocality = (isset( $values[$key.'_addressLocality'] ) && $values[$key.'_addressLocality'][0] != '') ? esc_attr( $values[$key.'_addressLocality'][0] ) : '';
                                        $addressRegion = (isset( $values[$key.'_addressRegion'] ) && $values[$key.'_addressRegion'][0] != '') ? esc_attr( $values[$key.'_addressRegion'][0] ) : '';
                                        $addressRegion = htmlspecialchars($addressRegion);

                                        $addressCountry = (isset( $values[$key.'_addressCountry'] ) && $values[$key.'_addressCountry'][0] != '') ? esc_attr( $values[$key.'_addressCountry'][0] ) : '';
                                        $addressCountry = htmlspecialchars($addressCountry);

                                        $remote = isset( $values[$key.'_remote'] ) ? $values[$key.'_remote'][0] : '';

                                        $remote_data = isset( $values['job_remote_data'] ) ? $values['job_remote_data'][0] : '';


                                        $full_address = array();

                                        if( $streetAddress )
                                            $full_address[] = $streetAddress;


                                        if( $city )
                                            $full_address[] = $city;

                                        // if( $addressLocality )
                                        //     $full_address[] = $addressLocality;

                                        if( $addressRegion )
                                            $full_address[] = $addressRegion;

                                        if( $postalCode )
                                            $full_address[] = $postalCode;

                                        if( $addressCountry )
                                            $full_address[] = $addressCountry;
                                        

                                        $icon = Job_Postings_Helper::getRawSvg( 'pin.svg' );

                                        if($full_address) {
                                            $full_address = apply_filters('job-postings/full_address', $icon . implode(', ', $full_address), $full_address, $icon);
                                            $out .= $full_address;
                                        }


                                        if($remote_data){
                                            $remote_data = unserialize($remote_data);
                                        }

                                        $icon_remote = Job_Postings_Helper::getRawSvg( 'wifi-signal.svg' );

                                        $icon_remote = apply_filters('job-postings/remote-icon', $icon_remote);

                                        $remote_possible = apply_filters('job-postings/'.$post_id.'/remote-possible-text', __('Remote work possible', 'job-postings'));

                                        $remote_from = apply_filters('job-postings/'.$post_id.'/remote-from-text', __('Remote work from', 'job-postings') . ': ');

                                        if( $remote == 'on' && $remote_data && (count($remote_data) >= 1 && $remote_data[0]['type'] != '' && $remote_data[0]['name'] != '') ){

                                            if($remote_data){
                                                $remote_places = array();
                                                foreach($remote_data as $data){
                                                    $remote_places[] = $data['name'];
                                                }
                                            }

                                            $remote_word_places = apply_filters('job-postings/'.$post_id.'/remote-places', implode('; ', $remote_places), $remote_data);

                                            $out .= '<div class="jobs-remote-work">';
                                                $out .= $icon_remote . $remote_from . $remote_word_places;
                                            $out .= '</div>';
                                        }else if( $remote == 'on' ){
                                            $out .= '<div class="jobs-remote-work">';
                                                $out .= $icon_remote . $remote_possible;
                                            $out .= '</div>';
                                        }


                                        break;


                                    case 'tinymce':

                                        # INPUT
                                        $value = isset( $values[$key] ) ? $values[$key][0] : '';
                                        //$value = htmlspecialchars($value);

                                        //$out .= apply_filters('the_content', $value);
                                        $tinymce_content = wpautop($value);

                                        if( class_exists('WP_Embed') ) {
                                            $wpembed = new WP_Embed();
                                            $tinymce_content = $wpembed->autoembed( $tinymce_content );
                                            $tinymce_content = $wpembed->run_shortcode( $tinymce_content );
                                        }
                                        $out .= do_shortcode( $tinymce_content );
                                        break;



                                    case 'file':
                                        # INPUT
                                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                                        $value = htmlspecialchars($value);
                                        $btn_name = isset( $values[$key.'_name'] ) ? esc_attr( $values[$key.'_name'][0] ) : '';
                                        $btn_name = htmlspecialchars($btn_name);

                                        $url 		= '';
                                        $filename 	= $btn_name;


                                        $url = esc_url_raw($value);


                                        // -- Depricated. Stays for backward compatibility
                                        if( strpos($value, '|') !== false ){
                                            $explode = explode('|', $value);

                                            if( isset($explode[1]) ) {
                                                $filename = sanitize_text_field($explode[1]);
                                            }

                                            if( isset( $explode[0] ) ){
                                                $url = esc_url_raw( $explode[0] );
                                            }
                                        }
                                        // --

                                        if($filename == ''){
                                            $filename = $url;
                                        }


                                        if( $url != '' ) {

                                            $pathinfo = pathinfo($url);
                                            $extension = $pathinfo['extension'];

                                            $out .= '<a href="'.$url.'" target="_blank" class="button jp-attachment-button ext-'.$extension.'">'.$filename.'</a>';

                                        }
                                        break;

                                    case 'checkboxes':
                                        $value = get_post_meta( $post_id, $key, true );

                                        $list = array();

                                        if( is_array($value) && !empty($options) ){
                                            foreach ($value as $vk => $value_key) {
                                                if( isset($options[$value_key]) && $value_key != 'OTHER' ) {
                                                    $list[] = htmlspecialchars($options[$value_key]);
                                                }
                                            }
                                            if( isset($value['other_input']) ){
                                                $list[] = $value['other_input'];
                                            }
                                        }else if( !is_array($value) && $value != '' ){
                                            $list[] = $value;
                                        }

                                        $out .= apply_filters('job-postings/format_list', implode(', ', $list), $list);
                                        break;

                                    default:
                                        # INPUT
                                        $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                                        $value = htmlspecialchars($value);

                                        $out .= $value;
                                        break;
                                }
                            }

                            if( $key == 'position_base_salary' ){
                                
                                $value 	            = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                                $value              = htmlspecialchars($value);
                                $upto 	            = isset( $values[$key.'_upto'] ) ? esc_attr( $values[$key.'_upto'][0] ) : '';
                                $upto               = htmlspecialchars($upto);
                                $unittext_value     = isset( $values[$key.'_unittext'] ) ? esc_attr( $values[$key.'_unittext'][0] ) : '';
                                $unittext_value     = htmlspecialchars($unittext_value);
							    $unitText 			= isset($field['unitText']) ? $field['unitText'] : array();

                                if($currency_symbol){
                                    $currency_position = get_option( 'jobs_currency_position'.'_'.Job_Postings::getLang() );
                                    if(!$currency_position) $currency_position = 'before';

                                    $starting = '';
                                    $to = '';

                                    switch ($currency_position) {
                                        case 'after':
                                            $out .= $starting . $value . '' . $currency_symbol;
                                            if( $upto ) {
                                                $out .= $starting = apply_filters('job-postings/salary-range-separator', '<span>-</span>');
                                                $out .= $to . $upto . '' . $currency_symbol;
                                            }
                                            break;
                                        default:
                                            $out .= $starting . $currency_symbol . '' . $value;
                                            if( $upto ) {
                                                $out .= $starting = apply_filters('job-postings/salary-range-separator', '<span>-</span>');
                                                $out .= $currency_symbol . '' . $to . $upto;
                                            }
                                            break;
                                    }

                                }else{
                                    $out .= esc_html($value);
                                }

                                if( !empty($unitText) && isset($unitText[$unittext_value]) ){
                                    $out .= apply_filters('job-postings/unitText_output', '<i class="unittext"> '.$unitText[$unittext_value].'</i>', $unittext_value, $unitText);
                                }
                            }


                            if( $meta_2 ) $out .= '</span>';
                            $out .= '</'.$tag.'>';
                        $out .= '</div>';
                    }
                } // if value


                if( $key == 'position_logo' ){
                    //$company_logo = get_option('jobs_company_logo');

                    if( $hiring_organization_logo || $hiring_organization ){
                        $out .= '<div class="jobs-row clearfix position_logo type-'.$type.' '.$class.'">';
                            if( $show_title ){
                                if( $custom_title ) $name = $custom_title;
                                $out .= '<div class="jobs-row-label">';
                                    $out .= '<span>'. $name . '</span>';
                                $out .= '</div>';
                            }
                            $out .= '<div '.$meta.' class="jobs-row-input">';
                                if( $meta_2 ) $out .= '<span '.$meta_2.'>';

                                    if($hiring_organization_logo){
                                        $hiring_organization = esc_html($hiring_organization);
                                        $out .= '<img class="jobs_hiring_logo" src="'.$hiring_organization_logo.'" alt="'.$hiring_organization.'" title="'.$hiring_organization.'">';
                                    }else if($hiring_organization){
                                        $out .= $hiring_organization;
                                    }

                                if( $meta_2 ) $out .= '</span>';
                            $out .= '</div>';
                        $out .= '</div>';
                    }
                }


                if( $key == 'position_date_posted' ){
                    $job_date = get_the_date( get_option('date_format'), $post_id );
                    $job_date = esc_attr($job_date);
                    $out .= '<div class="jobs-row clearfix type-'.$type.' '.$class.'">';
                        $out .= '<div '.$meta.' class="jobs-row-content">';
                            if( $meta_2 ) $out .= '<span '.$meta_2.'>';
                                $out .= '<div class="jobs-row-inset clearfix type-date-posted">';
                                    if( $show_title ){
                                        $name = apply_filters('jp-apply-date-posted', _x('Date posted','apply-now', 'job-postings') );
                                        if( $custom_title ) $name = $custom_title;
                                        $out .= '<div class="jobs-row-label">';
                                            $out .= '<span>'. $name . '</span>';
                                        $out .= '</div>';
                                    }
                                    $out .= '<div class="jobs-row-input">';

                                        $icon = Job_Postings_Helper::getRawSvg( 'event.svg' );

                                        $out .= $icon . $job_date;
                                    $out .= '</div>';

                                    //if($this->schema_type != 'json-ld') $out .= '<meta itemprop="datePosted" content="'.$date.'">';
                                $out .= '</div>';
                            if( $meta_2 ) $out .= '</span>';
                        $out .= '</div>';
                    $out .= '</div>';
                }

                $job_valid_date = '';

                if( $validThrough ){
                    $job_valid_date = isset( $values['position_valid_through'] ) ? esc_attr($values['position_valid_through'][0]) : '';
                }


                if( $key == 'position_valid_through' && $job_valid_date  ){
                    //$job_date = isset( $values[$key] ) ? esc_attr($values[$key][0]) : '';

                    $out .= '<div class="jobs-row clearfix type-'.$type.' '.$class.'">';
                        $out .= '<div '.$meta.' class="jobs-row-content">';
                            if( $meta_2 ) $out .= '<span '.$meta_2.'>';
                                $out .= '<div class="jobs-row-inset clearfix type-date-posted">';
                                    if( $show_title ){
                                        $name = apply_filters('jp-apply-date-posted', _x('Valid through','jobs-field', 'job-postings') );
                                        if( $custom_title ) $name = $custom_title;
                                        $out .= '<div class="jobs-row-label">';
                                            $out .= '<span>'. $name . '</span>';
                                        $out .= '</div>';
                                    }
                                    $out .= '<div class="jobs-row-input">';

                                        $icon = Job_Postings_Helper::getRawSvg( 'calendar-x.svg' );

                                        $job_valid_date = date_i18n( get_option('date_format'), strtotime($job_valid_date) );
                                        $out .= $icon . $job_valid_date;
                                    $out .= '</div>';

                                    // $date = date('c', strtotime($job_valid_date));

                                    // self::$json_ld['validThrough'] = $date;
                                    //if($this->schema_type != 'json-ld') $out .= '<meta itemprop="validThrough" content="'.$date.'">';
                                $out .= '</div>';
                            if( $meta_2 ) $out .= '</span>';
                        $out .= '</div>';
                    $out .= '</div>';
                }

                if( $key == 'position_pdf_export' ){

                    $value = htmlspecialchars($value);
                    $out .= '<div class="jobs-row clearfix type-'.$type.' '.$class.'">';
                        $out .= '<div '.$meta.' class="jobs-row-content">';
                            if( $meta_2 ) $out .= '<span '.$meta_2.'>';
                                $out .= '<div class="jobs-row-inset clearfix type-date-posted">';
                                    if( $show_title ){
                                        $name = apply_filters('jp-apply-pdf-export', _x('PDF Export','apply-now', 'job-postings') );
                                        if( $custom_title ) $name = $custom_title;
                                        $out .= '<div class="jobs-row-label">';
                                            $out .= '<span>'. $name . '</span>';
                                        $out .= '</div>';
                                    }

                                    $btn = $value;
                                    if( !$btn ) $btn = _x('Export as PDF', 'apply-now', 'job-postings');

                                    $out .= '<div class="jobs-row-input">';
                                        //$pdf_ico = '<img src="'.plugin_dir_url( __FILE__ ).'/images/pdf.svg">';
                                        $pdf_ico = Job_Postings_Helper::getRawSvg( 'pdf.svg' );
                                        $out .= '<a href="?export-pdf='.$post_id.'" class="job-pdf-export">'.$pdf_ico.$btn.'</a>';
                                    $out .= '</div>';
                                $out .= '</div>';
                            if( $meta_2 ) $out .= '</span>';
                        $out .= '</div>';
                    $out .= '</div>';
                }

                if( $key == 'position_apply_now' && JobSingleView::$modal_form_in == true ){
                    // JobSingleView::$modal_form_in = true;
                    // JobSingleView::$inline_form_in = false;

                    $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                    $value = htmlspecialchars($value);

                    $offer_ended_enabled = get_option( 'jobs_offer_ended_message_enabled_'.Job_Postings::getLang() );
                    $offer_ended_enabled = htmlspecialchars($offer_ended_enabled);
                    $offer_ended_message = get_option( 'jobs_offer_ended_message_'.Job_Postings::getLang() );
                    $offer_ended_message = htmlspecialchars($offer_ended_message);
                    $offer_ended_message = $offer_ended_message ?  sprintf($offer_ended_message, date_i18n(get_option('date_format'), strtotime($job_valid_date)) ) : sprintf(_x('Offer ended on %s', 'job-message', 'job-postings'), date_i18n(get_option('date_format'), strtotime($job_valid_date)) );

                    $out .= '<div class="jobs-row-apply">';

                        if( $job_valid_date && ($offer_ended_enabled == 'on' && strtotime('23:59:59', strtotime($job_valid_date)) < date('U') ) ){
                            $out .=  '<p class="jobs-offer-ended">'. $offer_ended_message . '</p>';
                        }else{

                            $apply = _x('Apply now','apply-now', 'job-postings');
                            if( $value ) {
                                $apply = $value;
                                $out .= '<button class="button jp-apply-button">'.$apply.'</button>';
                            }
                        }

                    $out .= '</div>';
                }


                if( $key == 'position_inline_apply_now' && JobSingleView::$inline_form_in == true ){
                    // JobSingleView::$inline_form_in 	= true;
                    // JobSingleView::$modal_form_in 	= false;

                    $value = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                    $value = htmlspecialchars($value);

                    $offer_ended_enabled = get_option( 'jobs_offer_ended_message_enabled_'.Job_Postings::getLang() );
                    $offer_ended_message = get_option( 'jobs_offer_ended_message_'.Job_Postings::getLang() );
                    $offer_ended_message = $offer_ended_message ?  sprintf($offer_ended_message, $job_valid_date) : sprintf(_x('Offer ended on %s', 'job-message', 'job-postings'), $job_valid_date);

                    $out .= '<div class="jobs-row-apply">';

                        if( $job_valid_date && ($offer_ended_enabled == 'on' && strtotime($job_valid_date) < date('U')) ){
                            $out .=  '<p class="jobs-offer-ended">'. $offer_ended_message . '</p>';
                        }else{
                            $out .= JobApplyForm::get_apply_form( true, $custom_title, $name, $show_title, $post_id );
                        }

                    $out .= '</div>';
                }


                if( $key == 'position_button' ){
                    $style 	= isset( $values[$key.'-style'] ) ? esc_attr( $values[$key.'-style'][0] ) : 'primary-style';
                    $url 	= isset( $values[$key.'-url'] ) ? esc_url( $values[$key.'-url'][0] ) : '';
                    $target = isset( $values[$key.'-url-target'] ) ? esc_attr( $values[$key.'-url-target'][0] ) : '_blank';
                    $value 	= isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
                    $value = htmlspecialchars($value);

                    $out .= '<div class="jobs-row jobs-row-button">';
                        if( $show_title ){
                            if( $custom_title ) $name = $custom_title;
                            $out .= '<div class="jobs-row-label">';
                                $out .= '<span>'. $name . '</span>';
                            $out .= '</div>';
                        }
                        if( $value ) {
                            $out .= '<a href="'.$url.'" class="button '.$style.'" target="'.$target.'">'.$value.'</a>';
                        }
                    $out .= '</div>';
                }

            } // end fields foreach



            // if( JobSingleView::$inline_form_in == false && JobSingleView::$modal_form_in == true  ) {
            //     //JobSingleView::$modal_form_in = true;
            //     //JobSingleView::$modal_in = true;
            //     /*
            //     if($this->schema_type != 'json-ld'){
            //         if($hiring_organization) {
            //             $out .= '<meta itemprop="hiringOrganization" content="'.$hiring_organization.'">';
            //         }
            //     }
            //     */

            //     $out .= JobApplyForm::get_apply_modal( $post_id );
            // }


            return $out;
        }
    }

    public static function is_inline_form_used(){
        $fields 	= self::$fields_sorted;
        $fields     = array_merge($fields[0], $fields[1]);

        // print_r( '<pre>' );
        // print_r( $fields );
        // print_r( '</pre>' );

        // Check what form type we use on this page
        foreach ($fields as $key => $field) {

            $key 		= isset($field['key']) ? $field['key'] : false;

            if( $key == 'position_inline_apply_now' ){
                return true;
                break;
            }
        }

        return false;
    }

    public static function get_apply_modal_markup( $post_id ){


        if( JobSingleView::is_inline_form_used() == false  ) {
            return JobApplyForm::get_apply_modal( $post_id );
        }
    }

    public static function print_json_ld( $job_id ){
        if( apply_filters( 'job-postings/disable_json_ld', false, $job_id ) == true ) return;
        echo '<script type="application/ld+json">'.json_encode( self::get_json_ld( $job_id ) ).'</script>';
    }

    public static function get_json_ld( $job_id ){
		$json_ld_keys = self::$json_ld;
		$json_script = $json_ld_keys;
        $current_description = '';
		
		/**
		* Adding multiple tags (e.g reponsibilities, qualifications, benefits) to description as whole 
		*/
		foreach( $json_script as $k => $v ){
			
			if( $k == 'description' && !empty($v) ){
				$current_description = $v;
				$json_ld_keys = $json_script;
			}
			
			$values 	= get_post_custom( $job_id );
			$prefix = $k;
			$prefix = ( $prefix == "jobBenefits" ) ? "job_benefits" : $prefix;
			$custom_title = isset( $values['position_'.$prefix.'-custom-title'] ) ? esc_attr($values['position_'.$prefix.'-custom-title'][0]) : '';
			$custom_title = htmlspecialchars($custom_title);
			if( $custom_title ) $name = sanitize_text_field( $custom_title );

			$show_title = true;

			if( $custom_title != '' )
				$show_title = true;

			if( empty($custom_title) )
				$show_title = false;

			if( isset( $values['position_'.$prefix.'-hide-title'] ) && $values['position_'.$prefix.'-hide-title'][0] == 'on' )
				$show_title = false;
			
			if( $k == 'responsibilities' ){
				$title = ( $show_title ) ? '<h2>'.$name.'</h2><br>' : '';
				$responsibilities_data = $title.$json_script['responsibilities'];
				$json_script['description'] = $current_description . $responsibilities_data;
				$current_description = $current_description . $responsibilities_data;
			}
			
			if( $k == 'qualifications' ){
				$title = ( $show_title ) ? '<h2>'.$name.'</h2><br>' : '';
				$qualifications_data = $title.$json_script['qualifications'];
				$json_script['description'] = $current_description . $qualifications_data;
				$current_description = $current_description . $qualifications_data;
			}

			if( $k == 'jobBenefits' ){
				$title = ( $show_title ) ? '<h2>'.$name.'</h2><br>' : '';
				$jobbenefits_data = $title.$json_script['jobBenefits'];
				$json_script['description'] = $current_description . $jobbenefits_data;
				$current_description = $current_description . $jobbenefits_data;
			}
		}
		
        $json_ld = apply_filters( 'job-postings/json_ld', $json_script, $job_id );
        return $json_ld;
    }

    public static function print_json_ld_yoast($job_id){
        $site_url = site_url();
        $custom_slug = get_option('jobs_custom_slug_'.Job_Postings::$lang );
		$slug = ( !empty($custom_slug) ) ? sanitize_key($custom_slug) : 'job';

        $graph_id = $site_url."/".$slug.'#'."jobpost";

        $yoast_script = [
            "@context" => "http://schema.org",
            "@graph" => [
                "@id" => $graph_id
            ]
        ];

        $json_ld_keys = self::$json_ld;
        foreach( $json_ld_keys as $k => $v ){
            $yoast_script['@graph'][$k] = $v;
            if( $k == "hiringOrganization" ){
                $org_slug = preg_replace('/\s+/', '_', $yoast_script['@graph'][$k]['name']);
                $org_slug = strtolower($org_slug);
                $yoast_script['@graph'][$k]['@id'] = site_url().'/#organization_'.$org_slug;
            }
			
			/**
			 * Added multiple tags (e.g reponsibilities, qualifications, benefits) to description as whole 
			 */
			if( isset($yoast_script['@graph']['description']) ){
				
				$current_description = $yoast_script['@graph']['description'];
				$values 	= get_post_custom( $job_id );
				$prefix = $k;
				$prefix = ( $prefix == "jobBenefits" ) ? "job_benefits" : $prefix;
                $custom_title = isset( $values['position_'.$prefix.'-custom-title'] ) ? esc_attr($values['position_'.$prefix.'-custom-title'][0]) : '';
                $custom_title = htmlspecialchars($custom_title);
                if( $custom_title ) $name = sanitize_text_field( $custom_title );

                $show_title = true;

                if( $custom_title != '' )
                    $show_title = true;

				if( empty($custom_title) )
					$show_title = false;

                if( isset( $values['position_'.$prefix.'-hide-title'] ) && $values['position_'.$prefix.'-hide-title'][0] == 'on' )
                    $show_title = false;
				
				if( isset($yoast_script['@graph']['responsibilities']) && $k == "responsibilities" ){
					$title = ( $show_title ) ? '<h2>'.$name.'</h2><br>' : '';
					$responsibilities_data = $title.$yoast_script['@graph']['responsibilities'];
					$yoast_script['@graph']['description'] = $current_description . $responsibilities_data;
				}
				
				if( isset($yoast_script['@graph']['qualifications']) && $k == "qualifications" ){
					$title = ( $show_title ) ? '<h2>'.$name.'</h2><br>' : '';
					$qualifications_data = $title.$yoast_script['@graph']['qualifications'];
					$yoast_script['@graph']['description'] = $current_description . $qualifications_data;
				}
					
				if( isset($yoast_script['@graph']['jobBenefits']) &&  $k == "jobBenefits" ){
					$title = ( $show_title ) ? '<h2>'.$name.'</h2><br>' : '';
					$jobbenefits_data = $title.$yoast_script['@graph']['jobBenefits'];
					$yoast_script['@graph']['description'] = $current_description . $jobbenefits_data;
				}
			}
        }
        
        $json_ld = apply_filters( 'job-postings/json_ld_yoast', $yoast_script, $job_id );
        echo '<script type="application/ld+json">'.json_encode( $json_ld ).'</script>';
    }
}

function get_job_fields( $job_id = '' ){
    //global $job_postings;

    if( $job_id == '' ){
        global $post;
        $job_id = $post->ID;
    }

    switch (Job_Postings::$side_position) {
        case 'left':
            $class_1 		= 'job-side';
            $class_2 		= 'job-content';
            $sort_type_1 	= 'sort-right';
            $sort_type_2 	= 'sort-left';
            break;

        default:
            $class_1 		= 'job-content';
            $class_2 		= 'job-side';
            $sort_type_1 	= 'sort-left';
            $sort_type_2 	= 'sort-right';
            break;
    }

    $itemscope = '';
    //if(Job_Postings::$schema_type != 'json-ld') $itemscope = 'itemscope itemtype="http://schema.org/JobPosting"';

    echo '<style>.jobs-modal.hide{ display: none; }</style>';

    // Print custom styles
    Job_Postings::customStyles();

    echo '<div class="job-post clearfix" '.$itemscope.' role="main">';

        do_action('job-postings/single/before_fields');

        $disable_featured_image = apply_filters('job-entry/disable_featured_image', true);
        if( has_post_thumbnail() && $disable_featured_image == true ){
            echo '<div class="job-image">';
                echo '<div class="job-content-wrap">';
                    the_post_thumbnail('job-image');
                echo '</div>';
            echo '</div>';
        }

        echo '<div class="'.$class_1.'">';
            echo '<div class="job-content-wrap">';
                echo JobSingleView::get_job_fields( $job_id, $sort_type_1 );
            echo '</div>';
            do_action('job-postings/single/after_left');
        echo '</div>';

        echo '<div class="'.$class_2.'">';
            echo '<div class="job-content-wrap">';
                echo JobSingleView::get_job_fields( $job_id, $sort_type_2 );
            echo '</div>';
            do_action('job-postings/single/after_right');
        echo '</div>';

        echo '<div class="clearfix"></div>';
        echo '<div class="job-content-wrap">';
            // This fixes the output of sharing icons for example
            echo apply_filters('the_content', '');
            do_action('job-postings/single/after_fields');
        echo '</div>';

        echo JobSingleView::get_apply_modal_markup( $job_id );

        // This must be at the end, as we combine it with 2 functions above
        $jobs_selected_schema = get_option( 'jobs_selected_schema' );
        if( !$jobs_selected_schema || empty($jobs_selected_schema) || $jobs_selected_schema == "default" ){            
            echo JobSingleView::print_json_ld( $job_id );
        }
        else if( $jobs_selected_schema == "yoast_seo" && class_exists('WPSEO_Admin') ){
            echo JobSingleView::print_json_ld_yoast( $job_id );
        }
        else{
            echo JobSingleView::print_json_ld( $job_id );
        }
        
        

    echo '</div>';

}

function jobs_list(){
    echo JobList::do_job_list();
}
