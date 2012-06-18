<?php

error_reporting(E_ALL);
ini_set('display_errors', 'true');

require('config.inc.php');

class WhereIsTheNorthReporter {
    function __construct() {
        $this->db = new mysqli(null, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    }
    
    public function getRandomLocation() {
        return array(
            'id' => 1,
            'name' => 'Doncaster',
            'lat' => 53.516,
            'lng' => -1.133
        );
    }
    
    public function getPlaceNameFromId($id) {
        return 'Doncaster';
    }
    
    public function saveSubmission($placeId, $choice, $postcode) {
        $query = $this->db->prepare("INSERT INTO results(placeId,choice,postcode) VALUES(?,?,?)");
        if ($query === false) { die($this->db->error); }
        $query->bind_param('iss', $placeId, $choice, $postcode);
        $query->execute();
    }
    
    public function safeSaveSubmissionFromPost() {
        $response = array();
        $placeId = filter_input(INPUT_POST, 'placeId', FILTER_VALIDATE_INT);
        $choice = $this->getCleanChoice();
        if (!empty($choice)) {
            if (!empty($placeId)) {
                $placeName = $this->getPlaceNameFromId($placeId);
                if (!empty($placeName)) {
                    if ($choice != 'dunno') {
                        $this->saveSubmission($placeId, $choice, $_POST['postcode']);
                    }
                    $response['lastLocation'] = $placeName;
                    $response['lastSubmission'] = $choice;
                }
            }
        }
        return $response;
    }
    
    private function getCleanChoice() {
        $choice = null;
        if (isset($_POST['choice'])) {
            switch ($_POST['choice']) {
                case 'north':
                case 'south':
                case 'midlands':
                case 'dunno':
                    $choice = $_POST['choice'];
                    break;
            }
        }
        return $choice;
    }
}

$whereIsTheNorth = new WhereIsTheNorthReporter();
$response = $whereIsTheNorth->getRandomLocation();
$response = array_merge($response, $whereIsTheNorth->safeSaveSubmissionFromPost());

header('Content-type: application/json');
echo json_encode($response);
