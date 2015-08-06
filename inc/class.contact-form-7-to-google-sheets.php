<?php
/*
 * Title                   : Contact Form 7 to Google Sheets
 * Version                 : 1.0
 * File                    : inc/class.contact-form-7-to-google-sheets.php
 * File Version            : 1.0.0
 * Created / Last Modified : 26 June 2015
 * Author                  : Danny Connolly
 * Copyright               : © 2015 Danny Connolly
 * Website                 : http://www.dannyconnolly.me
 * Description             : Contact Form 7 to Google Sheets.
 */

if (!class_exists('CFGS')) {

    class CFGS {

        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        /**
         * Start up
         */
        public function __construct() {
            add_action('admin_menu', array($this, 'add_plugin_page'));
            add_action('admin_init', array($this, 'page_init'));

            add_action("wpcf7_before_send_mail", array($this, 'save_to_sheet'));
        }

        /**
         * Add options page
         */
        public function add_plugin_page() {
            // This page will be under "Settings"
            add_options_page(
                    'Settings Admin', 'Contact Form 7 to Google Sheets', 'manage_options', 'contact-form-7-to-google-sheets', array($this, 'create_admin_page')
            );
        }

        /**
         * Options page callback
         */
        public function create_admin_page() {
            // Set class property
            $this->options = get_option('cfgs_api_credentials');
            ?>
            <div class="wrap">
                <h2>Contact Form 7 to Google Sheets</h2>           
                <form method="post" action="options.php">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields('cfgs_api_credentials_group');
                    do_settings_sections('contact-form-7-to-google-sheets');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        public function page_init() {
            register_setting(
                    'cfgs_api_credentials_group', // Option group
                    'cfgs_api_credentials', // Option name
                    array($this, 'sanitize') // Sanitize
            );

            add_settings_section(
                    'cfgs_setting_section_id', // ID
                    'Your Google API Credentials', // Title
                    array($this, 'print_section_info'), // Callback
                    'contact-form-7-to-google-sheets' // Page
            );

            add_settings_field(
                    'client_id', // ID
                    'Client ID', // Title 
                    array($this, 'client_id_callback'), // Callback
                    'contact-form-7-to-google-sheets', // Page
                    'cfgs_setting_section_id' // Section           
            );

            add_settings_field(
                    'email_address', // ID
                    'Email address', // Title 
                    array($this, 'email_address_callback'), // Callback
                    'contact-form-7-to-google-sheets', // Page
                    'cfgs_setting_section_id' // Section           
            );

            add_settings_field(
                    'client_key_path', // ID
                    'Client Key Path', // Title 
                    array($this, 'client_key_path_callback'), // Callback
                    'contact-form-7-to-google-sheets', // Page
                    'cfgs_setting_section_id' // Section           
            );

            add_settings_field(
                    'client_key_pass', // ID
                    'Client Key Pass', // Title 
                    array($this, 'client_key_pass_callback'), // Callback
                    'contact-form-7-to-google-sheets', // Page
                    'cfgs_setting_section_id' // Section           
            );

            add_settings_field(
                    'application_name', // ID
                    'Application Name', // Title 
                    array($this, 'application_name_callback'), // Callback
                    'contact-form-7-to-google-sheets', // Page
                    'cfgs_setting_section_id' // Section           
            );

            add_settings_field(
                    'spreadsheet_name', // ID
                    'Spreadsheet Name', // Title 
                    array($this, 'spreadsheet_name_callback'), // Callback
                    'contact-form-7-to-google-sheets', // Page
                    'cfgs_setting_section_id' // Section           
            );
        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize($input) {
            $new_input = array();
            if (isset($input['client_id'])) {
                $new_input['client_id'] = sanitize_text_field($input['client_id']);
            }
            if (isset($input['client_key_path'])) {
                $new_input['client_key_path'] = sanitize_text_field($input['client_key_path']);
            }
            if (isset($input['email_address'])) {
                $new_input['email_address'] = sanitize_text_field($input['email_address']);
            }
            if (isset($input['client_key_pass'])) {
                $new_input['client_key_pass'] = sanitize_text_field($input['client_key_pass']);
            }
            if (isset($input['application_name'])) {
                $new_input['application_name'] = sanitize_text_field($input['application_name']);
            }
            if (isset($input['spreadsheet_name'])) {
                $new_input['spreadsheet_name'] = sanitize_text_field($input['spreadsheet_name']);
            }
            return $new_input;
        }

        /**
         * Print the Section text
         */
        public function print_section_info() {
            print ' If you have never created a Google API project, read the <a href="https://developers.google.com/console/help/#managingprojects" target="_blank">Managing Projects page</a> and create a project in the <a href="https://console.developers.google.com/" target="_blank">Google Developers Console</a>';
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function client_id_callback() {
            printf(
                    '<input type="text" id="client_id" name="cfgs_api_credentials[client_id]" value="%s" />', isset($this->options['client_id']) ? esc_attr($this->options['client_id']) : ''
            );
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function client_key_path_callback() {
            printf(
                    '<input type="text" id="client_key_path" name="cfgs_api_credentials[client_key_path]" value="%s" />', isset($this->options['client_key_path']) ? esc_attr($this->options['client_key_path']) : ''
            );
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function email_address_callback() {
            printf(
                    '<input type="text" id="email_address" name="cfgs_api_credentials[email_address]" value="%s" />', isset($this->options['email_address']) ? esc_attr($this->options['email_address']) : ''
            );
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function client_key_pass_callback() {
            printf(
                    '<input type="text" id="client_key_pass" name="cfgs_api_credentials[client_key_pass]" value="%s" />', isset($this->options['client_key_pass']) ? esc_attr($this->options['client_key_pass']) : ''
            );
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function application_name_callback() {
            printf(
                    '<input type="text" id="application_name" name="cfgs_api_credentials[application_name]" value="%s" />', isset($this->options['application_name']) ? esc_attr($this->options['application_name']) : ''
            );
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function spreadsheet_name_callback() {
            printf(
                    '<input type="text" id="spreadsheet_name" name="cfgs_api_credentials[spreadsheet_name]" value="%s" />', isset($this->options['spreadsheet_name']) ? esc_attr($this->options['spreadsheet_name']) : ''
            );
        }

        /**
         * Used to catch form data before mailing it
         *
         * @param array $cf7 Contains all form settings and fields as array keys
         */
        public function save_to_sheet($cf7) {

            $submission = WPCF7_Submission::get_instance();
            dump_res($submission->get_posted_data());
            $wpcf7 = WPCF7_ContactForm::get_current();
            $cf7gs_google_spreadsheet = new CF7GS_Google_Spreadsheet();
//          check if sheet exists
            if ($cf7gs_google_spreadsheet->exists($wpcf7->title()) == true) {
//              add to sheet
                $saved_to_sheet = $cf7gs_google_spreadsheet->save_to_sheet($wpcf7->title(), $submission->get_posted_data());
            } else {
//              create sheet
                $cf7gs_google_spreadsheet->add_sheet($wpcf7->title());
//              add headers       
                $cf7gs_google_spreadsheet->add_headers($wpcf7->title(), $submission->get_posted_data());
                $saved_to_sheet = $cf7gs_google_spreadsheet->save_to_sheet($wpcf7->title(), $submission->get_posted_data());
            }
        }

    }

}