<?php
require 'vendor/autoload.php';

class GoogleSheet {
    private $service;
    private $spreadsheetId;

    public function __construct($spreadsheetId) {
        $this->spreadsheetId = $spreadsheetId;
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setAuthConfig('include/credentials.json'); // Ganti dengan path ke file JSON kredensial Anda
        $client->setAccessType('offline');
        $this->service = new Google_Service_Sheets($client);
    }

    public function updateValues($range, $values) {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $this->service->spreadsheets_values->update($this->spreadsheetId, $range, $body, $params);
    }
}
?>
