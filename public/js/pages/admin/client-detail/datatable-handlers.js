/**
 * Admin Client Detail - DataTable Handlers Module
 * 
 * Handles DataTable initialization and checklist selection
 * 
 * Dependencies:
 *   - jQuery
 *   - DataTables
 */

'use strict';

// ============================================================================
// ASYNC WRAPPER - Wait for vendor libraries before initialization
// ============================================================================

(async function() {
    // Wait for vendor libraries to be ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        console.log('[datatable-handlers.js] Waiting for vendorLibsReady promise...');
        await window.vendorLibsReady;
        console.log('[datatable-handlers.js] Vendor libraries ready!');
    } else {
        // Fallback: Poll for vendor libraries
        console.log('[datatable-handlers.js] vendorLibsReady not found, polling for libraries...');
        await new Promise((resolve) => {
            const check = () => {
                if (typeof $ !== 'undefined' && 
                    typeof $.fn.DataTable !== 'undefined') {
                    console.log('[datatable-handlers.js] All vendor libraries detected!');
                    resolve();
                } else {
                    setTimeout(check, 50);
                }
            };
            check();
        });
    }

// ============================================================================
// DATATABLE INITIALIZATION
// ============================================================================

jQuery(document).ready(function($){
    
    // Initialize DataTable for the checklist table
    let selectedChecklists = [];
    if($('#mychecklist-datatable').length) {
        let checklistTable = $('#mychecklist-datatable').DataTable({
            "paging": true,
            "pageLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "dom": 'lfrtip',
            "drawCallback": function(settings) {
                let api = this.api();
                api.rows().every(function() {
                    let row = this.node();
                    let checkbox = $(row).find('input[name="checklistfile[]"]');
                    let checklistId = checkbox.val();
                    if (selectedChecklists.includes(checklistId)) {
                        checkbox.prop('checked', true);
                    } else {
                        checkbox.prop('checked', false);
                    }
                });
            }
        });
    }

    // ============================================================================
    // CHECKLIST FILE SELECTION HANDLER
    // ============================================================================
    
    $(document).on('change', 'input[name="checklistfile[]"]', function() {
        var checklistId = $(this).val();
        if ($(this).is(':checked')) {
            if (!selectedChecklists.includes(checklistId)) {
                selectedChecklists.push(checklistId);
            }
        } else {
            selectedChecklists = selectedChecklists.filter(id => id !== checklistId);
        }
    });

    console.log('[datatable-handlers.js] DataTable handlers initialized');
});

})(); // End async wrapper
