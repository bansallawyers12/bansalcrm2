/**
 * Activity Handlers Module
 * 
 * Functions for managing activities/logs across detail pages
 * 
 * Usage:
 *   getallactivities()
 *   applyActivitiesResponse(response)
 *   deleteactivitylog(id)
 */

'use strict';

/**
 * Apply activities API response to the DOM.
 * Prefers server-rendered HTML; falls back to legacy JSON data builder.
 * @param {object|string} response
 */
function applyActivitiesResponse(response) {
    var ress = typeof response === 'string' ? JSON.parse(response) : response;
    var html = '';

    if (ress && ress.html) {
        html = ress.html;
    } else if (ress && ress.data && ress.data.length > 0) {
        $.each(ress.data, function(k, v) {
            html += buildLegacyActivityItemHtml(v);
        });
    }

    if ($('.activities').length) {
        $('.activities').html(html);
    } else if ($('.activitiesdata').length) {
        $('.activitiesdata').html(html);
    }
}

/**
 * Legacy fallback when server HTML is unavailable.
 * @param {object} v
 * @returns {string}
 */
function buildLegacyActivityItemHtml(v) {
    var activityId = v.activity_id || v.id || '';
    var pinHtml = (v.pin == 1)
        ? '<div class="pined_note">' + crmIcon('thumbtack', 'solid', { attrs: { style: 'font-size: 12px;color: #6777ef;' } }) + '</div>'
        : '';
    var pinLabel = (v.pin == 1) ? 'UnPin' : 'Pin';
    var canDelete = App.getPageConfig && App.getPageConfig('canDeleteActivityLog');
    var deleteHtml = canDelete
        ? '<a data-id="' + activityId + '" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;">Delete</a>'
        : '';

    var html = '<div class="activity" id="activity_' + activityId + '">';
    html += '<div class="activity-icon bg-primary text-white"><span>' + (v.createdname || '') + '</span></div>';
    html += '<div class="activity-detail">';
    html += '<div class="activity-head">';
    html += '<div class="activity-title"><p><b>' + (v.name || '') + '</b> ' + (v.subject || '') + '</p></div>';
    html += '<div class="activity-head-actions">';
    html += '<div class="activity-date"><span class="text-job">' + (v.date || '') + '</span></div>';
    html += '<div class="activity-actions">';
    html += pinHtml;
    html += '<div class="dropdown d-inline dropdown_ellipsis_icon">';
    html += '<a class="dropdown-toggle" href="javascript:;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' + crmIcon('ellipsis-v') + '</a>';
    html += '<div class="dropdown-menu">';
    html += deleteHtml;
    html += '<a data-id="' + activityId + '" class="dropdown-item pinactivitylog" href="javascript:;">' + pinLabel + '</a>';
    html += '</div></div></div></div></div>';
    if (v.message != null) {
        html += '<div class="activity-content-card"><div class="activity-content-body"><p>' + v.message + '</p></div></div>';
    }
    if (v.followup_date) {
        html += '<p>' + v.followup_date + '</p>';
    }
    if (v.task_group) {
        html += '<p>' + v.task_group + '</p>';
    }
    html += '</div></div>';
    return html;
}

/**
 * Get all activities for the current page
 * Fetches activities based on PageConfig settings
 * @param {function} [onSuccess] - Optional callback called after activities are refreshed
 */
function getallactivities(onSuccess) {
    var activityId = App.getPageConfig('clientId') || 
                     App.getPageConfig('productId') || 
                     App.getPageConfig('userId') || 
                     App.getPageConfig('agentId') || 
                     App.getPageConfig('partnerId');
    
    if (!activityId) {
        console.warn('Activity ID not found in PageConfig');
        if (typeof onSuccess === 'function') onSuccess();
        return;
    }
    
    var url = App.getUrl('getActivities') || App.getUrl('siteUrl') + '/get-activities';
    if (!url) {
        console.error('getActivities URL not configured');
        if (typeof onSuccess === 'function') onSuccess();
        return;
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        data: { id: activityId },
        success: function(responses) {
            applyActivitiesResponse(responses);
            if (typeof onSuccess === 'function') onSuccess();
        },
        error: function(xhr, status, err) {
            console.warn('Failed to refresh activities:', status, err);
            if (typeof onSuccess === 'function') onSuccess();
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
        toastMsg('Activity ID is required', 'warning');
        return;
    }

    crmConfirm('Are you sure you want to delete this activity?').then(function (ok) {
        if (!ok) {
            return;
        }

        var url = App.getUrl('deleteActivityLog');
        if (!url) {
            url = App.getUrl('deleteAction');
        }

        AjaxHelper.post(
            url,
            { id: id, table: 'activity_logs' },
            function(resp) {
                var obj = typeof resp === 'string' ? $.parseJSON(resp) : resp;
                if (obj.status == 1) {
                    $('#activity_' + id).remove();
                    getallactivities();
                } else {
                    toastMsg(obj.message || 'Error deleting activity', 'error');
                }
            }
        );
    });
}

// Export functions for use in other modules
if (typeof window !== 'undefined') {
    window.getallactivities = getallactivities;
    window.getallnotes = getallnotes;
    window.deleteactivitylog = deleteactivitylog;
    window.applyActivitiesResponse = applyActivitiesResponse;
}
