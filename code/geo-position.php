<?php session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

define('__ROOT__', dirname(__DIR__));
require_once(__ROOT__.'/vendor/autoload.php');

$response = [
    'status'    => false,
    'location'  => false,
    'message'   => false,
    'errors'    => false,
];

if (isset($_SESSION['userLocation'])) {
    $response['status']    = true;
    $response['message']    = 'Session user location data';
    $response['location']   = $_SESSION['userLocation'];

    echo json_encode($response);
    die();
}

const YANDEX_API_KEY = '08e43b5b-ff6b-4bc1-8ea6-3af9a2017269';
$httpClient = new \Http\Adapter\Guzzle6\Client();
$userLocation = false;

if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
    $latitude   = (float) $_POST['latitude'];
    $longitude  = (float) $_POST['longitude'];

    $userLocation = locateByUserCoordinates($latitude, $longitude, $httpClient);
    $response['message'] = 'Located by the navigator API geolocation data';
} else {
    $userIpAddress = getUserIpAddress();
    $userLocation = locateByUserIpAddress($httpClient, $userIpAddress);
    $response['message'] = 'Located by user ip address';
}

if (isset($userLocation)) {
    $_SESSION['userLocation'] = $userLocation;
    $response['status'] = true;
    $response['location'] = $userLocation;

    echo json_encode($response);
    die();
} else {
    $response['message'] = 'No data';

    echo json_encode($response);
    die();
}

/**
 * @param float $latitude
 * @param float $longitude
 * @param $httpClient
 * @return string|null
 * @throws \Geocoder\Exception\Exception
 */
function locateByUserCoordinates(float $latitude, float $longitude, $httpClient) {
    $userLocation = false;

    try {
        $provider = new \Geocoder\Provider\Yandex\Yandex($httpClient, null, YANDEX_API_KEY);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'ru');

        $result = $geocoder->reverse($latitude, $longitude);
        $singleLocationData = $result->first();
        $userLocation = $singleLocationData->getLocality();
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }

    if ($userLocation) {
        return $userLocation;
    } else {
        $response['errors'] = $errorMessage;
        $response['message'] = 'Location by the navigator API geolocation data failed';
        echo json_encode($response);
        die();
    }
}

/**
 * @param $httpClient
 * @param $userIpAddress
 * @return bool|string|null
 * @throws \Geocoder\Exception\Exception
 */
function locateByUserIpAddress($httpClient, $userIpAddress) {
    $userLocation = false;

    try {
        $provider = new Geocoder\Provider\FreeGeoIp\FreeGeoIp($httpClient);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'ru');
        $result = $geocoder->geocode($userIpAddress);
        $singleLocationData = $result->first();
        $userLocation = $singleLocationData->getLocality();
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }

    if ($userLocation) {
        return $userLocation;
    } else {
        $response['errors'] = $errorMessage;
        $response['message'] = 'Location by user ip address failed';
        echo json_encode($response);
        die();
    }
}

/**
 * @return mixed
 */
function getUserIpAddress() {
    // Whether ip is from share internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { // Whether ip is from proxy
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else { //whether ip is from remote address
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    }

    return $ipAddress;
}
