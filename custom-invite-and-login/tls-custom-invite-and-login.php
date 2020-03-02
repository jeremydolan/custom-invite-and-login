<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       TLS Custom Invite & Login
 * Plugin URI:        tls-custom-invite-and-login
 * Description:       Allows registrations for users by sending Invitation Emails.
 * Version:           2.2.81
 * Author:            think-learning
 * Author URI:        https://profiles.wordpress.org/think-learning
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tls-custom-invite-and-login
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'INVITATION_BASED_REGISTRATIONS_VERSION', '1.0.0' );


function activate_invitation_based_registrations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tls-custom-invite-and-login-activator.php';
	Invitation_Based_Registrations_Activator::activate();
}

function deactivate_invitation_based_registrations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tls-custom-invite-and-login-deactivator.php';
	Invitation_Based_Registrations_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_invitation_based_registrations' );
register_deactivation_hook( __FILE__, 'deactivate_invitation_based_registrations' );

require plugin_dir_path( __FILE__ ) . 'includes/class-tls-custom-invite-and-login.php';

function run_invitation_based_registrations() {

	$plugin = new Invitation_Based_Registrations();
	$plugin->run();

}
run_invitation_based_registrations();
