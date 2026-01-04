/**
 * Activity Handlers Module
 * 
 * Functions for managing activities/logs across detail pages
 * 
 * Usage:
 *   getallactivities()
 *   deleteactivitylog(id)
 */

'use strict';

/**
 * Get all activities for the current page
 * Fetches activities based on PageConfig settings
 */
function getallactivities() {
    var activityId = App.getPageConfig('clientId') || 
                     App.getPageConfig('productId') || 
                     App.getPageConfig('userId') || 
                     App.getPageConfig('agentId') || 
                     App.getPageConfig('partnerId');
    
    if (!activityId) {
        console.warn('Activity ID not found in PageConfig');
        return;
    }
    
    var url = App.getUrl('getActivities') || App.getUrl('siteUrl') + '/get-activities';
    if (!url) {
        console.error('getActivities URL not configured');
        return;
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        datatype: 'json',
        data: { id: activityId },
        success: function(responses) {
            var ress = typeof responses === 'string' ? JSON.parse(responses) : responses;
            var html = '';
            
            if (ress.data && ress.data.length > 0) {
                $.each(ress.data, function(k, v) {
                    // Build activity HTML (simplified - actual implementation may vary)
                    html += '<div class="activity" id="activity_' + v.id + '">';
                    html += '<p>' + (v.subject || '') + '</p>';
                    html += '</div>';
                });
            }
            
            $('.activitiesdata').html(html);
        }
    });
}

/**
 * Get all notes for the current page
 */
function getallnotes() {
    var clientId = App.getPageConfig('clientId');
    var type = App.getPageConfig('clientType') || 'client';
    
    if (!clientId) {
        console.warn('Client ID not found in PageConfig');
        return;
    }
    
    var url = App.getUrl('getNotes') || App.getUrl('siteUrl') + '/get-notes';
    if (!url) {
        console.error('getNotes URL not configured');
        return;
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        data: { clientid: clientId, type: type },
        success: function(responses) {
            $('.popuploader').hide();
            $('.note_term_list').html(responses);
        }
    });
}

/**
 * Delete activity log entry
 * @param {number|string} id - Activity log ID
 */
function deleteactivitylog(id) {
    if (!id) {
        alert('Activity ID is required');
        return;
    }
    
    var conf = confirm('Are you sure you want to delete this activity?');
    if (!conf) {
        return;
    }
    
    var url = App.getUrl('deleteActivityLog');
    if (!url) {
        url = App.getUrl('deleteAction'); // Fallback
    }
    
    AjaxHelper.post(
        url,
        { id: id, table: 'activity_logs' },
        function(resp) {
            var obj = typeof resp === 'string' ? $.parseJSON(resp) : resp;
            if (obj.status == 1) {
                $('#activity_' + id).remove();
                getallactivities(); // Refresh activities
            } else {
                alert(obj.message || 'Error deleting activity');
            }
        }
    );
}

// Export functions for use in other modules
if (typeof window !== 'undefined') {
    window.getallactivities = getallactivities;
    window.getallnotes = getallnotes;
    window.deleteactivitylog = deleteactivitylog;
}

