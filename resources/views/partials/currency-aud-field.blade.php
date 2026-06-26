<input type="hidden" name="{{ $name ?? 'currency' }}" value="AUD" @if(!empty($required)) data-valid="required" @endif>
<input class="form-control" type="text" value="Australian dollar (AUD)" readonly tabindex="-1" aria-readonly="true">
