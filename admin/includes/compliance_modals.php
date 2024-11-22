<!-- Compliance Details Modal -->
<div class="modal fade" id="complianceDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-clipboard-check"></i> Compliance Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="complianceDetailsContent">
                <div class="vessel-info mb-4"></div>
                <div class="compliance-checks">
                    <h5>Compliance Checklist</h5>
                    <div class="row" id="checklistContent"></div>
                </div>
                <div class="compliance-history mt-4">
                    <h5>Compliance History</h5>
                    <div id="historyContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printComplianceReport()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Issue Warning Modal -->
<div class="modal fade" id="warningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Issue Compliance Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="compliance_id" id="warning_compliance_id">
                    <div class="mb-3">
                        <label class="form-label">Warning Message</label>
                        <textarea class="form-control" name="warning_message" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Compliance Deadline</label>
                        <input type="date" class="form-control" name="deadline_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="issue_warning" class="btn btn-warning">Issue Warning</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Override Status Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Override Compliance Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="compliance_id" id="override_compliance_id">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" name="new_status" required>
                            <option value="compliant">Compliant</option>
                            <option value="warning">Warning</option>
                            <option value="non_compliant">Non-Compliant</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Override Reason</label>
                        <textarea class="form-control" name="admin_notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="override_status" class="btn btn-primary">Override Status</button>
                </div>
            </form>
        </div>
    </div>
</div>