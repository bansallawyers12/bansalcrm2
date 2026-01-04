/**
 * Client Create Page JavaScript
 * Handles form validation, phone/email checking, and Select2 initialization
 */

(function($) {
    'use strict';

    // Get URLs from data attributes or use defaults
    const checkClientExistUrl = $('#create-client-form').data('check-url') || '/checkclientexist';
    const getRecipientsUrl = $('#create-client-form').data('recipients-url') || '/clients/get-recipients';

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

    /**
     * Format Select2 repository result
     */
    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div class='ag-flex ag-align-start'>" +
                    "<div class='ag-flex ag-flex-column col-hr-1'>" +
                        "<div class='ag-flex'>" +
                            "<span class='select2-result-repository__title text-semi-bold'></span>&nbsp;" +
                        "</div>" +
                        "<div class='ag-flex ag-align-center'>" +
                            "<small class='select2-result-repository__description'></small>" +
                        "</div>" +
                    "</div>" +
                "</div>" +
                "<div class='ag-flex ag-flex-column ag-align-end'>" +
                    "<span class='ui label yellow select2-result-repository__statistics'></span>" +
                "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.name);
        $container.find(".select2-result-repository__description").text(repo.email);
        $container.find(".select2-result-repository__statistics").append(repo.status);

        return $container;
    }

    /**
     * Format Select2 repository selection
     */
    function formatRepoSelection(repo) {
        return repo.name || repo.text;
    }

    /**
     * Initialize Select2 for related files
     */
    function initRelatedFilesSelect2() {
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('Select2 is not loaded');
            return;
        }

        $('.js-data-example-ajaxcc').select2({
            multiple: true,
            closeOnSelect: false,
            minimumInputLength: 1,
            ajax: {
                url: getRecipientsUrl,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data.items || []
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
    }

    /**
     * Show/hide subagent field based on source selection
     */
    function initSubagentToggle() {
        $('#lead_source').on('change', function() {
            if ($(this).val() == 'Sub Agent') {
                $('.is_subagent').show();
            } else {
                $('.is_subagent').hide();
            }
        });

        // Trigger on page load if source is already Sub Agent
        if ($('#lead_source').val() == 'Sub Agent') {
            $('.is_subagent').show();
        }
    }

    /**
     * Initialize all functionality when document is ready
     */
    $(document).ready(function() {
        // Wait for jQuery and Select2 to be available
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }

        // Initialize phone validation
        initPhoneValidation();

        // Initialize email validation
        initEmailValidation();

        // Initialize Select2 for related files
        initRelatedFilesSelect2();

        // Initialize subagent toggle
        initSubagentToggle();
    });

})(jQuery);

