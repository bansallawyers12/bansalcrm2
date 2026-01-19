@php
    $preferredCountries = \App\Models\Country::getPreferredCountries();
    $allCountries = \App\Models\Country::getAllWithPhoneCodes();
    $preferredCodes = $preferredCountries->pluck('phonecode')->map(function($code) {
        return '+' . $code;
    })->toArray();
@endphp
<option value="">Select</option>
@if($preferredCountries->count())
    <optgroup label="Popular">
        @foreach($preferredCountries as $country)
            @php $code = '+' . $country->phonecode; @endphp
            <option value="{{ $code }}">{{ $code }} ({{ $country->name }})</option>
        @endforeach
    </optgroup>
@endif
<optgroup label="All Countries">
    @foreach($allCountries as $country)
        @php $code = '+' . $country->phonecode; @endphp
        @if(!in_array($code, $preferredCodes, true))
            <option value="{{ $code }}">{{ $code }} ({{ $country->name }})</option>
        @endif
    @endforeach
</optgroup>
