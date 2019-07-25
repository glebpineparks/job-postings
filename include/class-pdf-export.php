<?php  

$language = 'en';

class Jobs_PDF_Header_Footer extends TCPDF {


	//Page header
	public function Header() {

		$this->footerData = $this->getHeaderData();

		// Logo
		//$image_file = K_PATH_IMAGES.'viseca.jpg';
		//$this->Image($image_file, 0, 7, 35, 0, 'JPG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
	}

	// Page footer
	public function Footer() {
		global $language;
		// Position at 15 mm from bottom
		$this->SetY(-15);

		$font_family = apply_filters('job-postings/pdf/font', 'helvetica');

		// Set font
		$this->SetFont($font_family, 'I', 8);

		$blogdescription = get_option('blogdescription');
		$site_url = get_option('home');
		$hiring_organization = get_option('jobs_hiring_organization'.'_'.$language);

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
		global $language;
		//print_r($form);

		$language = $lang;


		// create new PDF document
		$pdf = new Jobs_PDF_Header_Footer(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$company_logo 			= get_option('jobs_company_logo');
		$hiring_organization 	= get_option('jobs_hiring_organization'.'_'.$language);
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


		$font_family = apply_filters('job-postings/pdf/font', 'helvetica');
		
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont($font_family, '', 9, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();


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

		$offer_link = get_permalink( $post_id );
		$sitename = get_option('blogname');
		$sitename = '<b style="font-size: 20px;">'.$sitename.'</b><br>';

		if( $company_logo ) $sitename = '<img src="'.$company_logo.'" height="35"><br>';

		$html = $sitename . '<br><i>'.$offer_link.'<i><br><br><br>';
		//$pdf->writeHTML($style . $html, true, false, true, false, '');
		$pdf->writeHTMLCell(0, 0, 15, 15, $style . $html, 0, 1, 0, true, '', true);




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

				if( strpos($sort_index, $position) === false ){
					unset( $fields_right[$index] );
					continue;
				}

				$sort_index = str_replace($position.'-', '', $sort_index);

				if( $sort_index == '' ) $sort_index = $index;

				$fields_right[$index]['sort'] = $sort_index;
			}

			usort($fields_right, array($this,'sortByOrder') );
			
			$i = 0;
			foreach ($fields_right as $key => $field) {
				
				$out = $this->get_field_content_2( $post_id, $field, $lang );

				//$x = $pdf->getX();
				$y = $pdf->getY();

				//$y = $y - 12;
				$y = $y - $compensator;
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

	public function get_field_content($post_id, $field, $lang){
		$out1 = '<table border="0" cellspacing="0" cellpadding="5">';



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
			

		$skip = array('position_apply_now', 'position_pdf_export');
    	if( !in_array($key, $skip) ){

			if( $value ){

				if( $show_title ){
					$out1 .= '<tr><td class="" >';
						$out1 .= '<b>'. $name . '</b>';
					$out1 .= '</td></tr>';
				}

				$fs = '';
				if( $key == 'position_title' ){
					$fs = 'font-size: 18px;';
				}

				$out1 .= '<tr><td style="line-height:18px; '.$fs.'" >';
					//$out1 .= '<p style="line-height:18px;">';
					if( $key != 'position_base_salary' ){
						
						$value = isset( $values[$key] ) ? $values[$key][0] : '';	
						$content = apply_filters('the_content', $value);
						$out1 .= $content;//substr($content, 0, 400);
					}
					

					if( $key == 'position_base_salary' ){

						$out1 .= '<br><div>';
						$out1 .= $title;
						$currency_symbol = get_option( 'jobs_currency_symbol'.'_'.$lang );

						$value 	= isset( $values[$key] ) ? esc_attr( $values[$key][0] ) : '';
	    				$upto 	= isset( $values[$key.'_upto'] ) ? esc_attr( $values[$key.'_upto'][0] ) : '';
						
						if($currency_symbol){
							$currency_position = get_option( 'jobs_currency_position'.'_'.$this->lang );
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

					//$out1 .= '</p>';
				$out1 .= '<br></td></tr>';
			
			} // if value
		}


		if( $key == 'position_date_posted' ){
			$job_date = get_the_date( get_option('date_format'), $post_id );

			$name = apply_filters('jp-apply-date-posted', _x('Date posted', 'apply-now', 'job-postings') );
			if( $custom_title ) $name = $custom_title;

	    	if( $show_title ){
				$out1 .= '<tr><td class="" >';
					$out1 .= '<b>'. $name . '</b>';
				$out1 .= '</td></tr>';
			}

	    	$out1 .= '<tr><td style="line-height:18px;" >';
	    		$out1 .= apply_filters('the_content', $job_date);
    		$out1 .= '<br></td></tr>';
    	}
	
		$out1 .= '</table>';

		return $out1;
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

		$skip = array('position_apply_now', 'position_pdf_export');
    	if( !in_array($key, $skip) ){

			if( $value ){
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


						//$out1 .= '<p style="line-height:18px;">';
						if( $key != 'position_base_salary' ){
							
							$value = isset( $values[$key] ) ? $values[$key][0] : '';	
							$content = apply_filters('the_content', $title.$value);
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
								$currency_position = get_option( 'jobs_currency_position'.'_'.$this->lang );
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
						$title = '<b style="line-height:14px; font-size: 12px;">'. $name . '</b><br>';
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