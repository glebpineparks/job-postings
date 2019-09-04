<?php
/*
//Updating default fields
//
//sort-left, sort-right, sort-disabled
//$default_fields['FIELD_KEY']['sort'] = 'sort-disabled';

add_filter( 'job-postings/position_fields', 'my_jobs_default_fields' );
function my_jobs_default_fields( $default_fields ){

	// Disable fields
	$default_fields['position_responsibilities']['sort'] 	=  'sort-disabled';
	$default_fields['position_qualifications']['sort'] 		=  'sort-disabled';

	// Set field in column to index
	$default_fields['position_skills']['sort'] 				= 'sort-left'; // or 'sort-right'
	$default_fields['position_custom_text_1']['sort'] 		= 'sort-left';	// or 'sort-right'

	return $default_fields;
}
*/
?>

<div id="jobs_fields" class="job_tab_content clearfix" style="display: none;">
    

    <h3><?php _e('Default fields', 'job-postings') ?></h3>

    <p class="description jfw_hint"><?php _e('Here you can rearrange fields or disable them.', 'job-postings'); ?></p>
    <p class="description jfw_hint jfw_hint_red"><?php _e('Fields required by Google are marked red. You still can disable them, but consider using them as Google can skip indexing job posting if required field is missing.', 'job-postings'); ?></p>
								

    <?php
        $default_name = 'jobs_default_field_selection';
        $default_fields = get_option($default_name);

        $fields = Job_Postings::$fields;

        // echo '<pre>';
        // print_r($default_fields);
        // echo '</pre>';



        ///////
        switch (Job_Postings::$side_position) {
            case 'left':
                $wide_1         = '';
                $wide_2         = 'wide-side';

                $class_1 		= 'jobs-wrapper-right jobs-settings-sortable-right';
                $class_2 		= 'jobs-wrapper-left jobs-settings-sortable-left';

                $sort_type_1 	= 'sort-right';
                $sort_type_2 	= 'sort-left';
                break;

            default:
                $wide_1         = 'wide-side';
                $wide_2         = '';

                $class_1 		= 'jobs-wrapper-left jobs-settings-sortable-left';
                $class_2 		= 'jobs-wrapper-right jobs-settings-sortable-right';

                $sort_type_1 	= 'sort-left';
                $sort_type_2 	= 'sort-right';
                break;
        }

        echo '<div id="jobs-settings-fields" class="clearfix">';
            echo '<div class="jobs-wrapper-top '.$wide_1.'">';
                echo '<h4>'.__('Content area', 'job-postings').'</h4>';
                echo '<div class="jobs-wrapper jobs_match_height '.$class_1.' connectedSortable" data-sort="'.$sort_type_1.'">';

                
                $fields_1 = JobAddEdit::prepareSortedFields( $fields, $sort_type_1 );

                if( $fields_1 ){

                    foreach($fields_1 as $index => $field){
                        $key = $field['key'];
                        $name = $field['name'];
                        $sort = $field['sort'];
                        $need = isset($field['need']) ? $field['need'] : 0;
                        $class = '';
                        if($need) $class .= 'required_by_google';

                        if( !$key || !$name ) continue;

                        $index_value = $sort_type_1 . '-' . $index;

                        echo '<div class="jobs-settings-field jobs-row '.$class.'">';
                            echo '<div class="jobs-row-label">';
                                echo $name;
                            echo '</div>';
                            echo '<input class="item-sort-value" type="hidden" name="'.$default_name.'['.$key.']" value="'.$index_value.'"/>';
                        echo '</div>';
                    }
                }
                //print_r($fields_1);
                
                echo '</div>';
            echo '</div>';
            

            echo '<div class="jobs-wrapper-top '.$wide_2.'">';
                echo '<h4>'.__('Side area', 'job-postings').'</h4>';

                echo '<div class="jobs-wrapper jobs_match_height '.$class_2.' connectedSortable" data-sort="'.$sort_type_2.'">';

                
                $fields_2 = JobAddEdit::prepareSortedFields( $fields, $sort_type_2 );
                //print_r($fields_2);

                if( $fields_2 ){
                    foreach($fields_2 as $index => $field){
                        $key = $field['key'];
                        $name = $field['name'];
                        $sort = $field['sort'];
                        $need = isset($field['need']) ? $field['need'] : 0;
                        $class = '';
                        if($need) $class .= 'required_by_google';
                        if( !$key || !$name ) continue;

                        $index_value = $sort_type_2 . '-' . $index;

                        echo '<div class="jobs-settings-field jobs-row '.$class.'">';
                            echo '<div class="jobs-row-label">';
                                echo $name;
                            echo '</div>';
                            echo '<input class="item-sort-value" type="hidden" name="'.$default_name.'['.$key.']" value="'.$index_value.'"/>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            echo '</div>';


            echo '<div class="jobs-wrapper-top ">';
                echo '<h4>'.__('Disabled fields', 'job-postings').'</h4>';
                echo '<div class="jobs-wrapper jobs_match_height jobs-wrapper-disabled jobs-settings-sortable-disabled connectedSortable" data-sort="sort-disabled">';


                $fields_3 = JobAddEdit::prepareSortedFields( $fields, 'sort-disabled' );
                //print_r($fields_3);

                if( $fields_3 ){
                    foreach($fields_3 as $index => $field){
                        $key = $field['key'];
                        $name = $field['name'];
                        $sort = $field['sort'];
                        $need = isset($field['need']) ? $field['need'] : 0;
                        $class = '';
                        if($need) $class .= 'required_by_google';
                        if( !$key || !$name ) continue;

                        $index_value = 'sort-disabled-' . $index;

                        echo '<div class="jobs-settings-field jobs-row '.$class.'">';
                            echo '<div class="jobs-row-label">';
                                echo $name;
                            echo '</div>';
                            echo '<input class="item-sort-value" type="hidden" name="'.$default_name.'['.$key.']" value="'.$index_value.'"/>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            echo '</div>';
        echo '</div>';
    ?>
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e( 'Save default fields', 'job-postings' ); ?>" />
    </p>
</div>