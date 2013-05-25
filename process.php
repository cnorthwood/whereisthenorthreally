<?php

error_reporting(E_ALL);
ini_set('display_errors', 'true');

require('config.inc.php');

function verifyCsrf() {
    return !empty($_COOKIE['whereisthenorthcsrftoken']) && !empty($_POST['csrftoken']) && ($_COOKIE['whereisthenorthcsrftoken'] == $_POST['csrftoken']);
}

class WhereIsTheNorthReporter {
    function __construct() {
        $this->db = new mysqli(null, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    }
    
    public function getRandomLocation() {
        $query = $this->db->prepare("SELECT placeId,name,lat,lon FROM places ORDER BY RAND() LIMIT 1;");
        $query->execute();
        $query->bind_result($placeId, $name, $lat, $lon);
        $query->fetch();
        return array(
            'id' => $placeId,
            'name' => $name,
            'lat' => $lat,
            'lon' => $lon
        );
    }
    
    public function getPlaceNameFromId($id) {
        $query = $this->db->prepare("SELECT name FROM places WHERE placeId=?");
        $query->bind_param('i', $id);
        $query->execute();
        $query->bind_result($name);
        $query->fetch();
        return $name;
    }
    
    public function saveSubmission($placeId, $choice, $postcode) {
        $query = $this->db->prepare("INSERT INTO results(placeId,choice,postcode,ip) VALUES(?,?,?,?)");
        if ($query === false) { die($this->db->error); }
        $query->bind_param('isss', $placeId, $choice, $postcode, $_SERVER['REMOTE_ADDR']);
        $query->execute();
    }

	public function getAgreement($placeId, $choice) {
		$query = $this->db->prepare("SELECT (SELECT COUNT(*) FROM results WHERE `placeId`=? AND choice=?) / count(*) FROM results WHERE `placeId`=?;");
		$query->bind_param('isi', $placeId, $choice, $placeId);
		$query->execute();
		$query->bind_result($agreement);
		$query->fetch();
		return $agreement;
	}
    
    public function safeSaveSubmissionFromPost() {
        $response = array();
        $placeId = filter_input(INPUT_POST, 'placeId', FILTER_VALIDATE_INT);
        $choice = $this->getCleanChoice();
        if (!empty($choice)) {
            if (!empty($placeId)) {
                $placeName = $this->getPlaceNameFromId($placeId);
                if (!empty($placeName)) {
                    $this->saveSubmission($placeId, $choice, $_POST['postcode']);
                    $response['lastLocation'] = $placeName;
                    $response['lastSubmission'] = $choice;
					$response['agreement'] = $this->getAgreement($placeId, $choice);
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
if (isset($_POST['placeId'])) {
    if (verifyCsrf()) {
        $response = array_merge($response, $whereIsTheNorth->safeSaveSubmissionFromPost());
    } else {
        die('CSRF failure');
    }
}

header('Content-type: application/json');
header("Cache-Control: no-cache, must-revalidate");
echo json_encode($response);
