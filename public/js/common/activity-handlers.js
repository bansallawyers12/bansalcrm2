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
 * @param {{append?: boolean}} [options]
 */
function applyActivitiesResponse(response, options) {
    var ress = typeof response === 'string' ? JSON.parse(response) : response;
    var html = '';
    var append = !!(options && options.append);

    if (ress && Object.prototype.hasOwnProperty.call(ress, 'html') && ress.html != null) {
        html = ress.html;
    } else if (ress && ress.data && ress.data.length > 0) {
        $.each(ress.data, function(k, v) {
            html += buildLegacyActivityItemHtml(v);
        });
    }

    if (!append && !String(html).trim()) {
        html = '<h4>No Record Found</h4>';
    }

    if ($('.activities').length) {
        if (append) {
            $('.activities').append(html);
        } else {
            $('.activities').html(html);
        }
    } else if ($('.activitiesdata').length) {
        if (append) {
            $('.activitiesdata').append(html);
        } else {
            $('.activitiesdata').html(html);
        }
    }

    syncActivitiesLoadMore(ress);
}

var activitiesLoadMoreInFlight = false;
var activitiesLoadMoreObserver = null;
var activitiesRequestSeq = 0;
var activitiesUserHasScrolled = false;

function activitiesLoadMoreEl() {
    return document.querySelector('.activities-load-more');
}

function activitiesScrollRoot() {
    var el = activitiesLoadMoreEl();
    if (!el) {
        return null;
    }
    var node = el.parentElement;
    while (node && node !== document.body && node !== document.documentElement) {
        var style = window.getComputedStyle(node);
        var overflowY = style.overflowY;
        if ((overflowY === 'auto' || overflowY === 'scroll') && node.scrollHeight > node.clientHeight + 1) {
            return node;
        }
        node = node.parentElement;
    }
    return null;
}

function isActivitiesLoadMoreVisible() {
    var el = activitiesLoadMoreEl();
    if (!el || el.style.display === 'none' || el.offsetParent === null) {
        return false;
    }
    var root = activitiesScrollRoot();
    var elRect = el.getBoundingClientRect();
    if (root) {
        var rootRect = root.getBoundingClientRect();
        return elRect.top < rootRect.bottom && elRect.bottom > rootRect.top;
    }
    return elRect.top < (window.innerHeight || document.documentElement.clientHeight) && elRect.bottom > 0;
}

function requestActivitiesNextPage() {
    var $el = $('.activities-load-more');
    if (!$el.length || activitiesLoadMoreInFlight || $el.is(':hidden')) {
        return;
    }
    var nextPage = parseInt($el.attr('data-next-page'), 10);
    if (!nextPage) {
        return;
    }
    activitiesLoadMoreInFlight = true;
    $el.addClass('is-loading').prop('disabled', true);
    getallactivities(null, { page: nextPage, append: true });
}

function maybeLoadMoreIfVisible() {
    if (!activitiesUserHasScrolled || !isActivitiesLoadMoreVisible()) {
        return;
    }
    requestActivitiesNextPage();
}

function observeActivitiesLoadMore() {
    var el = activitiesLoadMoreEl();
    if (activitiesLoadMoreObserver) {
        activitiesLoadMoreObserver.disconnect();
        activitiesLoadMoreObserver = null;
    }
    if (!el || typeof IntersectionObserver === 'undefined') {
        return;
    }
    activitiesLoadMoreObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting && activitiesUserHasScrolled) {
                requestActivitiesNextPage();
            }
        });
    }, { root: activitiesScrollRoot(), rootMargin: '0px 0px 80px 0px', threshold: 0 });
    activitiesLoadMoreObserver.observe(el);
}

function syncActivitiesLoadMore(ress) {
    var $el = $('.activities-load-more');
    if (!$el.length) {
        return;
    }
    activitiesLoadMoreInFlight = false;
    $el.prop('disabled', false).removeClass('is-loading').text('...');
    if (ress && ress.hasMore) {
        $el.attr('data-next-page', ress.nextPage).show();
        observeActivitiesLoadMore();
    } else {
        $el.removeAttr('data-next-page').hide();
        if (activitiesLoadMoreObserver) {
            activitiesLoadMoreObserver.disconnect();
            activitiesLoadMoreObserver = null;
        }
    }
}

function activityFilterRequestData() {
    var data = {};
    var $form = $('#activitiesFilterForm');
    if (!$form.length) {
        return data;
    }
    data.keyword = $form.find('[name="keyword"]').val() || '';
    data.activity_type = $form.find('[name="activity_type"]').val() || 'all';
    data.date_from = $form.find('[name="date_from"]').val() || '';
    data.date_to = $form.find('[name="date_to"]').val() || '';
    return data;
}

