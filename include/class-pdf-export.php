<?php  

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$language = 'en';
$the_post_id = 0;


class Jobs_PDF_Header_Footer extends TCPDF {


	//Page header
	public function Header() {

		$this->footerData = $this->getHeaderData();

		// Logo
		//$this->Image($image_file, 0, 7, 35, 0, 'JPG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
	}

	// Page footer
	public function Footer() {
		global $language, $the_post_id;
		// Position at 15 mm from bottom
		$this->SetY(-15);

		$font_family = apply_filters('job-postings/pdf/font', 'helvetica');

		$font_style = apply_filters('job-postings/pdf/footer_font_style', 'I');
		$font_size = apply_filters('job-postings/pdf/footer_font_size', 9);
		// Set font
		$this->SetFont($font_family, $font_style, $font_size);

		$font_color = apply_filters('job-postings/pdf/footer_font_color', array(0,0,0));
		if(is_array($font_color)) $this->SetTextColor($font_color[0],$font_color[1],$font_color[2]);

		$blogdescription = get_option('blogdescription');
		$site_url = get_option('home');
		
		$hiring_organization_name 	= get_post_meta($the_post_id, 'position_hiring_organization_name', true);
		$hiring_organization 		= $hiring_organization_name ? esc_attr( $hiring_organization_name ) : get_option('jobs_hiring_organization'.'_'.$language);
		if(!$hiring_organization) $hiring_organization = get_option('blogname');

		$html = '
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>	
						'.$hiring_organization.'
					</td>
					<td style="text-align: right;">
						'.$blogdescription.'<br>'.$site_url.'
					</td>
				</tr>
			</table>
		';
		$this->writeHTMLCell('', '', '', '', $html, 0, 1, 0, true, '', true);
	}
}

