(function () {
    'use strict';

    var root = document.getElementById('my-day-file-time');
    if (!root) {
        return;
    }

    var csrf = root.getAttribute('data-csrf') || '';
    var diaryUrl = root.getAttribute('data-diary-url');
    var logUrl = root.getAttribute('data-log-url');
    var copyUrl = root.getAttribute('data-copy-url');
    var saveUrl = root.getAttribute('data-save-url');
    var searchUrl = root.getAttribute('data-search-url');
    var sessionUpdateBase = root.getAttribute('data-session-update-base');
    var autoSessions = [];

    function fetchJson(url, options) {
        options = options || {};
        options.credentials = 'same-origin';
        options.headers = Object.assign({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
        }, options.headers || {});
        return fetch(url, options).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (data) {
                data = data || {};
                data._ok = res.ok;
                return data;
            });
        }).catch(function () { return { _ok: false }; });
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttr(str) {
        return escapeHtml(str).replace(/'/g, '&#39;');
    }

    /** Link only the client/college ref when a detail URL is present; keep the rest plain text. */
    function formatRef(item) {
        var ref = escapeHtml(item.ref || '—');
        if (!item.url) {
            return ref;
        }

        return '<a class="my-day-diary-ref" href="' + escapeAttr(item.url) + '">' + ref + '</a>';
    }

    function renderList(selector, items, renderer, emptyText) {
        var el = root.querySelector(selector);
        if (!el) {
            return;
        }
        if (!items || !items.length) {
            el.innerHTML = '<li class="my-day-diary-empty">' + escapeHtml(emptyText || 'None') + '</li>';
            return;
        }
        el.innerHTML = items.map(renderer).join('');
    }

    function renderDiary(data) {
        var hours = (data.hours && data.hours.label) ? data.hours.label : '—';
        var hoursEl = root.querySelector('[data-hours-label]');
        if (hoursEl) {
            hoursEl.textContent = hours;
        }

        var crm = (data.crm_events && data.crm_events.items) || [];
        renderList('[data-crm-list]', crm, function (item) {
            var chip = item.minutes ? '<span class="my-day-diary-chip">' + escapeHtml(item.minutes) + 'm</span>' : '';
            var line = formatRef(item) + ' · ' + escapeHtml(item.kind) + ' · ' +
                escapeHtml(item.title) + ' · ' + escapeHtml(item.time || '') + chip;
            var bodyHtml = '';
            if (item.body) {
                bodyHtml = '<div class="my-day-crm-body is-collapsed">' +
                    '<div class="my-day-crm-body-text">' + escapeHtml(item.body) + '</div>' +
                    '<button type="button" class="my-day-crm-show-more" aria-expanded="false">Show more</button>' +
                    '</div>';
            }
            return '<li><div class="my-day-diary-crm-main"><div class="my-day-diary-crm-line">' + line +
                '</div>' + bodyHtml + '</div></li>';
        }, 'No CRM writes yet today');

        var moreEl = root.querySelector('[data-crm-more]');
        if (moreEl) {
            if (data.crm_events && data.crm_events.more > 0) {
                moreEl.textContent = 'and ' + data.crm_events.more + ' more';
                moreEl.classList.remove('d-none');
            } else {
                moreEl.classList.add('d-none');
            }
        }

        autoSessions = Array.isArray(data.auto) ? data.auto : [];
        renderList('[data-auto-list]', autoSessions, function (item) {
            var count = parseInt(item.event_count, 10);
            if (isNaN(count) || count < 0) {
                count = 0;
            }
            var label;
            if (item.is_reviewed_only) {
                label = 'reviewed file';
            } else if (count > 0) {
                label = '<button type="button" class="my-day-auto-events-btn" data-auto-session-id="' +
                    escapeAttr(String(item.id)) + '">' + escapeHtml(String(count)) + ' activities</button>';
            } else {
                label = '0 activities';
            }
            var input = item.posted
                ? '<span>' + escapeHtml(item.confirmed_minutes) + 'm</span>'
                : '<input type="number" class="form-control form-control-sm my-day-diary-minutes-input" min="1" max="480" value="' +
                    escapeHtml(item.confirmed_minutes) + '" data-session-id="' + escapeHtml(item.id) + '">';
            return '<li><span>' + formatRef(item) + ' · ' + label + '</span>' + input + '</li>';
        }, 'No recorded file time yet');

        renderList('[data-opened-list]', data.opened || [], function (item) {
            return '<li><span>' + formatRef(item) + ' · ' + escapeHtml(item.minutes || 0) + 'm (open)</span></li>';
        }, 'No open files');

        renderList('[data-manual-list]', data.manual || [], function (item) {
            return '<li><span>' + formatRef(item) + ' · ' + escapeHtml(item.kind_label || item.kind) +
                ' · ' + escapeHtml(item.title) + ' · ' + escapeHtml(item.confirmed_minutes) + 'm</span></li>';
        }, 'No manual logs');
    }

    function loadDiary() {
        return fetchJson(diaryUrl).then(function (data) {
            if (data && data.success) {
                renderDiary(data);
            }
        });
    }

    function setSavedStatus(stored) {
        var el = root.querySelector('[data-saved-status]');
        if (!el) {
            return;
        }
        if (stored && stored.stored) {
            var when = stored.saved_at ? formatSavedAt(stored.saved_at) : '';
            el.textContent = 'Saved for admin' + (when ? ' · ' + when : '');
        } else {
            el.textContent = 'Not saved for admin yet';
        }
    }

    function formatSavedAt(iso) {
        try {
            var d = new Date(iso);
            if (isNaN(d.getTime())) {
                return iso;
            }
            return d.toLocaleString();
        } catch (e) {
            return iso;
        }
    }

    function loadCopySummary() {
        return fetchJson(copyUrl).then(function (data) {
            var pre = root.querySelector('[data-copy-text]');
            if (pre && data && data.summary && data.summary.text) {
                pre.textContent = data.summary.text;
            }
            if (data && data.stored) {
                setSavedStatus(data.stored);
            }
            return (data && data.summary && data.summary.text) ? data.summary.text : '';
        });
    }

    function saveSummary() {
        if (!saveUrl) {
            return Promise.resolve();
        }
        return fetchJson(saveUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: '{}' })
            .then(function (data) {
                if (data && data._ok && data.saved && data.summary) {
                    setSavedStatus(data.summary);
                    var pre = root.querySelector('[data-copy-text]');
                    if (pre && data.summary.text) {
                        pre.textContent = data.summary.text;
                    }
                } else {
                    var el = root.querySelector('[data-saved-status]');
                    if (el) {
                        el.textContent = 'Could not save for admin. Try again.';
                    }
                }
                return data;
            });
    }

    root.addEventListener('change', function (ev) {
        var input = ev.target.closest('[data-session-id]');
        if (!input) {
            return;
        }
        var minutes = parseInt(input.value, 10);
        if (!minutes || minutes < 1 || minutes > 480) {
            return;
        }
        fetchJson(sessionUpdateBase + '/' + input.getAttribute('data-session-id'), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ confirmed_minutes: minutes })
        }).then(function () {
            loadDiary();
            loadCopySummary();
        });
    });

    var logBtn = document.getElementById('myDayDiaryLogBtn');
    var dialog = document.getElementById('myDayDiaryLogDialog');
    var form = document.getElementById('myDayDiaryLogForm');

    if (logBtn && dialog) {
        logBtn.addEventListener('click', function () {
            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            } else {
                dialog.setAttribute('open', 'open');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function (ev) {
            var submitter = ev.submitter;
            if (submitter && submitter.value === 'cancel') {
                return;
            }
            ev.preventDefault();
            var fd = new FormData(form);
            if (fd.get('admin')) {
                fd.delete('record_type');
                fd.delete('record_id');
                fd.delete('application_id');
            }
            var payload = {
                kind: fd.get('kind'),
                title: fd.get('title'),
                confirmed_minutes: parseInt(fd.get('confirmed_minutes'), 10),
                admin: !!fd.get('admin'),
                record_type: fd.get('record_type') || null,
                record_id: fd.get('record_id') ? parseInt(fd.get('record_id'), 10) : null,
                application_id: fd.get('application_id') ? parseInt(fd.get('application_id'), 10) : null
            };
            fetchJson(logUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(function (res) {
                if (res && res.success) {
                    form.reset();
                    if (typeof dialog.close === 'function') {
                        dialog.close();
                    }
                    loadDiary();
                    loadCopySummary();
                }
            });
        });

        var searchInput = form.querySelector('[name="record_q"]');
        var resultsEl = form.querySelector('[data-search-results]');
        var adminBox = form.querySelector('[name="admin"]');
        if (adminBox) {
            adminBox.addEventListener('change', function () {
                var wrap = form.querySelector('[data-record-search-wrap]');
                if (wrap) {
                    wrap.style.display = adminBox.checked ? 'none' : '';
                }
            });
        }
        if (searchInput && resultsEl) {
            var searchTimer = null;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    var q = searchInput.value.trim();
                    if (q.length < 2) {
                        resultsEl.innerHTML = '';
                        return;
                    }
                    fetchJson(searchUrl + '?q=' + encodeURIComponent(q)).then(function (res) {
                        var rows = (res && res.results) || [];
                        resultsEl.innerHTML = rows.map(function (row) {
                            return '<button type="button" data-record-type="' + escapeHtml(row.record_type) +
                                '" data-record-id="' + escapeHtml(row.record_id) +
                                '" data-application-id="' + escapeHtml(row.application_id || '') + '">' +
                                escapeHtml(row.label) + '</button>';
                        }).join('');
                    });
                }, 250);
            });
            resultsEl.addEventListener('click', function (ev) {
                var btn = ev.target.closest('button[data-record-id]');
                if (!btn) {
                    return;
                }
                form.querySelector('[name="record_type"]').value = btn.getAttribute('data-record-type') || '';
                form.querySelector('[name="record_id"]').value = btn.getAttribute('data-record-id') || '';
                form.querySelector('[name="application_id"]').value = btn.getAttribute('data-application-id') || '';
                searchInput.value = btn.textContent.trim();
                resultsEl.innerHTML = '';
            });
        }
    }

    var copyBtn = document.getElementById('myDayDiaryCopyBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            loadCopySummary().then(function (text) {
                if (text && navigator.clipboard) {
                    navigator.clipboard.writeText(text);
                }
                return saveSummary();
            });
        });
    }

    var saveBtn = document.getElementById('myDayDiarySaveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            loadCopySummary().then(function () {
                return saveSummary();
            });
        });
    }

    function openAutoEventsDialog(sessionId) {
        if (!sessionId) {
            return;
        }
        var row = autoSessions.find(function (item) {
            return String(item.id) === String(sessionId);
        });
        if (!row) {
            return;
        }

        var dialog = document.getElementById('myDayAutoEventsDialog');
        var refEl = dialog ? dialog.querySelector('[data-auto-events-ref]') : null;
        var listEl = dialog ? dialog.querySelector('[data-auto-events-list]') : null;
        if (!dialog || !listEl) {
            return;
        }

        var count = parseInt(row.event_count, 10);
        if (isNaN(count) || count < 0) {
            count = Array.isArray(row.events) ? row.events.length : 0;
        }
        var total = parseInt(row.confirmed_minutes, 10);
        if (isNaN(total) || total < 0) {
            total = 0;
        }
        var summary = count > 1
            ? (' · total ' + total + 'm')
            : (' · ' + total + 'm');
        if (refEl) {
            if (row.url && row.ref) {
                refEl.innerHTML = '<a class="my-day-diary-ref" href="' + escapeAttr(row.url) + '">' +
                    escapeHtml(row.ref) + '</a>' + escapeHtml(summary);
            } else {
                refEl.textContent = (row.ref || '—') + summary;
            }
        }

        var events = Array.isArray(row.events) ? row.events : [];
        if (!events.length) {
            listEl.innerHTML = '<p class="my-day-auto-events-empty">No CRM activities found for this session.</p>';
        } else {
            listEl.innerHTML = events.map(function (event) {
                var title = event.title || event.kind || 'Activity';
                var linkUrl = event.url || row.url || '';
                var titleHtml = linkUrl
                    ? '<a href="' + escapeAttr(linkUrl) + '">' + escapeHtml(title) + '</a>'
                    : escapeHtml(title);
                var eventMins = parseInt(event.minutes, 10);
                if (isNaN(eventMins) || eventMins < 0) {
                    eventMins = 0;
                }
                var metaParts = [];
                if (event.time) {
                    metaParts.push(escapeHtml(String(event.time)));
                }
                metaParts.push(escapeHtml(eventMins + 'm'));
                if (event.ref) {
                    metaParts.push(
                        linkUrl
                            ? '<a href="' + escapeAttr(linkUrl) + '">' + escapeHtml(String(event.ref)) + '</a>'
                            : escapeHtml(String(event.ref))
                    );
                }
                return '<div class="my-day-auto-event">' +
                    '<div class="my-day-auto-event-kind">' + escapeHtml(event.kind || 'Activity') + '</div>' +
                    '<div class="my-day-auto-event-title">' + titleHtml + '</div>' +
                    '<div class="my-day-auto-event-meta">' + metaParts.join(' · ') + '</div>' +
                    '</div>';
            }).join('');
        }

        if (typeof dialog.showModal === 'function') {
            dialog.showModal();
        } else {
            dialog.setAttribute('open', 'open');
        }
    }

    if (!root.dataset.autoEventsBound) {
        root.dataset.autoEventsBound = '1';
        root.addEventListener('click', function (event) {
            var btn = event.target.closest('.my-day-auto-events-btn');
            if (!btn || !root.contains(btn)) {
                return;
            }
            event.preventDefault();
            openAutoEventsDialog(btn.getAttribute('data-auto-session-id'));
        });
    }

    var autoEventsDialog = document.getElementById('myDayAutoEventsDialog');
    if (autoEventsDialog && !autoEventsDialog.dataset.closeBound) {
        autoEventsDialog.dataset.closeBound = '1';
        autoEventsDialog.addEventListener('click', function (event) {
            var closeBtn = event.target.closest('[data-auto-events-close]');
            if (!closeBtn) {
                return;
            }
            if (typeof autoEventsDialog.close === 'function') {
                autoEventsDialog.close();
            } else {
                autoEventsDialog.removeAttribute('open');
            }
        });
    }

    if (!root.dataset.crmBodyToggleBound) {
        root.dataset.crmBodyToggleBound = '1';
        root.addEventListener('click', function (event) {
            var btn = event.target.closest('.my-day-crm-show-more');
            if (!btn || !root.contains(btn)) {
                return;
            }
            event.preventDefault();
            var body = btn.closest('.my-day-crm-body');
            if (!body) {
                return;
            }
            var expanding = body.classList.contains('is-collapsed');
            body.classList.toggle('is-collapsed', !expanding);
            btn.setAttribute('aria-expanded', expanding ? 'true' : 'false');
            btn.textContent = expanding ? 'Show less' : 'Show more';
        });
    }

    loadDiary();
    loadCopySummary();
})();
