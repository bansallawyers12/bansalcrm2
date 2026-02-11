/**
 * Admin Client Detail - Document Rename Module
 * 
 * Handles document renaming functionality for various document types
 * 
 * Dependencies:
 *   - jQuery
 *   - config.js (App object)
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[document-rename.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[document-rename.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[document-rename.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined') {
                    console.log('[document-rename.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// DOCUMENT RENAME HANDLERS
// ============================================================================

jQuery(document).ready(function($){
    
    // ============================================================================
    // ALL DOCUMENTS RENAME FILE NAME HANDLERS
    // ============================================================================
    
    $(document).on('click', '.alldocumnetlist .renamealldoc', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.data('current-html', parent.html());
        var opentime = parent.data('name');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-primary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-danger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-danger', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-primary', function () {
        var parent = $(this).closest('.drow').find('.doc-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = parent.find('.opentime').val();
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }
        var url = App.getUrl('renameAllDoc') || App.getUrl('siteUrl') + '/renamealldoc';
        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: {"filename": opentime, "id": parent.data('id')},
            url: url,
            success: function(result){
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html('<i class="fas fa-file-image"></i> '+obj.filename+'.'+obj.filetype)
                        );
                    $('#grid_'+obj.Id).html(obj.filename+'.'+obj.filetype);
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                }
            }
        });
        return false;
    });

    // ============================================================================
    // RENAME CHECKLIST NAME FOR ALL DOCUMENTS
    // ============================================================================
    
    $(document).on('click', '.alldocumnetlist .renamechecklist', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        parent.data('current-html', parent.html());
        var opentime = parent.data('personalchecklistname');
        parent.empty().append(
            $('<input style="display: inline-block;width: auto;" class="form-control opentime" type="text">').prop('value', opentime),
            $('<button class="btn btn-personalprimary btn-sm mb-1"><i class="fas fa-check"></i></button>'),
            $('<button class="btn btn-personaldanger btn-sm mb-1"><i class="far fa-trash-alt"></i></button>')
        );
        return false;
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-personaldanger', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        var hourid = parent.data('id');
        if (hourid) {
            parent.html(parent.data('current-html'));
        } else {
            parent.remove();
        }
    });

    $(document).on('click', '.alldocumnetlist .drow .btn-personalprimary', function () {
        var parent = $(this).closest('.drow').find('.personalchecklist-row');
        parent.find('.opentime').removeClass('is-invalid');
        parent.find('.invalid-feedback').remove();
        var opentime = parent.find('.opentime').val();
        if (!opentime) {
            parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
            parent.append($("<div class='invalid-feedback'>This field is required</div>"));
            return false;
        }
        var url = App.getUrl('renameChecklistDoc') || App.getUrl('siteUrl') + '/renamechecklistdoc';
        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': App.getCsrf()},
            data: {"checklist": opentime, "id": parent.data('id')},
            url: url,
            success: function(result){
                var obj = typeof result === 'string' ? JSON.parse(result) : result;
                if (obj.status) {
                    parent.empty()
                        .data('id', obj.Id)
                        .data('name', opentime)
                        .append(
                            $('<span>').html(obj.checklist)
                        );
                    $('#grid_'+obj.Id).html(obj.checklist);
                } else {
                    parent.find('.opentime').addClass('is-invalid').css({ 'background-image': 'none', 'padding-right': '0.75em' });
                    parent.append($('<div class="invalid-feedback">' + obj.message + '</div>'));
                }
            }
        });
        return false;
    });

    console.log('[document-rename.js] Document rename handlers initialized');
});

})(); // End async wrapper
