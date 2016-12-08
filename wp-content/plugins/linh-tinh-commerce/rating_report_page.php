<?php

// add bootstrap for wp_admin
function sinh_add_bootstrap() {
	if ( $_GET['page'] == 'user-rating-report' ) {
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">\n
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>\n
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
	}
 }

 add_action( 'admin_head', 'sinh_add_bootstrap' );

// create menu for rating report
function sinh_rating_report_setup_menu() {
	add_menu_page( 'User Rating Report', 'User Ratings', 'manage_options', 'user-rating-report', 'sinh_rating_report_page_init', 'dashicons-groups', 70 );
}
function sinh_rating_report_page_init() {
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$start_date_of_this_year = mktime(0, 0, 0, date("m"), 1, date("Y"));

	echo '<h1>User Rating Report</h1>';
	echo '<form action="' . $actual_link . '" method="GET" name="sinh-user-rating-form" class="form-inline">
		  <input name="page" type="hidden" value="user-rating-report" />
		  <div class="form-group">
		    <label for="from-date">From Date : </label>
		    <input name="from-date" id="from-date" type="date" value="' . date("Y-m-d", $start_date_of_this_year) . '"/>
		  </div>
		  <div class="form-group">
		    <label for="to-date">To Date : </label>
		    <input name="to-date" id="to-date" type="date" value="' . date("Y-m-d") . '"/>
		  </div>
		  <button type="submit" class="btn btn-primary">Statistics</button>
		</form>';
	sinh_get_user_ratings_list();
	/*if (function_exists('get_lowest_rated')):
	get_lowest_rated();
	endif;*/
}

add_action('admin_menu', 'sinh_rating_report_setup_menu');

function sinh_get_user_ratings_list() {
	$posts = query_posts( array( 'meta_key' => 'ratings_average', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );
	echo '<table class="table table-bordered">
	    <thead>
	      <tr>
	        <th class="col-lg-1">No.</th>
	        <th class="col-lg-5">Name</th>
	        <th class="col-lg-2">Score</th>
	        <th class="col-lg-2">Votes</th>
	        <th class="col-lg-2">Average</th>
	      </tr>
	    </thead>
	    <tbody>';
	$count = 1;
	// The Loop
	while ( have_posts() ) : the_post();
	    $post_meta = get_post_meta( get_the_ID() );
	    echo '<tr>
    		<td class="col-lg-1">' . $count . '</td>
	        <td class="col-lg-5">
	        	<a target="_blank" href="' . admin_url('user-edit.php?user_id=' . get_the_author_id() ) . '">' 
	        	  . get_the_author() 
	        	. '</a>
	        </td>
	        <td class="col-lg-2">' . $post_meta['ratings_score'][0] . '/5</td>
	        <td class="col-lg-2">by ' . number_format($post_meta['ratings_users'][0]) . ' users</td>
	        <td class="col-lg-2">' . $post_meta['ratings_average'][0] . '</td>
    	</tr>';
	    $count++;
	endwhile;

	echo '</tbody></table>';
	 
	// Reset Query
	wp_reset_query();
} 

?>