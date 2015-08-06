<?php

/*
 * Title                   : Contact Form 7 to Google Sheets
 * Version                 : 1.0
 * File                    : classes/class.cf7gs-google-spreadsheet.php
 * File Version            : 1.0.0
 * Created / Last Modified : 26 June 2015
 * Author                  : Danny Connolly
 * Copyright               : © 2015 Danny Connolly
 * Website                 : http://www.dannyconnolly.me
 * Description             : Contact Form 7 to Google Sheets
 */

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

if (!class_exists('CF7GS_Google_Spreadsheet')) {

    class CF7GS_Google_Spreadsheet {

        public $G_CLIENT_ID;
        public $G_CLIENT_EMAIL;
        public $G_CLIENT_KEY_PATH;
        public $G_CLIENT_KEY_PW;
        public $APPLICATION_NAME;
        public $SPREADSHEET_NAME;
        public $SPREADSHEET_SERVICE;
        public $SPREADSHEET_FEED;
        public $SPREADSHEET;

        /**
         * 
         */
        public function __construct() {

            $options = get_option('cfgs_api_credentials');

            $this->G_CLIENT_ID = $options['client_id'];
            $this->G_CLIENT_EMAIL = $options['email_address'];
            $this->G_CLIENT_KEY_PATH = $options['client_key_path'];
            $this->G_CLIENT_KEY_PW = $options['client_key_pass'];
            $this->APPLICATION_NAME = $options['application_name'];
            $this->SPREADSHEET_NAME = $options['spreadsheet_name'];

            $obj_client_auth = new Google_Client();
            $obj_client_auth->setApplicationName($this->APPLICATION_NAME);
            $obj_client_auth->setClientId($this->G_CLIENT_ID);
            $obj_client_auth->setAssertionCredentials(new Google_Auth_AssertionCredentials(
                    $this->G_CLIENT_EMAIL, array('https://spreadsheets.google.com/feeds', 'https://docs.google.com/feeds'), file_get_contents(CFGS_PATH . $this->G_CLIENT_KEY_PATH), $this->G_CLIENT_KEY_PW
            ));
            $obj_client_auth->getAuth()->refreshTokenWithAssertion();
            $obj_token = json_decode($obj_client_auth->getAccessToken());
            $accessToken = $obj_token->access_token;
            $serviceRequest = new Google\Spreadsheet\DefaultServiceRequest($accessToken);
            ServiceRequestFactory::setInstance($serviceRequest);

            $this->SPREADSHEET_SERVICE = new Google\Spreadsheet\SpreadsheetService();
            $this->SPREADSHEET_FEED = $this->SPREADSHEET_SERVICE->getSpreadsheets();
            $this->SPREADSHEET = $this->SPREADSHEET_FEED->getByTitle($this->SPREADSHEET_NAME);
        }

        /**
         * Checks if Sheet already exists
         * 
         * @param string $name
         * @return boolean
         */
        public function exists($name) {
            $worksheetFeed = $this->SPREADSHEET->getWorksheets();
            $worksheet = $worksheetFeed->getByTitle($name);
            if ($worksheet == true) {
                return true;
            }
        }

        /**
         * 
         * @param string $name
         */
        public function add_sheet($name) {
            $this->SPREADSHEET->addWorksheet($name);
        }

        /**
         * 
         * @param string $name
         * @param array $data
         */
        public function add_headers($name, $data) {
            $worksheetFeed = $this->SPREADSHEET->getWorksheets();
            $worksheet = $worksheetFeed->getByTitle($name);
            $cellFeed = $worksheet->getCellFeed();
            $x = 1;

            unset($data['_wpcf7'], $data['_wpcf7_version'], $data['_wpcf7_locale'], $data['_wpcf7_unit_tag'], $data['_wpnonce']);
            foreach ($data as $key => $val) {
                $cellFeed->editCell(1, $x, $key);
                $x++;
            }
        }

        /**
         * Save Submitted Data to Google Spreadsheet
         * 
         * @param string $sheet
         * @param array $data
         * @return boolean
         */
        public function save_to_sheet($sheet, $data) {
            $worksheetFeed = $this->SPREADSHEET->getWorksheets();
            $worksheet = $worksheetFeed->getByTitle($sheet);
            $listFeed = $worksheet->getListFeed();

            $listFeed->insert($data);
            return true;
        }

//        public function update_sheet($sheet, $orderid, $status) {
//
//            $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
//            $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
//            $spreadsheet = $spreadsheetFeed->getByTitle($this->SPREADSHEET_NAME);
//            $worksheetFeed = $spreadsheet->getWorksheets();
//            $worksheet = $worksheetFeed->getByTitle($sheet);
//            $listFeed = $worksheet->getListFeed(array("sq" => "orderid = $orderid"));
//            $entries = $listFeed->getEntries();
//            $listEntry = $entries[0];
//
//            $values = $listEntry->getValues();
//            $values['status'] = $status;
//            $listEntry->update($values);
//        }

        public function dump($data) {
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
        }

    }

}