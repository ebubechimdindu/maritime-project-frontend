// Weather alert functionality
function getWeatherAlerts() {
  const alerts = [];
  weatherData.forEach((weather) => {
    // Check for severe conditions
    if (weather.wind_speed > 10) {
      alerts.push({
        type: "warning",
        icon: "fa-exclamation-triangle",
        message: `Strong Winds (${weather.wind_speed} m/s): ${weather.location}`,
      });
    }

    if (
      weather.weather_condition === "Thunderstorm" ||
      weather.weather_condition === "Storm"
    ) {
      alerts.push({
        type: "warning",
        icon: "fa-bolt",
        message: `Storm Warning: ${weather.location}`,
      });
    }

    if (weather.description.toLowerCase().includes("heavy")) {
      alerts.push({
        type: "warning",
        icon: "fa-exclamation-circle",
        message: `Severe Weather: ${weather.location}`,
      });
    }

    if (weather.wind_speed > 5 && weather.wind_speed <= 10) {
      alerts.push({
        type: "info",
        icon: "fa-info-circle",
        message: `High Waves: ${weather.location}`,
      });
    }
  });
  return alerts;
}

function initWeatherAlerts() {
  setInterval(function () {
    fetch("get_weather_alerts.php")
      .then((response) => response.json())
      .then((data) => {
        const alertsContainer = document.querySelector(".weather-alerts");
        alertsContainer.innerHTML = "";

        data.forEach((alert) => {
          alertsContainer.innerHTML += `
                        <div class="alert-item info">
                            <i class="fas fa-cloud"></i>
                            <span>${alert.location}: ${alert.description} 
                                (${alert.temperature}°C, Wind: ${alert.wind_speed} m/s)</span>
                        </div>
                    `;
        });
      });
  }, 300000);
}

document.addEventListener("DOMContentLoaded", initWeatherAlerts);
