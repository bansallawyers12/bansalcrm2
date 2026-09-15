@auth('admin')
@php
    $myDayRecordType = $myDayRecordType ?? null;
    $myDayRecordId = $myDayRecordId ?? null;
    $myDayApplicationId = $myDayApplicationId ?? null;
    $myDayRef = $myDayRef ?? 'file';
@endphp
@if($myDayRecordType && $myDayRecordId)
<script>
    window.MyDaySession = {
        recordType: @json($myDayRecordType),
        recordId: @json((int) $myDayRecordId),
        applicationId: @json($myDayApplicationId ? (int) $myDayApplicationId : null),
        ref: @json($myDayRef),
        csrf: @json(csrf_token()),
        routes: {
            heartbeat: @json(route('dashboard.my-day.sessions.heartbeat')),
            blur: @json(route('dashboard.my-day.sessions.blur')),
            idleCutBase: @json(url('/dashboard/my-day/sessions'))
        }
    };
</script>
<script defer src="{{ asset('js/my-day/file-time-session.js') }}?v={{ file_exists(public_path('js/my-day/file-time-session.js')) ? filemtime(public_path('js/my-day/file-time-session.js')) : time() }}"></script>
@endif
@endauth
