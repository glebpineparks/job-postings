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


$terms = get_terms( 'jobs_category', $args );

if( $terms && !empty($terms) ){

	$active_all = '';

	if( isset($_GET['job-category']) && strip_tags($_GET['job-category']) != ''  && strip_tags($_GET['job-category']) == 'all' ){
		$active_all = 'active';
	}

	$out = '<div class="job-listing-categories '.implode(', ', $class).'">';

		$all = apply_filters( 'jobs/all', _x('All', 'job-categories', 'job-postings') );
		$out .= '<a href="'.$jobs_page_permalink.'?job-category=all" class="job-category job-category-all '.$active_all.'">'.$all.'</a>';

		foreach ($terms as $key => $term) {
			$active = '';
			$parent = $term->parent;

			if( isset($_GET['job-category']) && strip_tags($_GET['job-category']) != ''  && strip_tags($_GET['job-category']) == $term->slug ){
				$active = 'active';
			}

			$count = '';
			if( $show_count ){
						$args = array(
							'post_type' => 'jobs',
							'post_status' => 'publish',
							'posts_per_page' => -1,
							'tax_query' => array(
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

			$out .= '<a href="'.$jobs_page_permalink.'?job-category='.$term->slug.'" class="job-category job-category-'.$term->slug.' '.$child_class.' '.$active.'">'.$term->name.$count.'</a>';

		}
	$out .= '</div>';


	//
	// NB! No ooutput/write here. We return output ($out) from the function.
	//
}
