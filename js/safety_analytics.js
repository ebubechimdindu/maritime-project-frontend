document.addEventListener('DOMContentLoaded', function() {
    // Incident Trends Chart
    const trendsCtx = document.getElementById('incidentTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Incidents',
                data: [12, 19, 15, 8, 13, 17],
                borderColor: '#1e3c72',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Compliance Distribution Chart
    const complianceCtx = document.getElementById('complianceChart').getContext('2d');
    new Chart(complianceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Compliant', 'Warning', 'Non-Compliant'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Safety Performance Index Chart
    const safetyCtx = document.getElementById('safetyIndexChart').getContext('2d');
    new Chart(safetyCtx, {
        type: 'bar',
        data: {
            labels: ['Safety Equipment', 'Navigation Systems', 'Crew Certification', 'Environmental', 'Hull Integrity'],
            datasets: [{
                label: 'Compliance Score',
                data: [85, 92, 78, 88, 95],
                backgroundColor: '#3498db'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});

function viewReport(type) {
    // Implement report viewing logic
    window.location.href = `view_report.php?type=${type}`;
}

function downloadReport(type) {
    // Implement report download logic
    window.location.href = `download_report.php?type=${type}`;
}