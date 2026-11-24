<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Office Check-In — Bansal Education</title>
    <style>
        :root { --navy:#1e3a5f; --blue:#2d6a9f; --bg:#f4f7fb; --muted:#5c6770; --line:#e4e8ee; --ok:#1b7f4a; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: #1a1a1a; line-height: 1.45; }
        .wrap { max-width: 720px; margin: 0 auto; padding: 16px 12px 40px; }
        .card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 28px rgba(30,58,95,.08); border: 1px solid var(--line); }
        .head { background: linear-gradient(135deg, var(--navy), var(--blue)); color: #fff; padding: 20px 18px; }
        .head h1 { margin: 0; font-size: 1.25rem; }
        .head p { margin: 6px 0 0; opacity: .9; font-size: .92rem; }
        .body { padding: 18px 16px 22px; }
        .stepper { display: flex; gap: 4px; margin-bottom: 18px; }
        .step { flex: 1; text-align: center; min-width: 0; }
        .step .dot { width: 28px; height: 28px; border-radius: 50%; margin: 0 auto 4px; background: #d5dbe3; color: #667; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 700; }
        .step .lbl { font-size: .68rem; color: #7b8794; }
        .step.active .dot { background: var(--navy); color: #fff; }
        .step.active .lbl { color: var(--navy); font-weight: 700; }
        .step.done .dot { background: var(--ok); color: #fff; }
        .panel { display: none; }
        .panel.active { display: block; }
        h2 { margin: 0 0 12px; font-size: .78rem; letter-spacing: .04em; text-transform: uppercase; color: var(--navy); }
        label { display: block; font-weight: 650; font-size: .9rem; margin: 12px 0 6px; }
        .req { color: #b42318; }
        .hint { color: var(--muted); font-weight: 500; }
        input, select, textarea { width: 100%; font-size: 16px; padding: 12px; border: 1px solid #cfd6df; border-radius: 10px; background: #fff; }
        textarea { min-height: 88px; resize: vertical; }
        .err { color: #b42318; font-size: .82rem; margin-top: 4px; }
        .alert { display: none; background: #fdecea; color: #7a1f16; border-radius: 10px; padding: 10px 12px; margin-bottom: 12px; }
        .actions { display: flex; flex-direction: column; gap: 10px; margin-top: 18px; }
        .toolbar { display: flex; justify-content: flex-end; margin: -6px 0 12px; }
        button { font: inherit; cursor: pointer; border: 0; border-radius: 10px; padding: 14px 16px; font-weight: 700; min-height: 48px; }
        .primary { background: var(--navy); color: #fff; }
        .primary:disabled { opacity: .45; cursor: not-allowed; }
        .ghost { background: #eef2f6; color: #243140; }
        .match { border: 2px solid var(--line); border-radius: 12px; padding: 14px; margin-bottom: 10px; cursor: pointer; }
        .match.on { border-color: var(--navy); background: #eef4fb; }
        .badge { display: inline-block; font-size: .72rem; font-weight: 700; border-radius: 999px; padding: 2px 8px; margin-left: 6px; }
        .badge.client { background: #e7f6ee; color: #0f6b3c; }
        .badge.lead { background: #fff6e0; color: #8a5a00; }
        .muted { color: var(--muted); font-size: .88rem; }
        .summary { display: flex; flex-direction: column; gap: 4px; padding: 10px 0; border-bottom: 1px solid #f0f2f5; }
        .summary span { color: var(--muted); font-size: .82rem; }
        .center { text-align: center; padding: 28px 8px; }
        .spinner { display: none; text-align: center; color: var(--muted); margin-top: 12px; }
        .foot { text-align: center; color: var(--muted); font-size: .8rem; margin-top: 16px; }
        @media (min-width: 700px) {
            .wrap { padding: 36px 20px 56px; }
            .head, .body { padding-left: 28px; padding-right: 28px; }
            .actions { flex-direction: row; justify-content: flex-end; }
            .actions button { width: auto; min-width: 140px; }
            .summary { flex-direction: row; justify-content: space-between; align-items: baseline; gap: 16px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            <h1>Office Check-In</h1>
            <p>Tell us you have arrived. No login needed.</p>
        </div>
        <div class="body">
            <div class="stepper" id="stepper">
                <div class="step active" data-step="1"><div class="dot">1</div><div class="lbl">Contact</div></div>
                <div class="step" data-step="2"><div class="dot">2</div><div class="lbl">Match</div></div>
                <div class="step" data-step="3"><div class="dot">3</div><div class="lbl">Confirm</div></div>
                <div class="step" data-step="4"><div class="dot">4</div><div class="lbl">Reason</div></div>
            </div>
            <div class="toolbar"><button type="button" class="ghost" id="refresh">Refresh</button></div>
            <div class="alert" id="alert" role="alert"></div>

            <section class="panel active" id="step-1">
                <h2>Step 1 — Contact details</h2>
                <label for="phone">Phone <span class="req">*</span></label>
                <input id="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" placeholder="e.g. 0412 345 678">
                <div class="err" id="phone-err"></div>
                <label for="email">Email <span class="hint">(optional)</span></label>
                <input id="email" type="email" inputmode="email" autocomplete="email" maxlength="255">
                <div class="actions"><button type="button" class="primary" id="lookup">Look Up</button></div>
                <div class="spinner" id="lookup-spin">Looking up your details…</div>
            </section>

            <section class="panel" id="step-2">
                <h2>Step 2 — Is this you?</h2>
                <p class="muted" id="match-sub"></p>
                <div id="matches"></div>
                <button type="button" class="ghost" id="walkin" style="width:100%;margin-top:8px;">None of these — continue as a walk-in</button>
                <div class="actions">
                    <button type="button" class="ghost" id="back-2">Back</button>
                    <button type="button" class="primary" id="next-2" disabled>Continue</button>
                </div>
            </section>

            <section class="panel" id="step-new">
                <h2>We could not find that number</h2>
                <label for="walk-name">Your full name <span class="req">*</span></label>
                <input id="walk-name" maxlength="140" autocomplete="name">
                <div class="err" id="walk-name-err"></div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-new">Back</button>
                    <button type="button" class="primary" id="next-new">Continue</button>
                </div>
            </section>

            <section class="panel" id="step-3">
                <h2>Step 3 — Confirm your details</h2>
                <div id="summary"></div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-3">Back</button>
                    <button type="button" class="primary" id="next-3">Details are correct</button>
                </div>
            </section>

            <section class="panel" id="step-4">
                <h2>Step 4 — Reason for your visit</h2>
                <label for="reason">Reason <span class="hint">(optional)</span></label>
                <select id="reason">
                    <option value="">Select a reason</option>
                    @foreach ($visitReasons as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label for="notes">Notes <span class="req" id="notes-req" style="display:none;">*</span></label>
                <textarea id="notes" maxlength="2000" placeholder="Anything we should know"></textarea>
                <div class="err" id="notes-err"></div>
                <div class="actions">
                    <button type="button" class="ghost" id="back-4">Back</button>
                    <button type="button" class="primary" id="save">Submit check-in</button>
                </div>
                <div class="spinner" id="save-spin">Saving your check-in…</div>
            </section>

            <section class="panel" id="step-done">
                <div class="center">
                    <h2 style="font-size:1.25rem;text-transform:none;letter-spacing:0;">You're checked in</h2>
                    <p id="done-msg"></p>
                    <button type="button" class="primary" id="again">New check-in</button>
                </div>
            </section>
        </div>
    </div>
    <p class="foot">Bansal Immigration and Education</p>
</div>
<script>
(function () {
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;
    var BASE = @json(url('/office-check-in'));
    var state = blank();

    function blank() {
        return { phone:'', email:'', adminId:null, adminType:null, name:'', walkInName:'' };
    }
    function $(id) { return document.getElementById(id); }
    function alertMsg(msg) { var el = $('alert'); el.textContent = msg; el.style.display = 'block'; }
    function clearAlert() { $('alert').style.display = 'none'; }
    function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }

    function go(panel, stepNum) {
        document.querySelectorAll('.panel').forEach(function (el) { el.classList.remove('active'); });
        $(panel).classList.add('active');
        clearAlert();
        if (stepNum) {
            document.querySelectorAll('.step').forEach(function (el) {
                var n = parseInt(el.getAttribute('data-step'), 10);
                el.classList.toggle('active', n === stepNum);
                el.classList.toggle('done', n < stepNum);
            });
        }
    }

    function post(path, body) {
        return fetch(BASE + path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(body)
        }).then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok) {
                    var msg = data.message || (data.errors && Object.values(data.errors)[0] && Object.values(data.errors)[0][0]) || 'Request failed.';
                    throw new Error(msg);
                }
                return data;
            });
        });
    }

    function renderMatches(list) {
        var box = $('matches');
        box.innerHTML = '';
        list.forEach(function (m) {
            var div = document.createElement('div');
            div.className = 'match';
            div.innerHTML = '<strong>' + esc(m.name || 'Unknown') + '</strong>' +
                '<span class="badge ' + esc(m.type) + '">' + esc(m.type) + '</span>' +
                '<div class="muted">' + esc(m.phone || '') + (m.email ? ' · ' + esc(m.email) : '') + '</div>';
            div.addEventListener('click', function () {
                box.querySelectorAll('.match').forEach(function (el) { el.classList.remove('on'); });
                div.classList.add('on');
                state.adminId = m.id;
                state.adminType = m.type;
                state.name = m.name;
                state.walkInName = '';
                $('next-2').disabled = false;
            });
            box.appendChild(div);
        });
    }

    function fillSummary() {
        var name = state.name || state.walkInName || 'Walk-in';
        $('summary').innerHTML =
            '<div class="summary"><b>Name</b><span>' + esc(name) + '</span></div>' +
            '<div class="summary"><b>Phone</b><span>' + esc(state.phone) + '</span></div>' +
            '<div class="summary"><b>Email</b><span>' + esc(state.email || '—') + '</span></div>' +
            '<div class="summary"><b>Record</b><span>' + esc(state.adminType || 'Walk-in') + '</span></div>';
    }

    $('lookup').addEventListener('click', function () {
        clearAlert();
        $('phone-err').textContent = '';
        state.phone = $('phone').value.trim();
        state.email = $('email').value.trim();
        if (state.phone.length < 6) {
            $('phone-err').textContent = 'Enter a valid phone number.';
            return;
        }
        $('lookup-spin').style.display = 'block';
        post('/lookup', { phone: state.phone, email: state.email || null })
            .then(function (data) {
                $('lookup-spin').style.display = 'none';
                state.adminId = null;
                state.adminType = null;
                state.name = '';
                state.walkInName = '';
                $('next-2').disabled = true;
                if (!data.matches || !data.matches.length) {
                    go('step-new');
                    return;
                }
                $('match-sub').textContent = 'Select your record to continue.';
                renderMatches(data.matches);
                go('step-2', 2);
            })
            .catch(function (err) {
                $('lookup-spin').style.display = 'none';
                alertMsg(err.message || 'Lookup failed.');
            });
    });

    $('next-2').addEventListener('click', function () {
        if (!state.adminId) return;
        fillSummary();
        go('step-3', 3);
    });
    $('walkin').addEventListener('click', function () {
        state.adminId = null;
        state.adminType = null;
        state.name = '';
        go('step-new');
    });
    $('next-new').addEventListener('click', function () {
        $('walk-name-err').textContent = '';
        state.walkInName = $('walk-name').value.trim();
        if (!state.walkInName) {
            $('walk-name-err').textContent = 'Please enter your name.';
            return;
        }
        state.adminId = null;
        state.adminType = null;
        state.name = '';
        fillSummary();
        go('step-3', 3);
    });
    $('next-3').addEventListener('click', function () { go('step-4', 4); });
    $('reason').addEventListener('change', function () {
        $('notes-req').style.display = this.value === 'other' ? 'inline' : 'none';
    });
    $('save').addEventListener('click', function () {
        clearAlert();
        $('notes-err').textContent = '';
        var reason = $('reason').value;
        var notes = $('notes').value.trim();
        if (reason === 'other' && !notes) {
            $('notes-err').textContent = 'Please add a note when you choose Other.';
            return;
        }
        $('save-spin').style.display = 'block';
        post('/submit', {
            phone: state.phone,
            email: state.email || null,
            admin_id: state.adminId,
            admin_type: state.adminType,
            walk_in_name: state.walkInName || null,
            visit_reason: reason || null,
            visit_notes: notes || null
        }).then(function (data) {
            $('save-spin').style.display = 'none';
            $('done-msg').textContent = data.message || 'You are checked in.';
            go('step-done');
        }).catch(function (err) {
            $('save-spin').style.display = 'none';
            alertMsg(err.message || 'Could not save check-in.');
        });
    });

    function restart() {
        state = blank();
        $('phone').value = '';
        $('email').value = '';
        $('walk-name').value = '';
        $('reason').value = '';
        $('notes').value = '';
        go('step-1', 1);
    }
    $('refresh').addEventListener('click', restart);
    $('again').addEventListener('click', restart);
    $('back-2').addEventListener('click', function () { go('step-1', 1); });
    $('back-new').addEventListener('click', function () { go('step-1', 1); });
    $('back-3').addEventListener('click', function () {
        if (state.adminId) go('step-2', 2); else go('step-new');
    });
    $('back-4').addEventListener('click', function () { go('step-3', 3); });
})();
</script>
</body>
</html>
