<?php


function invitation_based_registrations_register(){

	if(isset($_GET['invbractiveuser'])){

		// moved this functionality to the registration include.
		// this allows us to format an entire page or page sections based on its logic,
		// instead of being forced to use an unstyled form provided by the plugin.

		// $inviteid = $_GET['invbractiveuser'];
		// if(get_option("invbr_".$inviteid)){
		// 	$issuccess = false;
		// 	$wperror = $username = $first_name = $last_name = $password = "";
		// 	if(isset($_POST['invbr_register'])){
		// 		if(isset($_POST['username'])) $username = sanitize_text_field($_POST['username']);
		// 		if(isset($_POST['pwd'])) $password = sanitize_text_field($_POST['pwd']);
		// 		if(isset($_POST['first_name'])) $first_name = sanitize_text_field($_POST['first_name']);
		// 		if(isset($_POST['last_name'])) $last_name = sanitize_text_field($_POST['last_name']);
		// 		$userdata = array(
		// 			'user_login'  =>  $username,
		// 			'user_email' =>   get_option("invbr_".$inviteid),
		// 			'first_name' =>   $first_name,
		// 			'last_name'  =>   $last_name,
		// 			'user_pass'   =>  $password,
		// 			'nickname' => $first_name,
		// 			'display_name' => $first_name." ".$last_name
		// 		);
		// 		$user_id = wp_insert_user( $userdata ) ;
		// 		if ( ! is_wp_error( $user_id ) ) {
		// 			$issuccess = true;
		// 		} else {
		// 			$wperror = $user_id->get_error_message();
		// 		}
		// 	}
		// }
		// else {
		// 	 wp_die("No Invitation request found for your ID. Please contact your administrator.");
		// }
	}
}

?>