function activitiesFiltersAreActive(data) {
    data = data || activityFilterRequestData();
    var type = data.activity_type || 'all';
    return !!(String(data.keyword || '').trim() || (type && type !== 'all') || data.date_from || data.date_to);
}

function syncActivitiesFilterBadge() {
    var $badge = $('#activities-filters-active');
    if (!$badge.length) {
        return;
    }
    if (activitiesFiltersAreActive()) {
        $badge.show();
    } else {
        $badge.hide();
    }
}

function syncActivitiesFilterUrl() {
    var $form = $('#activitiesFilterForm');
    if (!$form.length || !window.history || typeof window.history.replaceState !== 'function') {
        return;
    }
    var url;
    try {
        url = new URL(window.location.href);
    } catch (e) {
        return;
    }
    ['keyword', 'activity_type', 'date_from', 'date_to'].forEach(function (key) {
        url.searchParams.delete(key);
    });
    var data = activityFilterRequestData();
    if (String(data.keyword || '').trim()) {
        url.searchParams.set('keyword', data.keyword);
    }
    if (data.activity_type && data.activity_type !== 'all') {
        url.searchParams.set('activity_type', data.activity_type);
    }
    if (data.date_from) {
        url.searchParams.set('date_from', data.date_from);
    }
    if (data.date_to) {
        url.searchParams.set('date_to', data.date_to);
    }
    window.history.replaceState({}, '', url.pathname + url.search + url.hash);
}

function applyActivitiesFilters() {
    var $form = $('#activitiesFilterForm');
    if (!$form.length) {
        return;
    }
    syncActivitiesFilterUrl();
    syncActivitiesFilterBadge();
    getallactivities();
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
 * @param {{page?: number, append?: boolean}} [options]
 */
function getallactivities(onSuccess, options) {
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

    options = options || {};
    var page = parseInt(options.page, 10) || 1;
    var append = !!options.append;
    if (!append) {
        activitiesUserHasScrolled = false;
    }
    var data = activityFilterRequestData();
    data.id = activityId;
    data.paginated = 1;
    data.page = page;
    var requestSeq = ++activitiesRequestSeq;
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        data: data,
        success: function(responses) {
            if (requestSeq !== activitiesRequestSeq) {
                return;
            }
            applyActivitiesResponse(responses, { append: append });
            if (typeof onSuccess === 'function') onSuccess();
            maybeLoadMoreIfVisible();
        },
        error: function(xhr, status, err) {
            if (requestSeq !== activitiesRequestSeq) {
                return;
            }
            console.warn('Failed to refresh activities:', status, err);
            activitiesLoadMoreInFlight = false;
            $('.activities-load-more').prop('disabled', false).removeClass('is-loading');
            if (typeof onSuccess === 'function') onSuccess();
        }
    });
}

var notesLoadMoreInFlight = false;
var notesLoadMoreObserver = null;
var notesRequestSeq = 0;
var notesUserHasScrolled = false;

function notesLoadMoreEl() {
    return document.querySelector('.notes-load-more');
}

function notesScrollRoot() {
    var el = notesLoadMoreEl();
    if (!el) {
        return null;
    }
    var node = el.parentElement;
    while (node && node !== document.body && node !== document.documentElement) {
        var style = window.getComputedStyle(node);
        var overflowY = style.overflowY;
        if ((overflowY === 'auto' || overflowY === 'scroll') && node.scrollHeight > node.clientHeight + 1) {
            return node;
        }
        node = node.parentElement;
    }
    return null;
}

function isNotesLoadMoreVisible() {
    var el = notesLoadMoreEl();
    if (!el || el.style.display === 'none' || el.offsetParent === null) {
        return false;
    }
    var root = notesScrollRoot();
    var elRect = el.getBoundingClientRect();
    if (root) {
        var rootRect = root.getBoundingClientRect();
        return elRect.top < rootRect.bottom && elRect.bottom > rootRect.top;
    }
    return elRect.top < (window.innerHeight || document.documentElement.clientHeight) && elRect.bottom > 0;
}

function requestNotesNextPage() {
    var $el = $('.notes-load-more');
    if (!$el.length || notesLoadMoreInFlight || $el.is(':hidden')) {
        return;
    }
    var nextPage = parseInt($el.attr('data-next-page'), 10);
    if (!nextPage) {
        return;
    }
    notesLoadMoreInFlight = true;
    $el.addClass('is-loading').prop('disabled', true);
    getallnotes(null, { page: nextPage, append: true });
}

function maybeLoadMoreNotesIfVisible() {
    if (!notesUserHasScrolled || !isNotesLoadMoreVisible()) {
        return;
    }
    requestNotesNextPage();
}

