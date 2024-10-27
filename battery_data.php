<?php
require 'config.php';

// Function to make API requests for the current battery data
function getBatteryData($endpoint) {
    global $api_url, $bearer_token;
    $url = $api_url . 'states/' . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $bearer_token,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Fetch current battery data
$power_data = getBatteryData("sensor.thuisbatterij_power");
$percentage_data = getBatteryData("sensor.thuisbatterij_percentage");
$total_data = getBatteryData("sensor.thuisbatterij_total");
$today_data = getBatteryData("sensor.thuisbatterij_today");
$charged_data = getBatteryData("sensor.thuisbatterij_delivery_today");
$discharged_data = getBatteryData("sensor.thuisbatterij_production_today");

// Extract current battery values
$power_state = $power_data['state'];
$power_value = abs($power_state) . " W";
$percentage = round($percentage_data['state'], 0);
$charged_today = $charged_data['state'];
$discharged_today = $discharged_data['state'];

// Format earnings to always show two decimal places
$total_earned = "€ " . number_format($total_data['state'], 2, ',', '');
$today_earned = "€ " . number_format($today_data['state'], 2, ',', '');

if ($power_state > 500) {
    $power_status = "Charging";
    $dot_color = "#00FF85";
} elseif ($power_state < 0) {
    $power_status = "Discharging";
    $dot_color = "#FF4C4C";
} else {
    $power_status = "Idle";
    $dot_color = "#888888";
}

// Function to fetch historical battery power data
function getBatteryHistory() {
    global $api_url, $bearer_token;
    $url = $api_url . "history/period?filter_entity_id=sensor.thuisbatterij_power";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $bearer_token,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Fetch and format historical data for the power chart
$history_data = getBatteryHistory();
$chart_labels = [];
$chart_power_values = [];

if (!empty($history_data[0])) {
    foreach ($history_data[0] as $data_point) {
        $chart_labels[] = date('H:i', strtotime($data_point['last_changed']));
        $chart_power_values[] = $data_point['state'];
    }
}

// Combine current data and history data in one JSON response
$response = [
    "current_data" => [
        "power_status" => $power_status,
        "power_value" => $power_value,
        "percentage" => $percentage,
        "charged_today" => $charged_today,
        "discharged_today" => $discharged_today,
        "total_earned" => $total_earned,
        "today_earned" => $today_earned,
        "dot_color" => $dot_color
    ],
    "history_data" => [
        "chart_labels" => $chart_labels,
        "chart_power_values" => $chart_power_values
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>

