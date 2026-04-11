<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Read JSON POST body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// --- Load JSON model ---
$json = file_get_contents("data.json");
$model = json_decode($json, true);

// Build the trip

// Extract search criteria
$depart     = $data['depart'];
$arrival    = $data['arrival'];
$dateDepart = $data['dateDepart'];

$departAirports = array();
foreach ($model['airports'] as $airport) {
    if ($airport['city'] === $depart) {
        $departAirports[] = $airport; // cleaner than array_push
    }
}

$arrivalAirports = array();
foreach ($model['airports'] as $airport) {
    if ($airport['city'] === $arrival) {
        $arrivalAirports[] = $airport; // cleaner than array_push
    }
}

$foundFlights = array();

// finding all flights that depart from any of departure airports and arrives at any of arrival airports
$flightIsDeparting = false;
$flightIsArriving = false;
foreach ($model['flights'] as $flight) {

    foreach($departAirports as $departAirport)
    {
        if ($flight['departure_airport'] === $departAirport['code'])
        {
            $flightIsDeparting = true;
            break;
        }
    }

    if ($flightIsDeparting === false) {
        continue;
    }


    foreach($arrivalAirports as $arrivalAirport)
    {
        if ($flight['arrival_airport'] === $arrivalAirport['code'])
        {
            $flightIsArriving = true;
            break;
        }
    }

    // i found a departure, this flight is arriving or not at this location
    if ($flightIsArriving)
    {
        // found flight, add it
        $foundFlights[] = $flight;

    }

    // chek next flight, reset and continue
    $flightIsDeparting = false;
    $flightIsArriving = false;

}



echo json_encode($foundFlights);