function observeNotesLoadMore() {
    var el = notesLoadMoreEl();
    if (notesLoadMoreObserver) {
        notesLoadMoreObserver.disconnect();
        notesLoadMoreObserver = null;
    }
    if (!el || typeof IntersectionObserver === 'undefined') {
        return;
    }
    notesLoadMoreObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting && notesUserHasScrolled) {
                requestNotesNextPage();
            }
        });
    }, { root: notesScrollRoot(), rootMargin: '0px 0px 80px 0px', threshold: 0 });
    notesLoadMoreObserver.observe(el);
}

function syncNotesLoadMore(ress) {
    var $el = $('.notes-load-more');
    if (!$el.length) {
        return;
    }
    notesLoadMoreInFlight = false;
    $el.prop('disabled', false).removeClass('is-loading').text('...');
    if (ress && ress.hasMore) {
        $el.attr('data-next-page', ress.nextPage).show();
        observeNotesLoadMore();
    } else {
        $el.removeAttr('data-next-page').hide();
        if (notesLoadMoreObserver) {
            notesLoadMoreObserver.disconnect();
            notesLoadMoreObserver = null;
        }
    }
}

function applyNotesResponse(response, options) {
    var ress = typeof response === 'string' ? JSON.parse(response) : response;
    var html = '';
    var append = !!(options && options.append);

    if (ress && Object.prototype.hasOwnProperty.call(ress, 'html') && ress.html != null) {
        html = ress.html;
    } else if (typeof response === 'string') {
        html = response;
    }

    if (!append && !String(html).trim()) {
        html = '<h4>No Record Found</h4>';
    }

    if ($('.note_term_list').length) {
        if (append) {
            $('.note_term_list').append(html);
        } else {
            $('.note_term_list').html(html);
        }
    }

    if (ress && Object.prototype.hasOwnProperty.call(ress, 'hasMore')) {
        syncNotesLoadMore(ress);
    }
}

/**
 * Get all notes for the current page
 * @param {function} [onSuccess]
 * @param {{page?: number, append?: boolean}} [options]
 */
function getallnotes(onSuccess, options) {
    var clientId = App.getPageConfig('clientId');
    var type = App.getPageConfig('clientType') || 'client';
    
    if (!clientId) {
        console.warn('Client ID not found in PageConfig');
        if (typeof onSuccess === 'function') {
            onSuccess();
        }
        return;
    }
    
    var url = App.getUrl('getNotes') || App.getUrl('siteUrl') + '/get-notes';
    if (!url) {
        console.error('getNotes URL not configured');
        if (typeof onSuccess === 'function') {
            onSuccess();
        }
        return;
    }

    options = options || {};
    var page = parseInt(options.page, 10) || 1;
    var append = !!options.append;
    if (!append) {
        notesUserHasScrolled = false;
    }
    var requestSeq = ++notesRequestSeq;
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        data: { clientid: clientId, type: type, paginated: 1, page: page },
        success: function(responses) {
            if (requestSeq !== notesRequestSeq) {
                return;
            }
            $('.popuploader').hide();
            applyNotesResponse(responses, { append: append });
            if (typeof onSuccess === 'function') {
                onSuccess();
            }
            maybeLoadMoreNotesIfVisible();
        },
        error: function() {
            if (requestSeq !== notesRequestSeq) {
                return;
            }
            notesLoadMoreInFlight = false;
            $('.notes-load-more').prop('disabled', false).removeClass('is-loading');
            if (typeof onSuccess === 'function') {
                onSuccess();
            }
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
    window.applyNotesResponse = applyNotesResponse;
    window.applyActivitiesFilters = applyActivitiesFilters;
}

function bindActivitiesLoadMore() {
    if (typeof jQuery === 'undefined') {
        return;
    }
    jQuery(document).off('click.activitiesLoadMore', '.activities-load-more').on('click.activitiesLoadMore', '.activities-load-more', function () {
        requestActivitiesNextPage();
    });
    if (!window._activitiesLoadMoreScrollBound) {
        window._activitiesLoadMoreScrollBound = true;
        document.addEventListener('scroll', function () {
            activitiesUserHasScrolled = true;
            maybeLoadMoreIfVisible();
        }, { passive: true, capture: true });
    }
    observeActivitiesLoadMore();
}

function bindNotesLoadMore() {
    if (typeof jQuery === 'undefined') {
        return;
    }
    jQuery(document).off('click.notesLoadMore', '.notes-load-more').on('click.notesLoadMore', '.notes-load-more', function () {
        requestNotesNextPage();
    });
    if (!window._notesLoadMoreScrollBound) {
        window._notesLoadMoreScrollBound = true;
        document.addEventListener('scroll', function () {
            notesUserHasScrolled = true;
            maybeLoadMoreNotesIfVisible();
        }, { passive: true, capture: true });
    }
    observeNotesLoadMore();
}

if (typeof jQuery !== 'undefined') {
    bindActivitiesLoadMore();
    bindNotesLoadMore();
} else if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function () {
        bindActivitiesLoadMore();
        bindNotesLoadMore();
    });
}