class jobPDFExport{
		
	
	public function __construct( $post_id = null, $fields, $lang ){
		global $language, $the_post_id;
		//print_r($form);

		$language = $lang;
		$the_post_id = $post_id;


		// create new PDF document
		$pdf = new Jobs_PDF_Header_Footer(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$position_logo 	= get_post_meta($the_post_id, 'position_logo', true);
		$company_logo 	= get_option('jobs_company_logo');
		$company_logo 	= $position_logo ? esc_attr( $position_logo ) : $company_logo;
		
		$hiring_organization_name 	= get_post_meta($the_post_id, 'position_hiring_organization_name', true);
		$hiring_organization 		= $hiring_organization_name ? esc_attr( $hiring_organization_name ) : get_option('jobs_hiring_organization'.'_'.$language);
		if(!$hiring_organization) $hiring_organization = get_option('blogname');
		
	    $job_date 				= get_the_date( get_option('date_format'), $post_id );

		$title = get_post_meta($post_id, 'position_title', true);
		$title = sanitize_title($hiring_organization . ' ' .$title);
		$date = sanitize_title($job_date);
		$file_name = $title.'-'.$date;

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor( $hiring_organization );
		$pdf->SetTitle( $hiring_organization . ' - ' .$title . ' ' . $job_date );
		$pdf->SetSubject( $hiring_organization . ' - ' .$title . ' ' . $job_date );
		$pdf->SetKeywords( 'job position '. $hiring_organization . ' - ' .$title . ' ' . $job_date );

		$file_name = $file_name.'.pdf';

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	
		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);


		$font_family = apply_filters('job-postings/pdf/font', 'freesans');
	
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont($font_family, '', 9, '', true);

		$font_color = apply_filters('job-postings/pdf/font_color', array(0,0,0));
		if(is_array($font_color)) $pdf->SetTextColor($font_color[0],$font_color[1],$font_color[2]);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();


		$background_filename = TEMPLATEPATH.'/jobs-pdf/background.jpg';
		if( file_exists( $background_filename ) ){
			// get the current page break margin
			$bMargin = $pdf->getBreakMargin();
			// get current auto-page-break mode
			$auto_page_break = $pdf->getAutoPageBreak();
			// disable auto-page-break
			$pdf->SetAutoPageBreak(false, 0);
			// place image
			$pdf->Image($background_filename, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0, false, false, false);
			// restore auto-page-break status
			$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
			// set the starting point for the page content
			$pdf->setPageMark();
		}


		$style = '
		<style>
			table td{
				padding: 16px;
				vertical-align: top;
			}

			table td p{
				margin-top: 0;
				padding-top: 0;
			}
			p{
				margin-bottom: 0;
				padding-bottom: 0;
			}
		</style>
		';
		
		$logo_offset = apply_filters('job-postings/pdf/logo_offset', array(15,15));
		if( !is_array($logo_offset) ) $logo_offset = array(15,15);

		$offer_link = get_permalink( $post_id );
		$sitename = get_option('blogname');
		$sitename = '<b style="font-size: 20px;">'.apply_filters('job-postings/pdf/sitename', $sitename, $post_id).'</b><br><br>';

		if( $company_logo ) $sitename = '<img src="'.$company_logo.'" height="75"><br>';

		$html = $sitename . '<br><i>'.$offer_link.'<i><br><br><br>';
		//$pdf->writeHTML($style . $html, true, false, true, false, '');
		$pdf->writeHTMLCell(0, 0, $logo_offset[0], $logo_offset[1], $style . $html, 0, 1, 0, true, '', true);




		$pdf->SetCellPadding(0);
		$pdf->setCellHeightRatio(0);


		$compensator = 12;
    	

		if( $fields ){
			$fields_left = $fields;
			$fields_right = $fields;
			
			$position = 'sort-left';

			$first_y = 0;
			
			// SORT
			foreach ($fields_left as $index => $field) {
				$key 		= isset($field['key']) ? $field['key'] : false;
				if( !$key ) continue;

				$sort_index = get_post_meta( $post_id, 'sort-'.$key, true );

				if( strpos($sort_index, $position) === false ){
					unset( $fields_left[$index] );
					continue;
				}

				$sort_index = str_replace($position.'-', '', $sort_index);

				if( $sort_index == '' ) $sort_index = $index;

				$fields_left[$index]['sort'] = $sort_index;
			}

			usort($fields_left, array($this,'sortByOrder') );

			foreach ($fields_left as $key => $field) {
				
				$out = $this->get_field_content_2( $post_id, $field, $lang );

				//$pdf->writeHTML($style . $out1, true, false, true, false, '');
				// set color for newt_form_set_background(from, background)d
				//$pdf->SetFillColor(255, 255, 255);
				

				$y = $pdf->getY();
				$y = $y - $compensator;

				if( $key == 0 ) $first_y = $y;

				if( $out ) $pdf->writeHTMLCell(115, 0, 16, $y, $style . $out, 0, 1, 0, true, 'J', true);
			
			}
				


			$pdf->SetY($first_y + $compensator, true);
			$pdf->setPage(1);


			$position = 'sort-right';
			
			// SORT
			foreach ($fields_right as $index => $field) {
				$key 		= isset($field['key']) ? $field['key'] : false;
				if( !$key ) continue;

				$sort_index = get_post_meta( $post_id, 'sort-'.$key, true );

				if( strpos($sort_index, $position) === false  ){
					unset( $fields_right[$index] );
					continue;
				}

				$sort_index = str_replace($position.'-', '', $sort_index);

				if( $sort_index == '' ) $sort_index = $index;

				$fields_right[$index]['sort'] = $sort_index;
			}

			usort($fields_right, array($this,'sortByOrder') );
			
			$i = 1;
			$t = count($fields_right);
			foreach ($fields_right as $key => $field) {
				
				$out = $this->get_field_content_2( $post_id, $field, $lang );

				//$x = $pdf->getX();
				$y = $pdf->getY();

				$y = $y - $compensator;
				
				//$y = $y - 12;
				
				//if( $i == 1 ) $y = $y - 5;
				//if( $key == 5 ) $y = $y - 10;

				if( $out ) $pdf->writeHTMLCell(50, 0, 140, $y, $style . $out, 0, 1, 0, false, 'J', false);

				$i++;
			}
		

		}


		$epdf = $pdf->Output($file_name , 'D');
		//$epdf = $pdf->Output($file_name , 'I');


		//============================================================+
		// END OF FILE
		//============================================================+
		exit;

	}

	
	public function get_field_content_2($post_id, $field, $lang){

		$out1 = '';



		$type 		= isset($field['type']) ? $field['type'] : 'input';
		$name 		= isset($field['name']) ? $field['name'] : 'Field';
		$key 		= isset($field['key']) ? $field['key'] : false;
		$required 	= isset($field['required']) ? $field['required'] : false;
		$placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
		$options 	= isset($field['options']) ? $field['options'] : array();
		$teeny 		= isset($field['teeny']) ? $field['teeny'] : false;
		$class 		= isset($field['class']) ? $field['class'] : array();

    	if( $key == 'position_pdf_job' ) return false;

		$values = get_post_custom( $post_id );

		$value = isset( $values[$key] ) ? $values[$key][0] : '';

		$sort_index = get_post_meta( $post_id, 'sort-'.$key, true );
		
		
		$custom_title = isset( $values[$key.'-custom-title'] ) ? $values[$key.'-custom-title'][0] : '';
		if( $custom_title ) $name = $custom_title;

		$show_title = true;

		if( strpos($key, 'position_custom_text') !== false )
			$show_title = false;

		if( $custom_title != '' )
			$show_title = true;

		if( isset( $values[$key.'-hide-title'] ) && $values[$key.'-hide-title'][0] == 'on' )
			$show_title = false;
			
			
		// Move to next field if we want to hide this field from job offer
		if( isset( $values[$key.'-hide-field'] ) && $values[$key.'-hide-field'][0] == 'on' ) 
			return;

		if( isset( $values[$key.'-field-class'] ) && $values[$key.'-field-class'][0] != '' && strpos($values[$key.'-field-class'][0], 'hide_in_pdf') !== false ) 
			return;
			

		$skip = array('position_apply_now', 'position_pdf_export');
    	if( !in_array($key, $skip) ){

			if( $value || $key == 'position_logo' || $type == 'location' ){
				$out1 .= '<div>';
				
					$fs = '';
					if( $key == 'position_title' ){
						$fs = 'font-size: 18px;';
					}

					$title = '';
					if( $show_title && $key != 'position_title' ){
						$title = '<b style="line-height:15px; font-size: 12px;">'. $name . '</b><br>';
					}

					$out1 .= '<div style="line-height:15px; '.$fs.'" >';

						$skip = array('position_base_salary', 'position_logo', 'position_job_location', 'position_employment_type');

						//$out1 .= '<p style="line-height:18px;">';
						if( !in_array($key, $skip) ){
							
							$value = isset( $values[$key] ) ? $values[$key][0] : '';	
							$content = apply_filters('the_content', $title.$value);
							$out1 .= $content;//substr($content, 0, 400);
							//$out1 .= $title.$value;//substr($content, 0, 400);
						}

						if( $key == 'position_employment_type' ){
							$value = get_post_meta( $post_id, $key, true );
							$list = array();
							if( is_array($value) && !empty($options) ){
								foreach ($value as $vk => $value_key) {
									if( isset($options[$value_key]) && $value_key != 'OTHER' ) {
										$list[] = $options[$value_key];
									}
								}
								if( isset($value['other_input']) ){
									$list[] = $value['other_input'];
								}
							}else if( !is_array($value) && $value != '' ){
								$list[] = $value;
							}

							$content .= apply_filters('job-postings/format_list', implode(', ', $list), $list);
							$content = apply_filters('the_content', $title.$content);
							$out1 .= $content;
						}
						
						if( $key == 'position_logo' ){
							$hiring_organization_name 	= get_post_meta($post_id, 'position_hiring_organization_name', true);
							$hiring_organization 		= $hiring_organization_name ? $hiring_organization_name : get_option('jobs_hiring_organization'.'_'.$lang);
							if(!$hiring_organization) $hiring_organization = get_option('blogname');
							
							$content = apply_filters('the_content', $title.$hiring_organization);
							$out1 .= $content;//substr($content, 0, 400);
							//$out1 .= $title.$value;//substr($content, 0, 400);
						}

						if( $key == 'position_base_salary' ){

							$out1 .= '<br><div>';
							$out1 .= $title;
							$currency_symbol = get_option( 'jobs_currency_symbol'.'_'.$lang );

							$value 	= isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
		    				$upto 	= isset( $values[$key.'_upto'] ) ? esc_attr( $values[$key.'_upto'][0] ) : '';
							
							if($currency_symbol){
								$currency_position = get_option( 'jobs_currency_position'.'_'.$lang );
								if(!$currency_position) $currency_position = 'before';

								$starting = '';
								$to = '';

								switch ($currency_position) {
									case 'after':
										$out1 .= $starting . $value . ' ' . $currency_symbol;

										if( $upto ) {
											$out1 .= $starting = apply_filters('job-postings/salary-range-separator', '<span> - </span>');
											$out1 .= $title . $to . $upto . ' ' . $currency_symbol;
										}

										break;
									
									default:
										$out1 .= $starting . $currency_symbol . ' ' . $value;

										if( $upto ) {
											$out1 .= $starting = apply_filters('job-postings/salary-range-separator', '<span> - </span>');
											$out1 .= $currency_symbol . ' ' . $to . $upto;
										}
										break;
								}

							}else{
								$out1 .= $value;
							}

							$out1 .= '</div><br>';
						}

						if( $type == 'location' ){
							$city = isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';

							$streetAddress = isset( $values[$key.'_streetAddress'] ) ? esc_attr( $values[$key.'_streetAddress'][0] ) : '';
							$postalCode = isset( $values[$key.'_postalCode'] ) ? esc_attr( $values[$key.'_postalCode'][0] ) : '';
							// $addressLocality = (isset( $values[$key.'_addressLocality'] ) && $values[$key.'_addressLocality'][0] != '') ? esc_attr( $values[$key.'_addressLocality'][0] ) : '';
							$addressRegion = (isset( $values[$key.'_addressRegion'] ) && $values[$key.'_addressRegion'][0] != '') ? esc_attr( $values[$key.'_addressRegion'][0] ) : '';
							$addressCountry = (isset( $values[$key.'_addressCountry'] ) && $values[$key.'_addressCountry'][0] != '') ? esc_attr( $values[$key.'_addressCountry'][0] ) : '';

							$remote = isset( $values[$key.'_remote'] ) ? $values[$key.'_remote'][0] : '';

							$remote_data = isset( $values['job_remote_data'] ) ? $values['job_remote_data'][0] : '';


							$full_address = array();

							if( $streetAddress )
								$full_address[] = $streetAddress;

							if( $postalCode )
								$full_address[] = $postalCode;

							if( $city )
								$full_address[] = $city;

							// if( $addressLocality )
							//     $full_address[] = $addressLocality;

							if( $addressRegion )
								$full_address[] = $addressRegion;

							if( $addressCountry )
								$full_address[] = $addressCountry;


							$content = '';
							if($full_address) {
								$content .= implode(', ', $full_address) . '<br>';
							}

							if($remote_data){
								$remote_data = unserialize($remote_data);
							}

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

								$content .= $remote_from . $remote_word_places;
							}else if( $remote == 'on' ){
								$content .= $remote_possible;
							}

							if( $content ){
								$out1 .= apply_filters('the_content', $title.$content);
							}else{
								return false;
							}
						}

						//$out1 .= '</p>';
					
					$out1 .= '</div>';
				$out1 .= '</div>';
			} // if value

			if( $key == 'position_date_posted' ){
				$out1 .= '<div>';

					$job_date = get_the_date( get_option('date_format'), $post_id );

					$name = apply_filters('jp-apply-date-posted', _x('Date posted', 'apply-now', 'job-postings') );
					if( $custom_title ) $name = $custom_title;

					$title = '';
			    	if( $show_title ){
						$title = '<b style="line-height:15px; font-size: 12px;">'. $name . '</b><br>';
					}

			    	$out1 .= '<div style="line-height:15px;">';
			    		$out1 .= apply_filters('the_content', $title . $job_date);
		    		$out1 .= '</div>';
				$out1 .= '</div>';
	    	}

		}



	

		return $out1;
	}
	
	
	public function sortByOrder($a, $b) {
	    return $a['sort'] - $b['sort'];
	}

} // Class

?>