$(document).ready(function() {
    $('#emergenciesTable').DataTable({
        order: [[0, 'desc']]
    });

    // Real-time updates every 30 seconds
    setInterval(refreshEmergencies, 30000);
});

function viewEmergencyDetails(incidentId) {
    fetch(`get_emergency_details.php?id=${incidentId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('.emergency-info').innerHTML = generateEmergencyInfo(data);
            document.getElementById('timelineContent').innerHTML = generateTimeline(data.timeline);
            new bootstrap.Modal(document.getElementById('emergencyDetailsModal')).show();
        });
}

function deployTeam(incidentId) {
    document.getElementById('deploy_incident_id').value = incidentId;
    new bootstrap.Modal(document.getElementById('deployTeamModal')).show();
}

function updateStatus(incidentId) {
    document.getElementById('status_incident_id').value = incidentId;
    new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
}

function generateEmergencyInfo(data) {
    return `
        <div class="row">
            <div class="col-md-6">
                <h4>${data.vessel_name}</h4>
                <p><strong>IMO Number:</strong> ${data.imo_number}</p>
                <p><strong>Type:</strong> ${data.incident_type}</p>
                <p><strong>Location:</strong> ${data.location}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Reported By:</strong> ${data.reporter_name}</p>
                <p><strong>Time:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                <p><strong>Severity:</strong> <span class="badge bg-${data.severity_level === 'critical' ? 'danger' : 'warning'}">${data.severity_level}</span></p>
                <p><strong>Status:</strong> <span class="badge bg-${getStatusClass(data.status)}">${data.status}</span></p>
            </div>
        </div>
        <div class="mt-3">
            <h5>Description</h5>
            <p>${data.description}</p>
        </div>
    `;
}

function generateTimeline(timeline) {
    return timeline.map(event => `
        <div class="timeline-item">
            <div class="timeline-time">${new Date(event.time).toLocaleString()}</div>
            <div class="timeline-content">
                <strong>${event.action}</strong>
                <p>${event.details}</p>
            </div>
        </div>
    `).join('');
}

function getStatusClass(status) {
    const classes = {
        'resolved': 'success',
        'investigating': 'warning',
        'reported': 'info',
        'closed': 'secondary'
    };
    return classes[status] || 'primary';
}

function soundAlarm() {
    // Implement alarm sound functionality
    const audio = new Audio('assets/alarm.mp3');
    audio.play();
}

function refreshEmergencies() {
    location.reload();
}

function printEmergencyReport() {
    window.print();
}