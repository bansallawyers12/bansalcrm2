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

    function fetchJson(url, options) {
        options = options || {};
        options.credentials = 'same-origin';
        options.headers = Object.assign({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
        }, options.headers || {});
        return fetch(url, options).then(function (res) {
            return res.json().catch(function () { return {}; });
        }).catch(function () { return {}; });
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
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
            return '<li><span>' + escapeHtml(item.ref || '—') + ' · ' + escapeHtml(item.kind) + ' · ' +
                escapeHtml(item.title) + ' · ' + escapeHtml(item.time || '') + chip + '</span></li>';
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

        renderList('[data-auto-list]', data.auto || [], function (item) {
            var label = item.is_reviewed_only ? 'reviewed file' : ((item.event_count || 0) + ' activities');
            var input = item.posted
                ? '<span>' + escapeHtml(item.confirmed_minutes) + 'm</span>'
                : '<input type="number" class="form-control form-control-sm my-day-diary-minutes-input" min="1" max="480" value="' +
                    escapeHtml(item.confirmed_minutes) + '" data-session-id="' + escapeHtml(item.id) + '">';
            return '<li><span>' + escapeHtml(item.ref) + ' · ' + escapeHtml(label) + '</span>' + input + '</li>';
        }, 'No recorded file time yet');

        renderList('[data-opened-list]', data.opened || [], function (item) {
            return '<li><span>' + escapeHtml(item.ref) + ' · ' + escapeHtml(item.minutes || 0) + 'm (open)</span></li>';
        }, 'No open files');

        renderList('[data-manual-list]', data.manual || [], function (item) {
            return '<li><span>' + escapeHtml(item.ref) + ' · ' + escapeHtml(item.kind_label || item.kind) +
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
            el.textContent = 'Saved for admin' + (stored.saved_at ? ' · ' + stored.saved_at : '');
        } else {
            el.textContent = 'Not saved for admin yet';
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
                if (data && data.summary) {
                    setSavedStatus(data.summary);
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

    loadDiary();
    loadCopySummary();
})();
