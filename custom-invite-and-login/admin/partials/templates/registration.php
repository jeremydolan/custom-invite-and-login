<?php
// exit if accessed directly
if ( ! defined( 'WPINC' ) )  die;

// already checked if there's a query string
// and that it matches as the conditional in page template.
if(isset($_GET['invbractiveuser'])) {

      $inviteid = $_GET['invbractiveuser'];
      if(get_option("invbr_".$inviteid)){
        $issuccess = false;
        $wperror = $username = $first_name = $last_name = $password = "";
        if(isset($_POST['invbr_register'])){
          if(isset($_POST['pwd'])) $password = sanitize_text_field($_POST['pwd']);

          // function validates password according to Law Society reqs.
          function isValidPassword($string) {
            // match lowercase
          	if(preg_match("/[a-z]/", $string)===0) {
          		return false;
          	}
            // match uppercase
          	if(preg_match("/[A-Z]/", $string)===0) {
          		return false;
          	}
            // match number
          	if(preg_match("/[0-9]/", $string)===0) {
          		return false;
          	}
            // match length
            if(strlen($string) < 8) {
          		return false;
          	}
          	return true;
          }

          if(isValidPassword($password)) {

            // get_option("invbr_".$inviteid) is a string Firstname | Lastname | email@email.com

            // break string into array pieces
            $email_string = explode('|', sanitize_text_field(get_option("invbr_".$inviteid)));

            // allocate appropriately
            $first_name = trim($email_string[0]);
            $last_name = trim($email_string[1]);
            $email = trim($email_string[2]);

            // all good? then we'll add the user to WordPress
            $userdata = array(
              'user_login'  =>  $email,
              'user_email' =>   $email,
              'user_pass'   =>  $password,
              'nickname' => $first_name,
              'display_name' => $first_name." ".$last_name,
              'first_name' => $first_name,
              'last_name' => $last_name
            );

            $user_id = wp_insert_user( $userdata ) ;

            if ( ! is_wp_error( $user_id ) ) {

              $issuccess = true;

            }
            else {
              $wperror = $user_id->get_error_message();
            }
          }
          else {
            $wperror = 'Please ensure you follow the valid password criteria.';
          }

        }
      }
      else {
         wp_die("No Invitation request found for your ID. Please contact your administrator.");
      }


      // define variables for form content population
      // fixme: more configurations option edits - most of these aren't currently in use

      // Form title
      $invbr_registration_header = get_option("invbr_registration_header") ? get_option("invbr_registration_header") : "Beta access invitation";

      //
      $invbr_registration_username = get_option("invbr_registration_username") ? get_option("invbr_registration_username") : "Username";

      $invbr_registration_email = get_option("invbr_registration_email") ? get_option("invbr_registration_email") : "Email Address";

      $invbr_registration_password = get_option("invbr_registration_password") ? get_option("invbr_registration_password") : "Password";

      $invbr_registration_first_name = get_option("invbr_registration_first_name") ? get_option("invbr_registration_first_name") : "First Name";

      $invbr_registration_last_name = get_option("invbr_registration_last_name") ? get_option("invbr_registration_last_name") : "Last Name";

      $invbr_registration_submit = get_option("invbr_registration_submit") ? get_option("invbr_registration_submit") : "Get Access";
      $login_page = get_option("invbr_register_login_page") ? get_option("invbr_register_login_page") : 'authorized-only';

      // if no errors, add rediect link and message to login.
  		if(!empty($wperror)){ echo "<span style=color:red>".$wperror."</a><br>"; $wperror = "";}
  		else if($issuccess){

        // creds created above, remembering user
        $creds = array(
            'user_login'    => $email,
            'user_password' => $password,
            'remember'      => true
        );

        // login, and setting ssl to true
        if(wp_signon( $creds, is_ssl() )) {
          wp_redirect( home_url( '/' ) );
          exit;
        }
        else {
          echo 'something wrong with signon redirect';
        }

  			$register_redirect_url = get_option("invbr_register_redirect_url");
  			if(!empty($register_redirect_url)) {
  				echo '<script>window.location.href = "'.$register_redirect_url.'";</script>';
  				exit;
  			}

  			echo "<p class='registration-success-message'>
          <a href='" . trailingslashit(site_url()) . $login_page . "'>Registration successful, please continue to site login here
            <svg class='icon' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'><use x='0' y='0' xlink:href='#icon-arrow-long' /></svg>
            </a>
          </p>";

        // Add class to body to hide log in form
        echo '<script>jQuery("body").addClass("reg-success");</script>';

        // obscure rest of page template.
        // exit;
  		}
      ?>
      <p class="login-instruction">To access the Law Society Learning Beta, create a password for <strong><?php echo get_option("invbr_".$inviteid);?></strong></p>

      <form name="loginform" id="loginform" action="" method="post" onsubmit="if(document.getElementById('agree').checked) { return true; } else { alert('Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy'); return false; }">
        <!-- <h5><?php echo $invbr_registration_header;?></h5> -->
        <div class="login-password">
          <label for="user_pass"><?php echo $invbr_registration_password;?>
            <a class="password-hide" href="#">
              <svg class="icon svg-hide" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-hide" /></svg>
              Hide
            </a>

            <input type="password" name="pwd" id="user_pass" class="input" value="<?php echo (!empty($password)) ? $password : '' ;?>" size="20" />
          </label>
        </div>

        <p class="login-password-instruction">Your password must contain at least</p>
        <ul class="login-password-criteria">
          <li id="lower-case">
            <svg class="icon svg-hide grey" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-grey" /></svg>
            <svg class="icon svg-hide green" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-green" /></svg>
            1 lower case character</li>
          <li id="upper-case">
            <svg class="icon svg-hide grey" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-grey" /></svg>
            <svg class="icon svg-hide green" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-green" /></svg>
            1 upper case character</li>
          <li id="number">
            <svg class="icon svg-hide grey" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-grey" /></svg>
            <svg class="icon svg-hide green" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-green" /></svg>
            1 number</li>
          <li id="eight-characters">
            <svg class="icon svg-hide grey" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-grey" /></svg>
            <svg class="icon svg-hide green" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use x="0" y="0" xlink:href="#icon-tick-green" /></svg>
            8 characters</li>
        </ul>

        <div class="checkbox-container">
          <label class="login-form-agree" for="agree">
            <input type="checkbox" name="checkbox" value="check" id="agree" /> I have read and agree to the <a href="<?php echo get_template_directory_uri(); ?>/legal-notice/">terms and conditions.</a>
            <span class="tls-checkbox"></span>
          </label>
        </div>

        <input type="hidden" name="invbr_register" value="1" />

        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary login-submit" value="<?php echo $invbr_registration_submit;?>" />

      </form>
      <?php

}
