/**
 * Admin Partner Detail - Status Handlers Module
 * 
 * Handles student status and application overall status updates
 * 
 * Dependencies:
 *   - jQuery
 *   - Bootstrap (for modals)
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[status-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[status-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[status-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[status-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// STUDENT STATUS CHANGE HANDLERS
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Use delegated handlers because DataTables redraws action buttons dynamically.
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.change-status-btn');
        if (!button) {
            return;
        }

        const studentId = button.getAttribute('data-id');
        const currentStatus = button.getAttribute('data-current-status');
        document.getElementById('studentId').value = studentId || '';
        document.getElementById('newStatus').value = currentStatus || '0';
    });

    // Change status form submission
    document.getElementById('changeStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(App.getUrl('partnersUpdateStudentStatus'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': App.getCsrf()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                $('#changeStatusModal').modal('hide');
                
                // Update the status in the visible DataTable without reloading
                const studentId = data.studentId;
                const newStatus = data.newStatus;
                const newStatus_id = data.newStatus_id;

                function updateStatusInTable(tableSelector) {
                    if (!$(tableSelector).length || !$.fn.DataTable.isDataTable(tableSelector)) {
                        return false;
                    }
                    const table = $(tableSelector).DataTable();
                    const rowIndex = table.rows().eq(0).filter((rowIdx) => {
                        return table.cell(rowIdx, 23).data() == studentId;
                    });
                    if (rowIndex.length > 0) {
                        table.cell(rowIndex[0], 21).data(newStatus).draw(false);
                        $(tableSelector).find('.change-status-btn[data-id="' + studentId + '"]').attr('data-current-status', newStatus_id);
                        return true;
                    }
                    return false;
                }

                if (!updateStatusInTable('.table-3')) {
                    updateStatusInTable('.table-31');
                }

                $('.custom-error-msg').html('<span class="alert alert-success">'+data.message+'</span>');
            } else {
                $('.custom-error-msg').html('<span class="alert alert-danger">'+data.message+'</span>');
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // ============================================================================
    // APPLICATION OVERALL STATUS CHANGE HANDLERS
    // ============================================================================
    
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.change-application-overall-status-btn');
        if (!button) {
            return;
        }

        const applicationStudentId = button.getAttribute('data-id');
        const applicationOverallStatus = button.getAttribute('data-application-overall-status');
        document.getElementById('applicationStudentId').value = applicationStudentId || '';
        document.getElementById('applicationOverallStatus').value = applicationOverallStatus || '';
    });

    // Change application overall status form submission
    document.getElementById('changeApplicationOverallStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(App.getUrl('partnersUpdateStudentApplicationStatus'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': App.getCsrf()
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload(); // Reload to reflect changes
        })
        .catch(error => console.error('Error:', error));
    });
    
});

})(); // End async wrapper
