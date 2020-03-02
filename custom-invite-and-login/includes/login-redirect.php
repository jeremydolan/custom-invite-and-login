<?php
function authorisation_check_redirect() {

  // determine what the current url is
  $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  // array of publically viewable pages
  $publically_viewable_pages = get_option("invbr_publically_viewable_pages") ? get_option("invbr_publically_viewable_pages") : array(
    'log-in'
  );

  // assigned login page
  $login_page = get_option("invbr_register_login_page") ? get_option("invbr_register_login_page") : 'log-in';

  // is the user logged in?
  if ( is_user_logged_in() ) {

    // no gatekeeping necessary

  }
  else {

    // echo 'current url: ' . $current_url;

    if( $current_url != ( home_url('/' . $login_page . '/') ) ) {

       // echo current page url for comparison
       // echo 'current url = ' . $current_url . '<br/>' ;

       // is this a publically viewable page?
       if ( in_array ( basename($current_url), $publically_viewable_pages)) {

         // echo 'publically viewable current url = ' . $current_url . '<br/>' ;

       }

       // coming from an invite email with query string addition?
       elseif( isset( $_GET['invbractiveuser'] ) ) {

         $inviteid = $_GET['invbractiveuser'];

       }
       else {

         // echo 'going to redirect to: ' . home_url('/' . $login_page . '/');

         // exit and redirect
         exit(wp_redirect(home_url('/' . $login_page . '/')));

       }
    }
  }
}
// add_action('template_redirect','authorisation_check');
// suspect template_redirect hook to be responsible for 'unexpected output' of cookies
add_action('template_redirect','authorisation_check_redirect');

/* Main redirection of the default login page */
function redirect_login_page() {
	$login_page  = get_option("invbr_register_login_page") ? home_url('/' . get_option("invbr_register_login_page") . '/') : 'log-in';
	$page_viewed = basename($_SERVER['REQUEST_URI']);

	if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
		wp_redirect($login_page);
		exit;
	}
}
add_action('init','redirect_login_page');

/* Where to go if a login failed */
function custom_login_failed() {
	$login_page  = get_option("invbr_register_login_page") ? home_url('/' . get_option("invbr_register_login_page") . '/') : 'log-in';
  // echo 'custom login fail - login page var: ' . $login_page;
	wp_redirect($login_page);
	exit;
}
add_action('wp_login_failed', 'custom_login_failed');

/* Where to go if any of the fields were empty */
function verify_user_pass($user, $username, $password) {
	$login_page  = get_option("invbr_register_login_page") ? home_url('/' . get_option("invbr_register_login_page") . '/') : 'log-in';
	if($username == "" || $password == "") {
    // echo 'fields empty - login page var: ' . $login_page;
		wp_redirect($login_page . "?login=empty");
		exit;
	}
}
add_filter('authenticate', 'verify_user_pass', 1, 3);

/* What to do on logout */
// function logout_redirect() {
// 	$login_page  = get_option("invbr_register_login_page") ? home_url('/' . get_option("invbr_register_login_page") . '/') : 'log-in';
//   echo 'logout - login page var: ' . $login_page;
// 	wp_redirect($login_page . "?action=logout");
// 	exit;
// }
// add_action('wp_logout','logout_redirect');

function auto_redirect_external_after_logout(){
  wp_redirect( get_option("invbr_register_login_page") ? home_url('/' . get_option("invbr_register_login_page") . '/') : 'log-in' );
  exit();
}
add_action( 'wp_logout', 'auto_redirect_external_after_logout');
