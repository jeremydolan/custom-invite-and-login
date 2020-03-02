<?php

	function invitation_based_registrations_invite_login_theming(){
		echo '<div>';
			if(isset($_POST['invitation_based_registrations_invite_login_theming'])){

				$login_page = "";
				if(isset($_POST['login_page'])) $login_page = sanitize_text_field($_POST['login_page']);
				update_option('invbr_register_login_page', $login_page);

				$publically_viewable_pages = "";
				if(isset($_POST['publically_viewable_pages'])) $publically_viewable_pages = $_POST['publically_viewable_pages'];

			  // add to $publically_viewable_pages array
			  $publically_viewable_pages[] =  basename(wc_lostpassword_url());

				// save to options table
				update_option('invbr_publically_viewable_pages', $publically_viewable_pages);

				echo "<div style='max-width:60%;color:black;margin:4px;background: #cdfbaa;padding: 5px 20px;border: 1px solid #b8d6a1;'>Login Theming settings has been updated.</b></div>";
			}

		invitation_based_registrations_invite_login_theming_view();
		echo '</div>';

	}


	function invitation_based_registrations_invite_login_theming_view() {

		$login_page = '';
		if(get_option("invbr_register_login_page")) {
			$login_page = get_option("invbr_register_login_page");
		}

		$publically_viewable_pages = array();
		if(get_option("invbr_publically_viewable_pages")) {
			$publically_viewable_pages = get_option("invbr_publically_viewable_pages");
		}
		// $pages = get_pages($args);
		// var_dump( $pages );


		echo '<div style="margin:4px;padding:10px 40px;max-width:850px">
		<h3><br>Login Theming</h3>
		<p>Assumes you will choose a page using Template Name: Authorised. <br/>Uses WordPress slug as identifier. <br/>Login page data is not saved as an object. <br/>If page slugs are changed, please re-edit these settings to match.</p>
		<hr>

		<form  method="POST" action="">
		<input type="hidden" name="invitation_based_registrations_invite_login_theming" value="1"/>';
		// <input type=text  name="login_page"  placeholder="Login Page" value="'.$login_page.'" style="min-width:750px;padding:5px;">


		$args = array(
			'post_status' => 'publish'
		)

		?>
		<b>Login Page</b> ( Select which page to use as themed login.  )<br><br>

		<label style="clear:left;min-width:250px;display:block;">
			<select name="login_page">
			 <option value="<?php echo $login_page ;?>">
			 <?php echo $login_page ;?></option>
			 <?php
			  $pages = get_pages($args);
			  foreach ( $pages as $page ) {
			  	$option = '<option value="' . $page->post_name . '">';
				$option .= $page->post_name;
				$option .= '</option>';
				echo $option;
			  }
			 ?>
			</select>
		</label>

		<br><br>
		<b>Publically Accessible Pages</b> ( Select which pages can be viewed before login )<br><br>
		<?php

		foreach($pages as $page) {

			//
			?>
			<label style="float:left;width:33%;min-width:250px;">
				<input type="checkbox" name="publically_viewable_pages[]" value="<?php echo trim($page->post_name); ?>" <?php echo (in_array($page->post_name, $publically_viewable_pages)) ? 'checked' : '' ;?>> <?php echo $page->post_name; ?><br>
			</label>
			<?php
		}

		echo '
		<hr style="clear:left;position:relative;top:2rem;"><br><br>
		<br><input type="submit" class="button button-primary button-large" value="Save Settings" style="clear:left;display:block;"></form></div>';
	}


?>
