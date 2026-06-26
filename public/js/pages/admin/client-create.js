/**
 * Client Create Page JavaScript
 * Handles form validation, phone/email checking, and Tom Select initialization
 */

(function($) {
    'use strict';

    // Get URLs from AppConfig (set in blade template) or use defaults
    const checkClientExistUrl = (window.AppConfig && window.AppConfig.urls && window.AppConfig.urls.checkClientExist) || '/checkclientexist';
    const getRecipientsUrl = (window.AppConfig && window.AppConfig.urls && window.AppConfig.urls.getRecipients) || '/clients/get-recipients';

    var relatedFilesInitOptions = {
        url: getRecipientsUrl,
        minimumInputLength: 1
    };

    /**
     * Initialize phone number validation
     */
    function initPhoneValidation() {
        $('#checkphone').on('blur', function() {
            var phoneValue = $(this).val();
            if (phoneValue != '') {
                $.ajax({
                    url: checkClientExistUrl,
                    type: 'GET',
                    data: {
                        vl: phoneValue,
                        type: 'phone'
                    },
                    success: function(res) {
                        if (res == 1) {
                            alert('Phone number is already exist in our record.');
                        }
                    },
                    error: function() {
                        console.error('Error checking phone number');
                    }
                });
            }
        });
    }

    /**
     * Initialize email validation
     */
    function initEmailValidation() {
        $('#checkemail').on('blur', function() {
            var emailValue = $(this).val();
            if (emailValue != '') {
                $.ajax({
                    url: checkClientExistUrl,
                    type: 'GET',
                    data: {
                        vl: emailValue,
                        type: 'email'
                    },
                    success: function(res) {
                        if (res == 1) {
                            alert('Email is already exist in our record.');
                        }
                    },
                    error: function() {
                        console.error('Error checking email');
                    }
                });
            }
        });
    }

    function initRelatedFilesTomSelect() {
        if (typeof window.RecipientSelect === 'undefined') {
            return;
        }
        if (typeof window.RecipientSelect.ensureRelatedFiles === 'function') {
            window.RecipientSelect.ensureRelatedFiles(relatedFilesInitOptions);
            return;
        }
        if (typeof window.RecipientSelect.initRelatedFiles === 'function') {
            window.RecipientSelect.initRelatedFiles(relatedFilesInitOptions);
        }
    }

    /**
     * Initialize Tom Select on client form static fields (Phase 2+).
     */
    function initClientFormTomSelects() {
        if (typeof waitForTomSelect !== 'function' || typeof initTomSelect !== 'function') {
            return;
        }

        waitForTomSelect().then(function () {
            var fullWidth = { width: '100%' };

            initTomSelect('#visa_type', Object.assign({
                placeholder: '- Select Visa Type -',
                allowClear: true
            }, fullWidth));

            initTomSelect('select[name="country_passport"]', fullWidth);

            initTomSelect('#country_select', { width: '200px' });

            initTomSelect('select[name="service"]', Object.assign({
                placeholder: '- Select Lead Service -',
                allowClear: true
            }, fullWidth));

            initTomSelect('#assign_to', Object.assign({}, fullWidth, {
                closeAfterSelect: false
            }));

            if (document.querySelector('#tag')) {
                initTomSelect('#tag', Object.assign({}, fullWidth, {
                    closeAfterSelect: false
                }));
            }

            if (document.querySelector('#lead_source')) {
                initTomSelect('#lead_source', Object.assign({
                    allowClear: true
                }, fullWidth));
                syncSubagentVisibility();
            }

            if (document.querySelector('select[name="subagent"]')) {
                initTomSelect('select[name="subagent"]', Object.assign({
                    allowClear: true
                }, fullWidth));
            }
        });
    }

    function getLeadSourceValue() {
        var el = document.querySelector('#lead_source');
        if (!el) {
            return '';
        }
        if (el.tomselect) {
            return el.tomselect.getValue() || '';
        }
        return el.value || '';
    }

    function syncSubagentVisibility() {
        if (getLeadSourceValue() === 'Sub Agent') {
            $('.is_subagent').show();
        } else {
            $('.is_subagent').hide();
        }
    }

    /**
     * Show/hide subagent field based on source selection
     */
    function initSubagentToggle() {
        $(document).on('change', '#lead_source', function() {
            syncSubagentVisibility();
        });

        syncSubagentVisibility();
    }

    /**
     * Initialize all functionality when document is ready
     */
    $(document).ready(function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        initPhoneValidation();
        initEmailValidation();

        if (typeof waitForRecipientSelect === 'function') {
            waitForRecipientSelect().then(function () {
                initRelatedFilesTomSelect();
            });
        } else {
            initRelatedFilesTomSelect();
        }
        $(window).on('load', function() {
            initRelatedFilesTomSelect();
        });

        initClientFormTomSelects();
        initSubagentToggle();
    });

})(jQuery);
