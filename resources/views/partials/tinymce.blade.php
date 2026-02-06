{{-- TinyMCE only - Include via @push('tinymce-scripts') on pages that need the editor --}}
<script>
    window.TINYMCE_UPLOAD_URL = "{{ url(route('tinymce.upload-image')) }}";
    window.TINYMCE_CSRF_TOKEN = "{{ csrf_token() }}";
</script>
<script src="{{asset('assets/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce-init.js')}}"></script>



