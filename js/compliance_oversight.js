$(document).ready(function() {
    $('#complianceTable').DataTable({
        order: [[2, 'desc']]
    });
});

function viewCompliance(complianceId) {
    fetch(`get_compliance_details.php?id=${complianceId}`)
        .then(response => response.json())
        .then(data => {
            // Populate vessel info
            document.querySelector('.vessel-info').innerHTML = `
                <h4>${data.vessel_name}</h4>
                <p><strong>IMO Number:</strong> ${data.imo_number}</p>
                <p><strong>Owner:</strong> ${data.owner_name}</p>
            `;

            // Populate checklist
            const checklistHtml = generateChecklistHtml(data);
            document.getElementById('checklistContent').innerHTML = checklistHtml;

            // Show modal
            new bootstrap.Modal(document.getElementById('complianceDetailsModal')).show();
        });
}

function issueWarning(complianceId) {
    document.getElementById('warning_compliance_id').value = complianceId;
    new bootstrap.Modal(document.getElementById('warningModal')).show();
}

function overrideStatus(complianceId) {
    document.getElementById('override_compliance_id').value = complianceId;
    new bootstrap.Modal(document.getElementById('overrideModal')).show();
}

function generateChecklistHtml(data) {
    const checks = [
        { key: 'safety_equipment_check', label: 'Safety Equipment' },
        { key: 'navigation_systems_check', label: 'Navigation Systems' },
        { key: 'crew_certification_check', label: 'Crew Certification' },
        { key: 'environmental_compliance', label: 'Environmental Compliance' },
        { key: 'hull_integrity_check', label: 'Hull Integrity' },
        { key: 'firefighting_equipment', label: 'Firefighting Equipment' },
        { key: 'medical_supplies_check', label: 'Medical Supplies' },
        { key: 'radio_equipment_check', label: 'Radio Equipment' },
        { key: 'waste_management_check', label: 'Waste Management' },
        { key: 'security_systems_check', label: 'Security Systems' }
    ];

    return checks.map(check => `
        <div class="col-md-6">
            <div class="compliance-check-item mb-2">
                <i class="fas fa-${data[check.key] ? 'check text-success' : 'times text-danger'}"></i>
                ${check.label}
            </div>
        </div>
    `).join('');
}

function printComplianceReport() {
    window.print();
}