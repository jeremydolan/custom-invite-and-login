<?php


	function invitation_based_registrations_invite(){
		echo '<div>';
			if(isset($_POST['invitation_based_registrations_send_invite']) && isset($_POST['emails'])){

				// set the headers for the invitation email - default invite method.
				$subject = get_option("invbr_email_subject");
				$emailbody = get_option("invbr_email_body");
				$emailbody = str_replace(PHP_EOL, "<br>", $emailbody);
				$from_name = get_option("invbr_smtp_from_name");
				$from_email = get_option("invbr_smtp_from_email");

				if(!empty($from_name) && !empty($from_email)) {
					$headers = array('From: '.$from_name.' <'.$from_email.'>', 'Content-Type: text/html; charset=UTF-8');
				} else
					$headers = array('Content-Type: text/html; charset=UTF-8');


				// isolate individual lines from the input text area
				$emailsarray = preg_split('/\r\n|[\r\n]/', $_POST['emails']);

				if(sizeof($emailsarray)==0) {
					echo "<span style='color:red'>No email address provided.</span><br><br>";
				} else {
					foreach($emailsarray as $email_string) {

						// break email string into constituent parts
						$email = explode('|', sanitize_text_field($email_string));

						// the number of the counting shall be 3
						$count = count($email);
						if( $count < 3) {
							echo "<div style='max-width:60%;color:black;margin:4px;background:#ffc3c3;padding: 5px 20px;border: 1px solid #ff8c8c;'><b>Too few params for $email_string: Expected input = 'Firstname | Lastname | email@email.com'</b></div>";
							continue;
						}
						else if( $count > 3) {
							echo "<div style='max-width:60%;color:black;margin:4px;background:#ffc3c3;padding: 5px 20px;border: 1px solid #ff8c8c;'><b>Too many params for $email_string: Expected input = 'Firstname | Lastname | email@email.com'</b></div>";
							continue;
						}

						// 3rd item in the array should be the email.
						$email = trim($email[2]);


						if(empty($email))
							continue;

						// check for whitespace
						else if ( preg_match('/\s/',$email) ) {
							echo "<div style='max-width:60%;color:black;margin:4px;background:#ffc3c3;padding: 5px 20px;border: 1px solid #ff8c8c;'><b>".$email." contains whitespace.: Expected input = 'Firstname | Lastname | email@email.com'</b></div>";
							continue;
						}
						// check if there's a user with this email on the DB already
						else if(email_exists( $email )) {
							echo "<div style='max-width:60%;color:black;margin:4px;background:#ffc3c3;padding: 5px 20px;border: 1px solid #ff8c8c;'>User with email <b>".$email." already exists.</b></div>";
							continue;
						}

						$invitationkey = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32/strlen($x)) )),1,32);

						// save email_string and query string pair to DB
						update_option("invbr_".$invitationkey, $email_string);
						// echo $invitationkey . ' ' . $email_string;

						$emailbodyinner = str_replace("##INVITE_LINK#", site_url().'/log-in/?invbractiveuser='.$invitationkey ,$emailbody);
						$emailbodyinner = str_replace("##FROM_EMAIL##", $from_email, $emailbodyinner);
						$emailbodyinner = str_replace("##FROM_NAME##", $from_name, $emailbodyinner);

						$subject = str_replace("##FROM_EMAIL##", $from_email, $subject);
						$subject = str_replace("##FROM_NAME##", $from_name, $subject);

						// send email to invitee
						wp_mail( $email, $subject, $emailbodyinner, $headers );

						echo "<div style='max-width:60%;color:black;margin:4px;background: #cdfbaa;padding: 5px 20px;border: 1px solid #b8d6a1;'>Invitation has been sent to <b>".$email."</b> ".$email."'s' url will be ?invbractiveuser=$invitationkey</div>";

						// update invitee line in wp_options table ...? why is this necessary?
						update_option("invbr_".$invitationkey, $email_string);
					}
					echo "<br>";
				}

			}

		invitation_based_registrations_send_invite();

		// list and delete
		invitation_based_registrations_manage();

		// download csv button
		invitation_based_registrations_dowload_csv();

		echo '</div>';

	}


	function invitation_based_registrations_send_invite(){

		echo '<div style="margin:4px;padding:10px 40px;max-width:850px">

		<h2>Invite Users to Register</h2>';
		$subject = 'Invitation to create account | '.get_bloginfo();
		if(get_option("invbr_email_subject"))
			$subject = get_option("invbr_email_subject");
		$emailbody = 'Hey there,&#10;&#10;You have been invited to create account with '.get_bloginfo().'. Click <a href="##INVITE_LINK#">this link</a> to complete registration.&#10;&#10;Regards,&#10;'.get_bloginfo();
		if(get_option("invbr_email_body")) {
			$emailbody = get_option("invbr_email_body");
		}
		$emailbody = str_replace(PHP_EOL, "<br>", $emailbody);

		echo '<form  method="POST" action="">
			<input type="hidden" name="invitation_based_registrations_send_invite" value="1"/>
			<font style="color:#f00">* </font>Expected input = \'Firstname | Lastname | email@email.com\'<br/>
			Paste email ID\'s to invite </b> (each on new line) : <br>
			<textarea rows="8" cols="120" required="true"  name="emails"  placeholder="List of Firstname | Lastname | email@email.com, each on new line" value="" style="min-width:250px"></textarea><br><br>
			<input type="submit" class="button button-primary button-large" value="Send Invitation">

			</form>
		</div>';

	}

	function invitation_based_registrations_manage(){

		global $wpdb;

		if(isset($_POST['invitation_based_registrations_delete_invitee'])){
			if(isset($_POST['delete_strings'])) {
				$delete_strings = $_POST['delete_strings'];
				foreach ($delete_strings as $key => $value) {
					delete_option( $key );
				}
			}
		}
		// container div
		echo '<div style="margin:4px;padding:10px 40px;max-width:850px;">';

		// get results from wp_options about all Invitees - purge function addition?
		$results = $wpdb->get_results( "
		SELECT * FROM {$wpdb->prefix}options
		WHERE option_name LIKE '%invbr_%'
		AND option_name NOT LIKE 'invbr_email_subject'
		AND option_name NOT LIKE 'invbr_smtp_from_name'
		AND option_name NOT LIKE 'invbr_smtp_from_email'
		AND option_name NOT LIKE 'invbr_smtp_host'
		AND option_name NOT LIKE 'invbr_smtp_port'
		AND option_name NOT LIKE 'invbr_smtp_username'
		AND option_name NOT LIKE 'invbr_smtp_password'
		AND option_name NOT LIKE 'invbr_register_redirect_url'
		AND option_name NOT LIKE 'invbr_register_login_page'
		AND option_name NOT LIKE 'invbr_email_body'
		AND option_name NOT LIKE 'invbr_publically_viewable_pages'
		", OBJECT );

		if($results) {

			echo '<form  method="POST" action="" style="border:1px solid grey;padding:0.5rem;margin-top:2rem;border-radius:3px;">';
			echo '<h3>Invitees, so far:</h3>';
			echo '<table cellpadding="5"><tr><th>Email</th><th>Invite string</th></tr>';
				foreach($results as $invitee) {
					echo '<tr><td> ' . $invitee->option_value . '</td>';
					echo '<td> ?' . str_replace('invbr_', 'invbractiveuser=', $invitee->option_name) . '</td>';
					echo '<td><input type="checkbox" name="delete_strings[' . $invitee->option_name . ']"/></td></tr>';
				}
			echo '</table><br>';
			echo '<input type="hidden" name="invitation_based_registrations_delete_invitee" value="1"/>';
			echo '<input type="submit" class="button button-primary button-large" value="Delete Checked">
						</form>';
		}

		// close container div
		echo '</div>';

	}


	function invitation_based_registrations_dowload_csv(){

		global $wpdb;

		// container div
		echo '<div style="margin:4px;padding:10px 40px;max-width:850px;">';

		//
		echo '<form  method="POST" action="" style="border:1px solid grey;padding:0.5rem;margin-top:2rem;border-radius:3px;">
			<input type="hidden" name="invitation_based_registrations_download_csv" value="1"/>
			<h3><font style="color:#f00">* </font>Download CSV. Custom format for the Law Society LMS Beta launch.</h3><br><br>
			<input type="submit" class="button button-primary button-large" value="Download CSV">
			</form>';

		// close container div
		echo '</div>';

	}

?>
