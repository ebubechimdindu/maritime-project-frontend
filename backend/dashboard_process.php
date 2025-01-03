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

$compliance_query = "SELECT 
    AVG(CASE WHEN safety_equipment_check = 1 OR navigation_systems_check = 1 
        OR crew_certification_check = 1 OR firefighting_equipment = 1 
        OR medical_supplies_check = 1 THEN 100 ELSE 0 END) as safety_cert_percentage,
    AVG(CASE WHEN environmental_compliance = 1 OR waste_management_check = 1 
        OR radio_equipment_check = 1 OR security_systems_check = 1 
        OR hull_integrity_check = 1 THEN 100 ELSE 0 END) as documentation_percentage
    FROM vessel_compliance 
    WHERE user_id = '$user_id'";

$compliance_result = $conn->query($compliance_query);
$compliance_data = $compliance_result->fetch_assoc();

$safety_percentage = round($compliance_data['safety_cert_percentage'] ?? 0);
$documentation_percentage = round($compliance_data['documentation_percentage'] ?? 0);

// Get weather alerts for dashboard
function getWeatherAlerts()
{
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
