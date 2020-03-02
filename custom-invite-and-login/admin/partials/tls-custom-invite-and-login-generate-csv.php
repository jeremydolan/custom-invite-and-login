<?php
class CSVExport
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (isset($_POST['invitation_based_registrations_download_csv']))
        {
            $csv = $this->generate_csv();

            // headers make the difference between browser
            // printing echo on screen amd downloading as csv.
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"report.csv\";");
            header("Content-Transfer-Encoding: binary");
            echo $csv;
            exit;
        }
    }


    /**
     * Converting data to CSV
     */
    public function generate_csv()
    {

  		  global $wpdb;

        $csv_output = 'Firstname,Lastname,Email,Title,Salutation,Company,Job Title,Gender,DOB,Street,Street 2,City,State,Zip/Postal Code,Country,Phone,Mobile,Fax,Updated,Created,Opt Out,Opt Out Level,Undeliverable,Plain Text,Email Sent,Email Score,Email Views,External ID,Groups,Source,URL'
        . "\n"; // quoted in doubles to be parsed as new line instead of string

    		// get results from wp_options about all Invitees - purge function addition?
    		$results = $wpdb->get_results( "
    		SELECT * FROM {$wpdb->prefix}options
    		WHERE option_name LIKE '%invbr_%'
    		AND option_name NOT LIKE 'invbr_email_subject'
    		AND option_name NOT LIKE 'invbr_smtp_from_name'
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

  				foreach($results as $invitee) {

            $email = explode('|', sanitize_text_field($invitee->option_value));

            // absolute login url prefix
            $login_url = home_url('/' . get_option("invbr_register_login_page") ? get_option("invbr_register_login_page") : 'log-in' . '/');

            // build absolute link
            $hashed_link = $login_url . '?' . trim(sanitize_text_field(str_replace('invbr_', 'invbractiveuser=', $invitee->option_name)));

            $csv_output .=
              trim($email[0]) . ',' . // Firstname
              trim($email[1]) . ',' . // Lastname
              trim($email[2]) . ',' . // Email
              ',' . // Title
              $hashed_link . ',' . // Salutation
              ',' . // Company
              ',' . // Job Title
              ',' . // Gender
              ',' . // DOB
              ',' . // Street
              ',' . // Street 2
              ',' . // City
              ',' . // State
              ',' . // Zip/Postal Code
              ',' . // Country
              ',' . // Phone
              ',' . // Mobile
              ',' . // Fax
              ',' . // Updated
              ',' . // Created
              ',' . // Opt Out
              ',' . // Opt Out Level
              ',' . // Undeliverable
              ',' . // Plain Text
              ',' . // Email Sent
              ',' . // Email Score
              ',' . // Email Views
              ',' . // External ID
              ',' . // Groups,Source
              $hashed_link . // ',' . // URL custom field
              "\n"; // quoted in doubles to be parsed as new line instead of string
  				}

        }

        return $csv_output;
    }
}

// Instantiate a singleton of this plugin
$csvExport = new CSVExport();
