<?php
session_start();
require_once 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$user_id = $_SESSION['user_id'];

// Get vessel counts by status
$active_vessels_sql = "SELECT COUNT(*) as count FROM vessels WHERE user_id = '$user_id' AND status = 'active'";
$in_port_sql = "SELECT COUNT(*) as count FROM vessels WHERE user_id = '$user_id' AND status = 'in_port'";
$at_sea_sql = "SELECT COUNT(*) as count FROM vessels WHERE user_id = '$user_id' AND status = 'active' AND location = 'at_sea'";

$active_result = $conn->query($active_vessels_sql)->fetch_assoc();
$in_port_result = $conn->query($in_port_sql)->fetch_assoc();
$at_sea_result = $conn->query($at_sea_sql)->fetch_assoc();

// Get weather alerts for dashboard
function getWeatherAlerts() {
    global $conn;
    $api_key = 'bd5e378503939ddaee76f12ad7a97608'; 
    
    $maritime_locations = [
        ['name' => 'Strait of Malacca', 'lat' => '1.7500', 'lon' => '101.3333'],
        ['name' => 'South China Sea', 'lat' => '13.9833', 'lon' => '114.9833'],
        ['name' => 'Mediterranean Sea', 'lat' => '35.4667', 'lon' => '18.5333']
    ]; // Limited to 3 locations for the dashboard card

    $alerts = [];
    foreach ($maritime_locations as $location) {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$location['lat']}&lon={$location['lon']}&appid={$api_key}&units=metric";
        $response = json_decode(file_get_contents($url), true);
        
        if ($response) {
            $alerts[] = [
                'location' => $location['name'],
                'description' => $response['weather'][0]['description'],
                'temperature' => $response['main']['temp'],
                'wind_speed' => $response['wind']['speed']
            ];
        }
    }
    return $alerts;
}

$weather_alerts = getWeatherAlerts();


