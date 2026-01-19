@php
    $preferredCountries = \App\Models\Country::getPreferredCountries();
    $allCountries = \App\Models\Country::getAllWithPhoneCodes();
    $defaultCountryCode = \App\Helpers\PhoneHelper::getDefaultCountryCode();
    $selectedCountryCode = $selected ?? $defaultCountryCode;
    $preferredCodes = $preferredCountries->pluck('phonecode')->map(function($code) {
        return '+' . $code;
    })->toArray();
@endphp
<select class="form-control country_code_select" name="{{ $name }}">
    <option value="">Select</option>
    @if($preferredCountries->count())
        <optgroup label="Popular">
            @foreach($preferredCountries as $country)
                @php $code = '+' . $country->phonecode; @endphp
                <option value="{{ $code }}" {{ $code === $selectedCountryCode ? 'selected' : '' }}>
                    {{ $code }} ({{ $country->name }})
                </option>
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
</select>
