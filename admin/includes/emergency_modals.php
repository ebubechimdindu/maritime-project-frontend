<!-- Emergency Details Modal -->
<div class="modal fade" id="emergencyDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Emergency Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="emergencyDetailsContent">
                <div class="emergency-info mb-4"></div>
                <div class="response-timeline">
                    <h5>Response Timeline</h5>
                    <div id="timelineContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printEmergencyReport()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Deploy Team Modal -->
<div class="modal fade" id="deployTeamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-users"></i> Deploy Response Team</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deployTeamForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="incident_id" id="deploy_incident_id">
                    <div class="mb-3">
                        <label class="form-label">Select Team</label>
                        <select class="form-select" name="team_id" required>
                            <option value="">Choose team...</option>
                            <option value="1">Fire Response Team</option>
                            <option value="2">Medical Emergency Team</option>
                            <option value="3">Maritime Police Unit</option>
                            <option value="4">Environmental Response Team</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deployment Notes</label>
                        <textarea class="form-control" name="deployment_notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estimated Response Time</label>
                        <input type="number" class="form-control" name="eta_minutes" placeholder="Minutes" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Deploy Team</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle"></i> Update Emergency Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="incident_id" id="status_incident_id">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" name="new_status" required>
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resolution Notes</label>
                        <textarea class="form-control" name="resolution_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>