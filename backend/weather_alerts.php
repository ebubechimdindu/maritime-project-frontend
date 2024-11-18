<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

// OpenWeatherMap API configuration
$api_key = 'bd5e378503939ddaee76f12ad7a97608'; // Replace with your real API key

// Add error handling for API calls
function getWeatherData($location, $api_key)
{
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$location['lat']}&lon={$location['lon']}&appid={$api_key}&units=metric";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        return null;
    }

    return json_decode($response, true);
}
$maritime_locations = [
    ['name' => 'Strait of Malacca', 'lat' => '1.7500', 'lon' => '101.3333'],
    ['name' => 'South China Sea', 'lat' => '13.9833', 'lon' => '114.9833'],
    ['name' => 'Mediterranean Sea', 'lat' => '35.4667', 'lon' => '18.5333'],
    ['name' => 'Gulf of Aden', 'lat' => '12.7086', 'lon' => '47.1206'],
    ['name' => 'Panama Canal', 'lat' => '9.0800', 'lon' => '-79.6833'],
    ['name' => 'Suez Canal', 'lat' => '30.7500', 'lon' => '32.3333'],
    ['name' => 'Cape of Good Hope', 'lat' => '-34.3568', 'lon' => '18.4740'],
    ['name' => 'Port of Lagos', 'lat' => '6.4531', 'lon' => '3.3958'],
    ['name' => 'Mozambique Channel', 'lat' => '-16.5867', 'lon' => '41.3205'],
    ['name' => 'Gulf of Guinea', 'lat' => '3.2500', 'lon' => '2.7167'],
    ['name' => 'Strait of Gibraltar', 'lat' => '35.9961', 'lon' => '-5.3535'],
    ['name' => 'Port of Durban', 'lat' => '-29.8587', 'lon' => '31.0218']
];

$weather_data = [];
foreach ($maritime_locations as $location) {
    $data = getWeatherData($location, $api_key);

    if ($data) {
        $weather_data[] = [
            'location' => $location['name'],
            'temperature' => $data['main']['temp'] ?? 'N/A',
            'wind_speed' => $data['wind']['speed'] ?? 'N/A',
            'wind_direction' => $data['wind']['deg'] ?? 'N/A',
            'weather_condition' => $data['weather'][0]['main'] ?? 'N/A',
            'description' => $data['weather'][0]['description'] ?? 'N/A',
            'humidity' => $data['main']['humidity'] ?? 'N/A'
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Alerts - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Maritime Weather Alerts</h1>
                <div class="user-info">
                    <span class="user-name">Hello <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="container mt-4">
                <div class="row">
                    <?php foreach ($weather_data as $weather): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $weather['location']; ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="weather-info">
                                        <div class="weather-icon">
                                            <i class="fas <?php echo getWeatherIcon($weather['weather_condition']); ?> fa-3x"></i>
                                        </div>
                                        <div class="weather-details">
                                            <p class="temperature">
                                                <i class="fas fa-thermometer-half"></i>
                                                <?php echo round($weather['temperature']); ?>°C
                                            </p>
                                            <p class="wind">
                                                <i class="fas fa-wind"></i>
                                                Wind: <?php echo $weather['wind_speed']; ?> m/s
                                                <i class="fas fa-compass"></i>
                                                <?php echo getWindDirection($weather['wind_direction']); ?>
                                            </p>
                                            <p class="humidity">
                                                <i class="fas fa-tint"></i>
                                                Humidity: <?php echo $weather['humidity']; ?>%
                                            </p>
                                            <p class="condition">
                                                <?php echo ucfirst($weather['description']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if (isHazardousCondition($weather)): ?>
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Weather Advisory: Potentially hazardous conditions
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
  <!-- Logout Modal with Modern UI -->
  <div class="modal fade logout-modal-wrapper" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-logout-modal">
                <div class="modal-body">
                    <div class="logout-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </div>
                    <h3 class="logout-title">Log Out</h3>
                    <p class="logout-message">Are you sure you want to log out of your account?</p>
                    <div class="logout-actions">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-logout">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
function getWeatherIcon($condition)
{
    $icons = [
        'Clear' => 'fa-sun',
        'Clouds' => 'fa-cloud',
        'Rain' => 'fa-cloud-rain',
        'Thunderstorm' => 'fa-bolt',
        'Snow' => 'fa-snowflake',
        'Mist' => 'fa-smog',
        'Fog' => 'fa-smog'
    ];
    return $icons[$condition] ?? 'fa-cloud';
}

function getWindDirection($degrees)
{
    $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
    return $directions[round($degrees / 45) % 8];
}

function isHazardousCondition($weather)
{
    return (
        $weather['wind_speed'] > 10 || // Strong winds
        $weather['weather_condition'] == 'Thunderstorm' ||
        $weather['weather_condition'] == 'Storm' ||
        strpos(strtolower($weather['description']), 'heavy') !== false
    );
}
?>