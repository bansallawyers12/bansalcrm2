/**
 * Client detail — Schedule Follow-up modal (Add follow-up icon).
 */
'use strict';

(function () {
    var scheduleFp = null;
    var currentScheduleDateStr = '';
    var selectedSlot = null;

    var slotsRequestSeq = 0;

    function hasConsultantSelected() {
        return $('input[name="consultant"]:checked').length > 0;
    }

    function slotsEndpointUrl() {
        return (typeof App !== 'undefined' && App.getUrl && App.getUrl('scheduleFollowupSlots'))
            ? App.getUrl('scheduleFollowupSlots')
            : '';
    }

    function setSlotsEmptyState(title, hint) {
        $('#scheduleSlotsEmptyTitle').text(title);
        $('#scheduleSlotsEmptyHint').text(hint);
    }

    function renderSlots(dateStr) {
        var $empty = $('#scheduleSlotsEmpty');
        var $wrap = $('#scheduleSlotsButtons');
        selectedSlot = null;
        $('#schedule_followup_datetime').val('');
        $wrap.empty().addClass('d-none');

        if (!dateStr) {
            setSlotsEmptyState('Pick a date', 'Choose a date on the calendar.');
            $empty.removeClass('d-none');
            return;
        }

        if (!hasConsultantSelected()) {
            setSlotsEmptyState('Select a consultant', 'Pick a consultant above, then choose a time for the selected date.');
            $empty.removeClass('d-none');
            return;
        }

        var endpoint = slotsEndpointUrl();
        if (!endpoint) {
            setSlotsEmptyState('Configuration error', 'Slot availability URL is missing.');
            $empty.removeClass('d-none');
            return;
        }

        slotsRequestSeq += 1;
        var seq = slotsRequestSeq;
        setSlotsEmptyState('Loading slots…', 'Fetching availability for this consultant.');
        $empty.removeClass('d-none');

        $.ajax({
            url: endpoint,
            method: 'GET',
            dataType: 'json',
            data: {
                consultant: $('input[name="consultant"]:checked').val(),
                date: dateStr,
                service: $('input[name="service"]:checked').val() || 'free'
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).done(function (res) {
            if (seq !== slotsRequestSeq) {
                return;
            }
            var slots = res && Array.isArray(res.slots) ? res.slots : [];
            if (!slots.length) {
                setSlotsEmptyState(
                    'No available slots',
                    'This date may be outside working hours or blocked — choose another day.'
                );
                return;
            }
            $empty.addClass('d-none');
            $wrap.removeClass('d-none');
            slots.forEach(function (t) {
                var btn = $('<button type="button" class="schedule-slot-btn"></button>').text(formatSlotLabel(t));
                btn.attr('data-slot', t);
                btn.on('click', function () {
                    $wrap.find('.schedule-slot-btn').removeClass('is-active');
                    btn.addClass('is-active');
                    selectedSlot = t;
                    syncFollowupDatetimeHidden();
                });
                $wrap.append(btn);
            });
        }).fail(function () {
            if (seq !== slotsRequestSeq) {
                return;
            }
            setSlotsEmptyState('Could not load slots', 'Check your connection and try again.');
        });
    }

    function formatSlotLabel(t) {
        var parts = t.split(':');
        var h = parseInt(parts[0], 10);
        var m = parts[1];
        var ampm = h >= 12 ? 'PM' : 'AM';
        var h12 = h % 12;
        if (h12 === 0) {
            h12 = 12;
        }
        return h12 + ':' + m + ' ' + ampm;
    }

    function syncFollowupDatetimeHidden() {
        if (hasConsultantSelected() && currentScheduleDateStr && selectedSlot) {
            $('#schedule_followup_datetime').val(currentScheduleDateStr + ' ' + selectedSlot + ':00');
        }
    }

    function initFlatpickr() {
        if (typeof flatpickr === 'undefined') {
            console.warn('schedule-followup: flatpickr not loaded');
            return;
        }
        if (scheduleFp) {
            return;
        }
        var el = document.getElementById('scheduleFollowupFlatpickr');
        if (!el) {
            return;
        }
        scheduleFp = flatpickr(el, {
            inline: true,
            disableMobile: true,
            minDate: 'today',
            dateFormat: 'Y-m-d',
            defaultDate: new Date(),
            locale: { firstDayOfWeek: 0 },
            onChange: function (selectedDates, dateStr) {
                currentScheduleDateStr = dateStr;
                $('#schedule_followup_date_hidden').val(dateStr);
                renderSlots(dateStr);
            }
        });
        if (scheduleFp.selectedDates && scheduleFp.selectedDates.length) {
            currentScheduleDateStr = scheduleFp.formatDate(scheduleFp.selectedDates[0], 'Y-m-d');
        }
        $('#schedule_followup_date_hidden').val(currentScheduleDateStr);
        renderSlots(currentScheduleDateStr);
    }

    function resetFormDefaults() {
        $('#scheduleFollowupFormErrors').addClass('d-none').text('');
        $('#schedule_followup_type').val('Education');
        $('input[name="service"][value="free"]').prop('checked', true);
        $('input[name="consultant"]').prop('checked', false);
        $('#schedule_followup_detail').val('');
        $('#schedule_preferred_language').val('');
        $('#schedule_details_of_enquiry').val('');
        $('#schedule_followup_datetime').val('');
        selectedSlot = null;

        if (scheduleFp) {
            scheduleFp.setDate(new Date(), true);
        } else {
            renderSlots(currentScheduleDateStr);
        }
    }

    function showFormError(msg) {
        var $box = $('#scheduleFollowupFormErrors');
        $box.removeClass('d-none').text(msg || 'Please check the form and try again.');
    }

    function openScheduleModal(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        var modalEl = document.getElementById('scheduleFollowupModal');
        if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            return;
        }
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    jQuery(document).ready(function ($) {
        $(document).on('click', '.client-detail-add-followup-btn', openScheduleModal);

        $('#scheduleFollowupModal').on('shown.bs.modal', function () {
            initFlatpickr();
            resetFormDefaults();
        });

        $('#scheduleFollowupModal').on('change', 'input[name="consultant"], input[name="service"]', function () {
            renderSlots(currentScheduleDateStr);
        });

        $('#scheduleFollowupSubmitBtn').on('click', function () {
            var $btn = $(this);
            if (!$('#schedule_followup_detail').val()) {
                showFormError('Please select follow-up details.');
                return;
            }
            if (!$('#schedule_preferred_language').val()) {
                showFormError('Please select preferred language.');
                return;
            }
            if (!$('#schedule_details_of_enquiry').val().trim()) {
                showFormError('Please enter details of enquiry.');
                return;
            }
            if (!$('input[name="consultant"]:checked').val()) {
                showFormError('Please select a consultant.');
                return;
            }
            if (!currentScheduleDateStr || !selectedSlot) {
                showFormError('Please select a date and an available time slot.');
                return;
            }
            syncFollowupDatetimeHidden();

            if (!$('#schedule_followup_datetime').val()) {
                showFormError('Follow-up date & time is required.');
                return;
            }

            var url = (typeof App !== 'undefined' && App.getUrl && App.getUrl('scheduleFollowup'))
                ? App.getUrl('scheduleFollowup')
                : '';

            $btn.prop('disabled', true);
            $('#scheduleFollowupFormErrors').addClass('d-none').text('');

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': (typeof App !== 'undefined' && App.getCsrf) ? App.getCsrf() : $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: $('#scheduleFollowupForm').serialize(),
                success: function (res) {
                    var obj = typeof res === 'string' ? $.parseJSON(res) : res;
                    if (obj && obj.success) {
                        var modalEl = document.getElementById('scheduleFollowupModal');
                        if (modalEl) {
                            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                        }
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({ title: 'Scheduled', message: obj.message || 'Follow-up saved.', position: 'topRight' });
                        }
                        if (typeof getallactivities === 'function') {
                            getallactivities();
                        }
                        if (typeof getallnotes === 'function') {
                            getallnotes();
                        }
                    } else {
                        showFormError((obj && obj.message) ? obj.message : 'Could not save follow-up.');
                    }
                },
                error: function (xhr) {
                    var msg = 'Could not save follow-up.';
                    try {
                        var json = xhr.responseJSON;
                        if (json && json.message) {
                            msg = json.message;
                        }
                    } catch (ignore) {}
                    showFormError(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });
    });
})();
