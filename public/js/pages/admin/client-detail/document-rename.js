/**
 * Admin Client Detail - Document Rename Module
 *
 * Handles document and checklist renaming in the Documents tab.
 *
 * Dependencies:
 *   - jQuery (sync in layout head)
 *   - config.js (App object)
 *   - crmIcon (crm-icon.js)
 */

'use strict';

(function () {
    var handlersRegistered = false;
    var NS = '.documentRename';

    function $jq() {
        return typeof jQuery !== 'undefined' ? jQuery : null;
    }

    function renameToast(message, type) {
        if (typeof window.toastMsg === 'function') {
            window.toastMsg(message, type);
        } else if (message) {
            alert(message);
        }
    }

    function createActionButton(className, iconName, iconStyle) {
        return $('<button type="button" class="btn ' + className + ' btn-sm">' +
            crmIcon(iconName, iconStyle) + '</button>');
    }

    function appendRenameEditControls(parent, input, saveBtn, cancelBtn) {
        var actions = $('<span class="document-rename-actions" role="group" aria-label="Rename actions"></span>')
            .append(saveBtn, cancelBtn);
        parent.addClass('document-rename-editing')
            .empty()
            .append(input, actions);
    }

    function readPersonalChecklistName(parent) {
        var value = parent.data('personalchecklistname');
        if (value === undefined || value === null || value === '') {
            value = parent.attr('data-personalchecklistname') || '';
        }
        return value;
    }

    function cancelChecklistRename(parent) {
        var $ = $jq();
        parent = $(parent);
        var savedHtml = parent.data('current-html');
        if (parent.data('id') && savedHtml !== undefined) {
            parent.html(savedHtml);
        } else if (parent.data('id')) {
            parent.empty().append($('<span>').text(readPersonalChecklistName(parent)));
        } else {
            parent.remove();
        }
    }

    function saveChecklistRename(parent) {
        var $ = $jq();
        parent = $(parent);
        var row = parent.closest('.drow');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = $.trim(parent.find('.opentime').val());
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({
                'background-image': 'none',
                'padding-right': '0.75em'
            });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return;
        }

        var url = App.getUrl('renameChecklistDoc') || App.getUrl('siteUrl') + '/renamechecklistdoc';
        $.ajax({
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf() },
            data: { checklist: opentime, id: parent.data('id') },
            url: url,
            success: function (result) {
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    var checklistName = obj.checklist || opentime;
                    var docId = obj.Id;
                    parent.empty()
                        .attr('data-personalchecklistname', checklistName)
                        .data('personalchecklistname', checklistName)
                        .data('id', docId)
                        .append($('<span>').text(checklistName));
                    if (row.length) {
                        row.attr('data-checklist-name', checklistName);
                    }
                    $('#grid_' + docId).html(checklistName);
                    renameToast(obj.data || obj.message || 'Checklist saved successfully', 'success');
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({
                        'background-image': 'none',
                        'padding-right': '0.75em'
                    });
                    parent.append($('<div class="invalid-feedback">' + (obj.message || 'Please try again') + '</div>'));
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                    'Unable to rename checklist. Please try again.';
                renameToast(msg, 'error');
            }
        });
    }

    function cancelFileRename(parent) {
        var $ = $jq();
        parent = $(parent);
        if (parent.data('id')) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    }

    function saveFileRename(parent) {
        var $ = $jq();
        parent = $(parent);
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = $.trim(parent.find('.opentime').val());
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({
                'background-image': 'none',
                'padding-right': '0.75em'
            });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return;
        }

        var url = App.getUrl('renameAllDoc') || App.getUrl('siteUrl') + '/renamealldoc';
        $.ajax({
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': App.getCsrf() },
            data: { filename: opentime, id: parent.data('id') },
            url: url,
            success: function (result) {
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .attr('data-name', opentime)
                        .append(
                            $('<span>').html(crmIcon('file-image') + ' ' + obj.filename + '.' + obj.filetype)
                        );
                    $('#grid_' + obj.Id).html(obj.filename + '.' + obj.filetype);
                    renameToast(obj.data || obj.message || 'Document saved successfully', 'success');
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({
                        'background-image': 'none',
                        'padding-right': '0.75em'
                    });
                    parent.append($('<div class="invalid-feedback">' + (obj.message || 'Please try again') + '</div>'));
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ||
                    'Unable to rename file. Please try again.';
                renameToast(msg, 'error');
            }
        });
    }

    function bindChecklistEditActions(parent, saveBtn, cancelBtn) {
        var $ = $jq();
        cancelBtn.off(NS).on('click' + NS, function (e) {
            e.preventDefault();
            e.stopPropagation();
            cancelChecklistRename(parent);
            return false;
        });
        saveBtn.off(NS).on('click' + NS, function (e) {
            e.preventDefault();
            e.stopPropagation();
            saveChecklistRename(parent);
            return false;
        });
    }

    function bindFileEditActions(parent, saveBtn, cancelBtn) {
        var $ = $jq();
        cancelBtn.off(NS).on('click' + NS, function (e) {
            e.preventDefault();
            e.stopPropagation();
            cancelFileRename(parent);
            return false;
        });
        saveBtn.off(NS).on('click' + NS, function (e) {
            e.preventDefault();
            e.stopPropagation();
            saveFileRename(parent);
            return false;
        });
    }

    function enterChecklistEditMode(parent) {
        var $ = $jq();
        if (!$) {
            return;
        }
        registerHandlers();
        parent = $(parent);
        if (!parent.length) {
            return;
        }
        parent.data('current-html', parent.html());
        var currentName = readPersonalChecklistName(parent);
        var input = $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">')
            .prop('value', currentName);
        var saveBtn = createActionButton('btn-primary checklist-rename-save', 'check');
        var cancelBtn = createActionButton('btn-danger checklist-rename-cancel', 'trash-alt', 'regular');
        appendRenameEditControls(parent, input, saveBtn, cancelBtn);
        bindChecklistEditActions(parent, saveBtn, cancelBtn);
        if (typeof window.refreshCrmIcons === 'function') {
            window.refreshCrmIcons(parent[0]);
        }
        input.trigger('focus');
    }

    function enterFileEditMode(parent) {
        var $ = $jq();
        if (!$) {
            return;
        }
        registerHandlers();
        parent = $(parent);
        if (!parent.length) {
            return;
        }
        parent.data('current-html', parent.html());
        var currentName = parent.data('name');
        if (currentName === undefined || currentName === null || currentName === '') {
            currentName = parent.attr('data-name') || '';
        }
        var input = $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">')
            .prop('value', currentName);
        var saveBtn = createActionButton('btn-primary file-rename-save', 'check');
        var cancelBtn = createActionButton('btn-danger file-rename-cancel', 'trash-alt', 'regular');
        appendRenameEditControls(parent, input, saveBtn, cancelBtn);
        bindFileEditActions(parent, saveBtn, cancelBtn);
        if (typeof window.refreshCrmIcons === 'function') {
            window.refreshCrmIcons(parent[0]);
        }
        input.trigger('focus');
    }

    function registerHandlers() {
        if (handlersRegistered) {
            return true;
        }
        var $ = $jq();
        if (!$) {
            return false;
        }

        $(document).on('click' + NS, '.alldocumnetlist .renamealldoc', function () {
            enterFileEditMode($(this).closest('.drow').find('.doc-row'));
            return false;
        });

        $(document).on('click' + NS, '.alldocumnetlist .renamechecklist', function () {
            enterChecklistEditMode($(this).closest('.drow').find('.personalchecklist-row'));
            return false;
        });

        handlersRegistered = true;
        return true;
    }

    window.DocumentRename = {
        enterChecklistEditMode: enterChecklistEditMode,
        enterFileEditMode: enterFileEditMode,
        registerHandlers: registerHandlers
    };

    function init() {
        if (registerHandlers()) {
            console.log('[document-rename.js] Document rename handlers initialized');
        }
    }

    if ($jq()) {
        init();
        $jq()(document).ready(init);
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            init();
            if ($jq()) {
                $jq()(document).ready(init);
            }
        });
    }
})();
