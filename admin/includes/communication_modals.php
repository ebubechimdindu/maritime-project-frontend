<!-- New Broadcast Modal -->
<div class="modal fade" id="newBroadcastModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-bullhorn"></i> New Broadcast</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Message Type</label>
                        <select class="form-select" name="type" required>
                            <option value="emergency">Emergency Alert</option>
                            <option value="safety">Safety Notice</option>
                            <option value="weather">Weather Update</option>
                            <option value="navigation">Navigation Advisory</option>
                            <option value="general">General Message</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority Level</label>
                        <select class="form-select" name="priority" required>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Audience</label>
                        <select class="form-select" name="target_audience" required>
                            <option value="all_vessels">All Vessels</option>
                            <option value="specific_vessels">Specific Vessels</option>
                            <option value="authorities">Maritime Authorities</option>
                            <option value="emergency_teams">Emergency Response Teams</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message Content</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="send_broadcast" class="btn btn-primary">Send Broadcast</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-envelope-open-text"></i> Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Quick Broadcast Templates Modal -->
<div class="modal fade" id="quickBroadcastModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Broadcast</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="type" id="quickBroadcastType">
                    <input type="hidden" name="priority" id="quickBroadcastPriority">
                    <div class="mb-3">
                        <label class="form-label">Message Template</label>
                        <textarea class="form-control" name="message" id="quickBroadcastMessage" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Audience</label>
                        <select class="form-select" name="target_audience" required>
                            <option value="all_vessels">All Vessels</option>
                            <option value="specific_vessels">Specific Vessels</option>
                            <option value="authorities">Maritime Authorities</option>
                            <option value="emergency_teams">Emergency Response Teams</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="send_broadcast" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>