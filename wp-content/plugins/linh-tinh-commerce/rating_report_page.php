<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// add bootstrap for wp_admin
function sinh_add_bootstrap() {
    if ($_GET['page'] == 'user-rating-report') {
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">\n
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>\n
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
    }
}

function bao_add_disable_user_handler() {
    echo
    '<script>
		$(document).ready(function(){
			$(".checkbox_disable_user").change(function() {
				var id = this.value;
				if(this.checked) {
					var message = prompt("Let the user know why him/her is blocked:",
					"low rating from many others user");
					if (message != null) {
						window.location.href = "./user-disable.php?id=" + id + "&message="+message+"&disable=true"
					} else {
						this.checked = !this.checked;
					}
				} else {
					window.location.href = "./user-disable.php?id=" + id + "&message="+ " " +"&disable=false"
				}
			})
		})
//		$("").load("user-disable.php/?id=" + id + "&message="+message+"&disable=true");
//		$("").load("user-disable.php/?id=" + id + "&message="+ " " + "&disable=false");
	</script>';
}

add_action('admin_head', 'sinh_add_bootstrap');
add_action('admin_head', 'bao_add_disable_user_handler');

// create menu for rating report
function sinh_rating_report_setup_menu() {
    add_menu_page('User Rating Report', 'User Ratings', 'manage_options', 'user-rating-report', 'sinh_rating_report_page_init', 'dashicons-groups', 70);
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
    $posts = query_posts(array('meta_key' => 'ratings_average', 'orderby' => 'meta_value_num', 'order' => 'ASC'));
    echo '<table class="table">
	    <thead>
	      <tr>
	        <th>No.</th>
	        <th>Name</th>
	        <th>Score</th>
	        <th>Votes</th>
	        <th>Average</th>
			<th>Disable</th>
			<th>Disabled Message</th>
	      </tr>
	    </thead>
	    <tbody>';
    $count = 1;
    // The Loop
    while (have_posts()) : the_post();
        $post_meta = get_post_meta(get_the_ID());
        $disabled = get_user_meta(get_the_author_meta('ID'), 'ja_disable_user', true);
        echo '<tr>
    		<td>' . $count . '</td>
	        <td>
	        	<a target="_blank" href="' . admin_url('user-edit.php?user_id=' . get_the_author_meta('ID')) . '">'
            . get_the_author()
            . '</a>
	        </td>
	        <td>' . $post_meta['ratings_score'][0] . '/5</td>
	        <td>by ' . number_format($post_meta['ratings_users'][0]) . ' users</td>
	        <td>' . $post_meta['ratings_average'][0] . '</td>';
        if ($disabled == 0) {
            echo '<td> <input type="checkbox" class="checkbox_disable_user" name="disabled" value="' . get_the_author_meta('ID') . '"/>';
        } else {
            echo '<td> <input type="checkbox" class="checkbox_disable_user" name="disabled" value="' . get_the_author_meta('ID') . '" checked/>';
            echo '<td> ' . get_user_meta(get_the_author_meta('ID'), 'ja_disable_user_des', true) . '</td>';
        }

        echo '</tr>';
        $count++;
    endwhile;

    echo '</tbody></table>';

    // Reset Query
    wp_reset_query();
}

?>