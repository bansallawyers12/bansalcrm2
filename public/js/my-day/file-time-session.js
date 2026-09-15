(function () {
    'use strict';

    var cfg = window.MyDaySession;
    if (!cfg || !cfg.routes || !cfg.recordType || !cfg.recordId) {
        return;
    }

    cfg.csrf = cfg.csrf
        || (document.querySelector('meta[name="csrf-token"]') || {}).content
        || '';

    var IDLE_MS = 15 * 60 * 1000;
    var IDLE_GRACE_MS = 2 * 60 * 1000;
    var HEARTBEAT_MS = 60 * 1000;

    var focusedSeconds = 0;
    var lastTickAt = null;
    var heartbeatTimer = null;
    var idleTimer = null;
    var idleGraceTimer = null;
    var idleStartedAt = null;
    var channel = typeof BroadcastChannel !== 'undefined' ? new BroadcastChannel('my-day-session') : null;
    var localTick = false;
    var sessionId = null;
    var started = false;

    function isFocused() {
        return document.hasFocus() && document.visibilityState === 'visible';
    }

    function currentRecord() {
        var recordId = parseInt(cfg.recordId, 10);
        var applicationId = cfg.applicationId ? parseInt(cfg.applicationId, 10) : null;
        if (!cfg.recordType || !recordId || isNaN(recordId)) {
            return null;
        }
        if (applicationId !== null && isNaN(applicationId)) {
            applicationId = null;
        }
        return {
            recordType: cfg.recordType,
            recordId: recordId,
            applicationId: applicationId,
            ref: cfg.ref || 'file'
        };
    }

    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': cfg.csrf || ''
            },
            body: JSON.stringify(body),
            keepalive: true
        })
            .then(function (res) {
                return res.json().catch(function () {
                    return {};
                });
            })
            .catch(function () {
                return {};
            });
    }

    function sendBlur() {
        var rec = currentRecord();
        if (!rec) {
            return;
        }
        var payload = {
            record_type: rec.recordType,
            record_id: rec.recordId,
            application_id: rec.applicationId,
            focused_seconds: focusedSeconds,
            _token: cfg.csrf || ''
        };
        var url = cfg.routes.blur;
        if (navigator.sendBeacon) {
            var fd = new FormData();
            Object.keys(payload).forEach(function (key) {
                if (payload[key] !== null && payload[key] !== undefined) {
                    fd.append(key, payload[key]);
                }
            });
            if (!navigator.sendBeacon(url, fd)) {
                postJson(url, payload);
            }
        } else {
            postJson(url, payload);
        }
    }

    function heartbeat() {
        if (!isFocused() || !localTick) {
            return;
        }
        var rec = currentRecord();
        if (!rec) {
            return;
        }
        postJson(cfg.routes.heartbeat, {
            record_type: rec.recordType,
            record_id: rec.recordId,
            application_id: rec.applicationId,
            focused_seconds: focusedSeconds
        }).then(function (res) {
            if (res && res.session && res.session.id) {
                sessionId = res.session.id;
            }
        });
    }

    function resetIdleTimers() {
        idleStartedAt = null;
        if (idleTimer) {
            clearTimeout(idleTimer);
            idleTimer = null;
        }
        if (idleGraceTimer) {
            clearTimeout(idleGraceTimer);
            idleGraceTimer = null;
        }
        hideIdleModal();
        scheduleIdleCheck();
    }

    function scheduleIdleCheck() {
        if (idleTimer) {
            clearTimeout(idleTimer);
        }
        if (!isFocused() || !localTick) {
            return;
        }
        idleTimer = setTimeout(onIdleWarning, IDLE_MS);
    }

    function onIdleWarning() {
        if (!isFocused() || !localTick) {
            return;
        }
        idleStartedAt = new Date().toISOString();
        showIdleModal();
        idleGraceTimer = setTimeout(applyIdleCut, IDLE_GRACE_MS);
    }

    function applyIdleCut() {
        if (!idleStartedAt || !sessionId || !cfg.routes.idleCutBase) {
            return;
        }
        postJson(cfg.routes.idleCutBase + '/' + sessionId + '/idle-cut', { idle_started_at: idleStartedAt });
        localTick = false;
        idleStartedAt = null;
        hideIdleModal();
    }

    function showIdleModal() {
        var el = document.getElementById('myDaySessionIdleDialog');
        if (!el) {
            el = document.createElement('dialog');
            el.id = 'myDaySessionIdleDialog';
            el.innerHTML = '<p>Still on <strong id="myDaySessionIdleRef"></strong>?</p>' +
                '<button type="button" id="myDaySessionIdleOk">Yes, still here</button>';
            document.body.appendChild(el);
            el.querySelector('#myDaySessionIdleOk').addEventListener('click', function () {
                resetIdleTimers();
            });
        }
        var refEl = el.querySelector('#myDaySessionIdleRef');
        if (refEl) {
            refEl.textContent = cfg.ref || 'this file';
        }
        if (typeof el.showModal === 'function') {
            el.showModal();
        } else {
            el.setAttribute('open', 'open');
        }
    }

    function hideIdleModal() {
        var el = document.getElementById('myDaySessionIdleDialog');
        if (!el) {
            return;
        }
        if (typeof el.close === 'function') {
            el.close();
        } else {
            el.removeAttribute('open');
        }
    }

    function startHeartbeatLoop() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
        }
        heartbeatTimer = setInterval(heartbeat, HEARTBEAT_MS);
        heartbeat();
    }

    function stopHeartbeatLoop() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
            heartbeatTimer = null;
        }
    }

    function onFocusGain() {
        localTick = true;
        lastTickAt = Date.now();
        startHeartbeatLoop();
        resetIdleTimers();
        if (channel) {
            channel.postMessage({
                type: 'focus',
                recordType: cfg.recordType,
                recordId: cfg.recordId,
                applicationId: cfg.applicationId || null
            });
        }
    }

    function onFocusLoss() {
        tickAccumulator();
        localTick = false;
        stopHeartbeatLoop();
        resetIdleTimers();
        sendBlur();
    }

    function tickAccumulator() {
        if (!localTick || lastTickAt === null) {
            return;
        }
        var now = Date.now();
        focusedSeconds += Math.max(0, Math.floor((now - lastTickAt) / 1000));
        lastTickAt = now;
    }

    function tickLoop() {
        if (localTick && isFocused()) {
            tickAccumulator();
        }
        requestAnimationFrame(tickLoop);
    }

    function bindInputReset() {
        ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(function (ev) {
            document.addEventListener(ev, resetIdleTimers, { passive: true });
        });
    }

    function syncApplicationFromTabs() {
        var tabs = document.getElementById('client_tabs');
        if (!tabs) {
            return;
        }
        var raw = tabs.getAttribute('data-application-id') || '';
        var next = raw ? parseInt(raw, 10) : null;
        if (next !== null && isNaN(next)) {
            next = null;
        }
        var prev = cfg.applicationId ? parseInt(cfg.applicationId, 10) : null;
        if (prev !== null && isNaN(prev)) {
            prev = null;
        }
        if (String(prev || '') === String(next || '')) {
            return;
        }
        flushAndSwitchApplication(next);
    }

    function flushAndSwitchApplication(nextApplicationId) {
        if (localTick || focusedSeconds > 0) {
            onFocusLoss();
        }
        focusedSeconds = 0;
        sessionId = null;
        lastTickAt = null;
        cfg.applicationId = nextApplicationId;
        if (isFocused()) {
            onFocusGain();
        }
    }

    function bindApplicationContextChange() {
        var tabs = document.getElementById('client_tabs');
        if (!tabs || typeof MutationObserver === 'undefined') {
            return;
        }
        var observer = new MutationObserver(function () {
            syncApplicationFromTabs();
        });
        observer.observe(tabs, { attributes: true, attributeFilter: ['data-application-id'] });
    }

    function startTracking() {
        if (started) {
            return;
        }
        started = true;
        syncApplicationFromTabs();
        bindInputReset();
        bindApplicationContextChange();
        requestAnimationFrame(tickLoop);
        if (isFocused()) {
            onFocusGain();
        }
    }

    window.MyDaySessionRebind = function (next) {
        if (!next || !next.recordType || !next.recordId) {
            return;
        }
        if (localTick || focusedSeconds > 0) {
            onFocusLoss();
        }
        focusedSeconds = 0;
        sessionId = null;
        lastTickAt = null;
        cfg.recordType = next.recordType;
        cfg.recordId = next.recordId;
        cfg.applicationId = next.applicationId || null;
        cfg.ref = next.ref || cfg.ref;
        if (isFocused()) {
            onFocusGain();
        }
    };

    if (channel) {
        channel.addEventListener('message', function (ev) {
            var data = ev.data || {};
            if (data.type !== 'focus') {
                return;
            }
            if (String(data.recordType) === String(cfg.recordType) &&
                String(data.recordId) === String(cfg.recordId) &&
                String(data.applicationId || '') === String(cfg.applicationId || '')) {
                return;
            }
            localTick = false;
            stopHeartbeatLoop();
        });
    }

    window.addEventListener('focus', function () {
        if (isFocused()) {
            onFocusGain();
        }
    });
    window.addEventListener('blur', onFocusLoss);
    document.addEventListener('visibilitychange', function () {
        if (isFocused()) {
            onFocusGain();
        } else {
            onFocusLoss();
        }
    });
    window.addEventListener('pagehide', onFocusLoss);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startTracking);
    } else {
        startTracking();
    }
})();
