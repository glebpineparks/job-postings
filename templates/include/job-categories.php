<?php
/*
	Categories selector template


	VSRIABLES:

	$class
*/


$args = array(
  	'hide_empty' => $hide_empty,
	'orderby' => 'slug'
);


if( $category ){
	$args['include'] = $category;
	$args['orderby'] = 'include';
}

$args = apply_filters( 'jobs/categories_args', $args );


$jobs_archive_page = get_option('jobs_archive_page_' . jfw_get_lang());
$jobs_page_permalink = get_permalink( $jobs_archive_page );

//var_dump( $multiselect );

$terms = get_terms( 'jobs_category', $args );

if( $terms && !empty($terms) ){

	$categories_multi = false;
	$active_all = '';

	if( isset($_GET['job-category']) && is_array($_GET['job-category']) ){
		$categories_multi = true;
	}else if( isset($_GET['job-category']) && $_GET['job-category'] != '' && $_GET['job-category'] == 'all' ){
		$active_all = 'active';
	}

	$out = '<div class="job-listing-categories '.implode(', ', $class).'">';
		if($multiselect == 'true' ) $out .= '<form action="" method="GET">';
			$all = apply_filters( 'jobs/all', _x('All', 'job-categories', 'job-postings') );
			$out .= '<a href="'.$jobs_page_permalink.'?job-category=all" class="job-category job-category-all '.$active_all.'">'.$all.'</a>';

			foreach ($terms as $key => $term) {
				$active = '';
				$parent = $term->parent;

				if( $categories_multi ){
					if( in_array($term->slug, $_GET['job-category']) ){
						$active = 'checked';
					}
				}else{
					if( isset($_GET['job-category']) && $_GET['job-category'] != '' && $_GET['job-category'] == $term->slug ){
						$active = 'active';
					}
				}

				$count = '';
				if( $show_count ){
					$args = array(
						'post_type' 		=> 'jobs',
						'post_status' 		=> 'publish',
						'posts_per_page' 	=> -1,
						'supress_filters' 	=> false,
						'tax_query' 		=> array(
							array(
								'taxonomy' => 'jobs_category',
								'field'    => 'term_id',
								'terms'    => array( $term->term_id ),
							),
						),
					);

					$args = apply_filters( 'jobs/listing_query', $args );

					$args['posts_per_page'] = -1;

					$count = get_posts( $args );
					//print_r( $count );
					$count = count($count);
					$count = apply_filters('job-postings/category_count', ' <span class="category">('.$count.')</span>');
				}

				$child_class = '';
				if( $parent != 0 ) $child_class = 'job-category-child';

				if($multiselect == 'true' ) {
					$out .= '<label for="job-category-'.$term->slug.'" class="job-category job-category-'.$term->slug.' '.$child_class.' '.$active.'">';

						$out .= '<input id="job-category-'.$term->slug.'" type="checkbox" name="job-category[]" value="'.$term->slug.'" '.$active.'>';
						$out .= '<span>';
							$out .= $term->name.$count;
						$out .= '</span>';
					$out .= '</label>';
				}else{
					$out .= '<a href="'.$jobs_page_permalink.'?job-category='.$term->slug.'" class="job-category job-category-'.$term->slug.' '.$child_class.' '.$active.'">'.$term->name.$count.'</a>';
				}
				

			}
			if($multiselect == 'true' ) $out .= '<button type="submit">Filter</button>';
		if($multiselect == 'true' ) $out .= '</form>';
	$out .= '</div>';


	//
	// NB! No ooutput/write here. We return output ($out) from the function.
	//
}
