@php
	use App\Support\FromEmailAddress;

	$storedEmail = (string) ($storedEmail ?? '');
	$emailName = old('email_name', $storedEmail !== '' ? FromEmailAddress::split($storedEmail)['local'] : '');
	$emailDomain = old('email_domain', $storedEmail !== '' ? FromEmailAddress::split($storedEmail)['domain'] : '');
	$domains = FromEmailAddress::domains();
@endphp
<div class="col-12 col-md-12 col-lg-12">
	<div class="form-group">
		<label for="email_name">Email name <span class="span_req">*</span></label>
		{!! Form::text('email_name', $emailName, [
			'class' => 'form-control',
			'id' => 'email_name',
			'data-valid' => 'required',
			'autocomplete' => 'off',
			'placeholder' => 'e.g. vipul, info, admission',
			'pattern' => '[a-zA-Z0-9._%+-]+',
			'title' => 'Enter only the part before @ (@ is not allowed here)',
		]) !!}
		<small class="form-text text-muted">Enter only the part before @ — do not type @ in this field. The full address is saved as name@domain (e.g. vipul@educationelite.com.au).</small>
		@if ($errors->has('email_name'))
			<span class="custom-error" role="alert">
				<strong>{{ $errors->first('email_name') }}</strong>
			</span>
		@endif
		@if ($errors->has('email'))
			<span class="custom-error" role="alert">
				<strong>{{ $errors->first('email') }}</strong>
			</span>
		@endif
	</div>
</div>
<div class="col-12 col-md-12 col-lg-12">
	<div class="form-group">
		<label>Email domain <span class="span_req">*</span></label>
		<div class="d-flex flex-wrap gap-3" style="gap:12px;">
			@foreach ($domains as $domain)
				<label class="mb-0" style="cursor:pointer;">
					<input type="radio" name="email_domain" value="{{ $domain }}"
						{{ $emailDomain === $domain ? 'checked' : '' }}>
					{{ '@' . $domain }}
				</label>
			@endforeach
		</div>
		<small class="form-text text-muted">Active addresses appear in compose From dropdowns for shared staff. Domains must be verified in AWS SES.</small>
		@if ($errors->has('email_domain'))
			<span class="custom-error" role="alert">
				<strong>{{ $errors->first('email_domain') }}</strong>
			</span>
		@endif
		@if ($errors->has('email') && !$errors->has('email_name'))
			<span class="custom-error" role="alert">
				<strong>{{ $errors->first('email') }}</strong>
			</span>
		@endif
	</div>
</div>
<script>
(function () {
	var el = document.getElementById('email_name');
	if (!el) return;
	function stripAt(value) {
		var i = value.indexOf('@');
		return i === -1 ? value : value.slice(0, i);
	}
	el.addEventListener('input', function () {
		var cleaned = stripAt(el.value);
		if (cleaned !== el.value) el.value = cleaned;
	});
	el.addEventListener('paste', function (ev) {
		ev.preventDefault();
		var text = (ev.clipboardData || window.clipboardData).getData('text') || '';
		el.value = stripAt(text.replace(/\s/g, ''));
	});
})();
</script>
