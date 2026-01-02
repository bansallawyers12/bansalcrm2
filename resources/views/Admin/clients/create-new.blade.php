@extends('layouts.admin')
@section('title', 'Create Client (New)')

@push('styles')
<style>
/* Modern Client Form Styles - Enhanced Design */
.crm-container {
    max-width: 1400px !important;
    margin: 0 auto !important;
    padding: 24px !important;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    min-height: 100vh !important;
    position: relative;
}

.crm-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.03) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.crm-container > * {
    position: relative;
    z-index: 1;
}

/* Override section styles for our custom container */
.section .crm-container {
    padding: 0 !important;
    margin: 0 !important;
    background: transparent !important;
}

.section-body {
    padding: 0 !important;
}

/* Ensure our styles override Bootstrap */
.crm-container * {
    box-sizing: border-box;
}

/* Override any card styles that might interfere */
.crm-container .card {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    margin-bottom: 0 !important;
}

.crm-container .card-header {
    display: none !important;
}

.crm-container .card-body {
    padding: 0 !important;
}

.client-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-bottom: 16px !important;
    padding: 14px 20px !important;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%) !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 12px rgba(99, 102, 241, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1) inset !important;
    position: relative !important;
    overflow: hidden !important;
}

.client-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    pointer-events: none;
}

.client-header h1 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
    letter-spacing: -0.3px;
}

.client-status {
    display: flex;
    gap: 8px;
    position: relative;
    z-index: 1;
}

/* Single page flow - no tabs, all sections visible */
.form-content-section {
    background: #ffffff !important;
    padding: 16px !important;
    border-radius: 8px !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    border: 1px solid #e2e8f0 !important;
    margin-bottom: 16px !important;
    display: block !important;
    width: 100% !important;
    transition: all 0.2s ease !important;
    position: relative !important;
}

.form-content-section:hover {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    border-color: #cbd5e1 !important;
}

.form-content-section:last-child {
    margin-bottom: 0 !important;
}

/* Remove any tab-related hiding */
.tab-content {
    display: block !important;
}

.form-section {
    margin-bottom: 16px;
    position: relative;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section h3 {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    position: relative;
}

.form-section h3::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 50px;
    height: 2px;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    border-radius: 2px;
}

.form-section h3 i {
    color: #6366f1;
    font-size: 14px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.content-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) !important;
    gap: 12px !important;
    margin-bottom: 12px !important;
}

.form-group {
    display: flex;
    flex-direction: column;
    position: relative;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #334155;
    font-size: 12px;
    letter-spacing: 0.1px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.form-group label .span_req {
    color: #ef4444;
    font-weight: 700;
    font-size: 14px;
}

.crm-container .form-group input[type="text"],
.crm-container .form-group input[type="email"],
.crm-container .form-group input[type="tel"],
.crm-container .form-group input[type="date"],
.crm-container .form-group select,
.crm-container .form-group textarea,
.crm-container .form-control {
    padding: 8px 12px !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 6px !important;
    font-size: 13px !important;
    transition: all 0.2s ease !important;
    width: 100% !important;
    background: #ffffff !important;
    height: auto !important;
    color: #1e293b !important;
    font-weight: 400 !important;
}

.crm-container .form-group input:hover,
.crm-container .form-group select:hover,
.crm-container .form-group textarea:hover,
.crm-container .form-control:hover {
    border-color: #cbd5e1 !important;
}

.crm-container .form-group input:focus,
.crm-container .form-group select:focus,
.crm-container .form-group textarea:focus,
.crm-container .form-control:focus {
    outline: none !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1), 0 2px 4px rgba(0, 0, 0, 0.05) !important;
    background: #ffffff !important;
}

.radio-group {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    padding: 8px 0;
}

.radio-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    cursor: pointer;
    font-size: 14px;
    color: #475569;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.radio-group label:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
}

.radio-group input[type="radio"]:checked + label,
.radio-group label:has(input[type="radio"]:checked) {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-color: #6366f1;
    color: #6366f1;
    font-weight: 600;
}

.radio-group input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #6366f1;
    margin: 0;
}

.repeatable-section {
    position: relative;
    padding: 20px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #ffffff, #f8fafc);
    transition: all 0.3s ease;
}

.repeatable-section:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    background: #ffffff;
}

.remove-item-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 13px;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
    z-index: 10;
}

.remove-item-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.add-item-btn {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    margin-top: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.add-item-btn:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    transform: translateY(-2px);
}

.add-item-btn:active {
    transform: translateY(0);
}

.input-group {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 0 !important;
    width: 100% !important;
}

.input-group .input-group-text {
    padding: 10px 14px !important;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important;
    border: 2px solid #e2e8f0 !important;
    border-right: none !important;
    border-radius: 8px 0 0 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #6366f1 !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    min-width: 48px !important;
    height: 42px !important;
    line-height: 1 !important;
    flex-shrink: 0 !important;
}

.input-group .input-group-text i {
    display: inline-block !important;
    line-height: 1 !important;
    vertical-align: middle !important;
}

.input-group .form-control,
.input-group input {
    border-radius: 0 8px 8px 0 !important;
    border-left: none !important;
    flex: 1 !important;
    height: 42px !important;
    padding: 10px 14px !important;
    margin: 0 !important;
    line-height: 1.5 !important;
}

.input-group:focus-within .input-group-text {
    border-color: #6366f1 !important;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05)) !important;
}

.text-danger {
    color: #ef4444;
    font-size: 12px;
    margin-top: 6px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.text-danger::before {
    content: '⚠';
    font-size: 14px;
}

.alert {
    padding: 16px 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    border-left: 4px solid;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.alert-danger {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border-left-color: #ef4444;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert ul {
    margin: 8px 0 0 0;
    padding-left: 20px;
    font-size: 14px;
    line-height: 1.6;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.crm-container .btn-primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
    color: white !important;
    border: none !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    transition: all 0.3s ease !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    font-size: 13px !important;
}

.crm-container .btn-primary:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4) !important;
    transform: translateY(-1px) !important;
}

.crm-container .btn-primary:active {
    transform: translateY(0) !important;
}

.crm-container .btn-secondary {
    background: #ffffff !important;
    color: #475569 !important;
    border: 2px solid #e2e8f0 !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    font-size: 13px !important;
}

.crm-container .btn-secondary:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #334155 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
}


/* Compact spacing adjustments */
.form-section:not(:last-child) {
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 16px;
}

/* Better select2 styling */
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    border: 2px solid #e2e8f0 !important;
    border-radius: 8px !important;
    min-height: 42px !important;
    transition: all 0.3s ease !important;
}

.select2-container--default .select2-selection--single:hover,
.select2-container--default .select2-selection--multiple:hover {
    border-color: #cbd5e1 !important;
}

.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1), 0 2px 4px rgba(0, 0, 0, 0.05) !important;
}

.select2-container--default .select2-selection__rendered {
    padding-left: 14px !important;
    padding-right: 14px !important;
    line-height: 40px !important;
    color: #1e293b !important;
    font-size: 14px !important;
}

.select2-container--default .select2-selection__arrow {
    height: 40px !important;
    right: 12px !important;
}

/* Checkbox styling */
.form-check-input {
    accent-color: #667eea;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    font-size: 13px;
    color: #475569;
}
</style>
@endpush

@section('content')

<div class="main-content">
    <section class="section">
        <div class="crm-container">
        <div class="client-header">
            <div>
                <h1>Create New Client</h1>
            </div>
            <div class="client-status">
                <button class="btn btn-secondary" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Back</button>
                <button class="btn btn-primary" type="submit" form="createClientForm"><i class="fas fa-save"></i> Create Client</button>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! Form::open(array('url' => 'admin/clients/store-new', 'name'=>"add-clients-new", 'id' => 'createClientForm', 'autocomplete'=>'off', "enctype"=>"multipart/form-data"))  !!} 
        <input type="hidden" name="type" value="client">

        <!-- Personal Information -->
        <div class="form-content-section">
            <section class="form-section">
                <h3><i class="fas fa-id-card"></i> Basic Information</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="span_req">*</span></label>
                        {!! Form::text('first_name', old('first_name'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter first name', 'required' ))  !!}
                        @if ($errors->has('first_name'))
                            <span class="text-danger">{{ @$errors->first('first_name') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="span_req">*</span></label>
                        {!! Form::text('last_name', old('last_name'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter last name', 'required' ))  !!}
                        @if ($errors->has('last_name'))
                            <span class="text-danger">{{ @$errors->first('last_name') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Gender <span class="span_req">*</span></label>
                        <div class="radio-group">
                            <label><input type="radio" name="gender" value="Male" {{ old('gender', 'Male') == 'Male' ? 'checked' : '' }} required> Male</label>
                            <label><input type="radio" name="gender" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}> Female</label>
                            <label><input type="radio" name="gender" value="Other" {{ old('gender') == 'Other' ? 'checked' : '' }}> Other</label>
                        </div>
                        @if ($errors->has('gender'))
                            <span class="text-danger">{{ @$errors->first('gender') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            {!! Form::text('dob', old('dob'), array('class' => 'form-control dobdatepickers', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!} 
                        </div>
                        @if ($errors->has('dob'))
                            <span class="text-danger">{{ @$errors->first('dob') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            {!! Form::text('age', old('age'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Age', 'readonly' ))  !!}
                        </div>
                        @if ($errors->has('age'))
                            <span class="text-danger">{{ @$errors->first('age') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="client_id">Client ID</label>
                        {!! Form::text('client_id', old('client_id'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Auto-generated' ))  !!}
                        @if ($errors->has('client_id'))
                            <span class="text-danger">{{ @$errors->first('client_id') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="martial_status">Marital Status</label>
                        <select name="martial_status" id="martial_status" class="form-control">
                            <option value="">Select Marital Status</option>
                            <option value="Married" {{ old('martial_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Never Married" {{ old('martial_status') == 'Never Married' ? 'selected' : '' }}>Never Married</option>
                            <option value="Engaged" {{ old('martial_status') == 'Engaged' ? 'selected' : '' }}>Engaged</option>
                            <option value="Divorced" {{ old('martial_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Separated" {{ old('martial_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                            <option value="De facto" {{ old('martial_status') == 'De facto' ? 'selected' : '' }}>De facto</option>
                            <option value="Widowed" {{ old('martial_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="Others" {{ old('martial_status') == 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                        @if ($errors->has('martial_status'))
                            <span class="text-danger">{{ @$errors->first('martial_status') }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h3><i class="fas fa-passport"></i> Passport Information</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="country_passport">Country of Passport</label>
                        <select class="form-control select2" name="country_passport">
                            <option value="">- Select Country -</option>
                            @foreach(\App\Models\Country::all() as $list)
                                <option value="{{@$list->sortname}}" {{ old('country_passport', 'IN') == $list->sortname ? 'selected' : '' }}>{{@$list->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('country_passport'))
                            <span class="text-danger">{{ @$errors->first('country_passport') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="passport_number">Passport Number</label>
                        {!! Form::text('passport_number', old('passport_number'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter passport number' ))  !!}
                        @if ($errors->has('passport_number'))
                            <span class="text-danger">{{ @$errors->first('passport_number') }}</span>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <!-- Contact Information -->
        <div class="form-content-section">
            <section class="form-section">
                <h3><i class="fas fa-phone"></i> Phone Numbers</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="contact_type">Contact Type <span class="span_req">*</span></label>
                        <select name="contact_type" id="contact_type" class="form-control" data-valid="required" required>
                            <option value="Personal" {{ old('contact_type', 'Personal') == 'Personal' ? 'selected' : '' }}>Personal</option>
                            <option value="Office" {{ old('contact_type') == 'Office' ? 'selected' : '' }}>Office</option>
                        </select>
                        @if ($errors->has('contact_type'))
                            <span class="text-danger">{{ @$errors->first('contact_type') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="phone">Contact No. <span class="span_req">*</span></label>
                        <div class="cus_field_input">
                            <div class="country_code"> 
                                <input class="telephone" id="telephone" type="tel" name="country_code" readonly>
                            </div>	
                            {!! Form::text('phone', old('phone'), array('class' => 'form-control tel_input', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter phone number', 'id' => 'checkphone', 'required' ))  !!}
                        </div>
                        @if ($errors->has('phone'))
                            <span class="text-danger">{{ @$errors->first('phone') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="att_phone">Additional Phone</label>
                        <div class="cus_field_input">
                            <div class="country_code"> 
                                <input class="telephone" id="telephone2" type="tel" name="att_country_code" readonly>
                            </div>	
                            {!! Form::text('att_phone', old('att_phone'), array('class' => 'form-control tel_input', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional phone' ))  !!}
                        </div>
                        @if ($errors->has('att_phone'))
                            <span class="text-danger">{{ @$errors->first('att_phone') }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h3><i class="fas fa-envelope"></i> Email Addresses</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="email_type">Email Type <span class="span_req">*</span></label>
                        <select name="email_type" id="email_type" class="form-control" data-valid="required" required>
                            <option value="Personal" {{ old('email_type', 'Personal') == 'Personal' ? 'selected' : '' }}>Personal</option>
                            <option value="Business" {{ old('email_type') == 'Business' ? 'selected' : '' }}>Business</option>
                        </select>
                        @if ($errors->has('email_type'))
                            <span class="text-danger">{{ @$errors->first('email_type') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="span_req">*</span></label>
                        {!! Form::text('email', old('email'), array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter email address', 'id' => 'checkemail', 'type' => 'email', 'required' ))  !!}
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ @$errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="att_email">Additional Email</label>
                        {!! Form::text('att_email', old('att_email'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter additional email', 'type' => 'email' ))  !!}
                        @if ($errors->has('att_email'))
                            <span class="text-danger">{{ @$errors->first('att_email') }}</span>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <!-- Visas & Addresses -->
        <div class="form-content-section">
            <section class="form-section">
                <h3><i class="fas fa-file-contract"></i> Visa Details</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="visa_type">Visa Type</label>
                        <select class="form-control select2" name="visa_type">
                            <option value="">- Select Visa Type -</option>
                            @foreach(\App\Models\VisaType::orderby('name', 'ASC')->get() as $visalist)
                                <option value="{{$visalist->name}}" {{ old('visa_type') == $visalist->name ? 'selected' : '' }}>{{$visalist->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('visa_type'))
                            <span class="text-danger">{{ @$errors->first('visa_type') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="visa_opt">Visa Details</label>
                        {!! Form::text('visa_opt', old('visa_opt'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Additional visa information' ))  !!}
                    </div>
                    <div class="form-group">
                        <label for="visaExpiry">Visa Expiry Date</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            {!! Form::text('visaExpiry', old('visaExpiry'), array('class' => 'form-control dobdatepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'DD/MM/YYYY' ))  !!}
                        </div>
                        @if ($errors->has('visaExpiry'))
                            <span class="text-danger">{{ @$errors->first('visaExpiry') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="preferredIntake">Preferred Intake</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            {!! Form::text('preferredIntake', old('preferredIntake'), array('class' => 'form-control datepicker', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Select intake date' ))  !!}
                        </div>
                        @if ($errors->has('preferredIntake'))
                            <span class="text-danger">{{ @$errors->first('preferredIntake') }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                <div class="content-grid">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="address">Address</label>
                        {!! Form::text('address', old('address'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter full address' ))  !!}
                        @if ($errors->has('address'))
                            <span class="text-danger">{{ @$errors->first('address') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        {!! Form::text('city', old('city'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter city' ))  !!}
                        @if ($errors->has('city'))
                            <span class="text-danger">{{ @$errors->first('city') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <select class="form-control" name="state">
                            <option value="">- Select State -</option>
                            <option value="Australian Capital Territory" {{ old('state') == 'Australian Capital Territory' ? 'selected' : '' }}>Australian Capital Territory</option>
                            <option value="New South Wales" {{ old('state') == 'New South Wales' ? 'selected' : '' }}>New South Wales</option>
                            <option value="Northern Territory" {{ old('state') == 'Northern Territory' ? 'selected' : '' }}>Northern Territory</option>
                            <option value="Queensland" {{ old('state') == 'Queensland' ? 'selected' : '' }}>Queensland</option>
                            <option value="South Australia" {{ old('state') == 'South Australia' ? 'selected' : '' }}>South Australia</option>
                            <option value="Tasmania" {{ old('state') == 'Tasmania' ? 'selected' : '' }}>Tasmania</option>
                            <option value="Victoria" {{ old('state') == 'Victoria' ? 'selected' : '' }}>Victoria</option>
                            <option value="Western Australia" {{ old('state') == 'Western Australia' ? 'selected' : '' }}>Western Australia</option>
                        </select>
                        @if ($errors->has('state'))
                            <span class="text-danger">{{ @$errors->first('state') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="zip">Post Code</label>
                        {!! Form::text('zip', old('zip'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter postcode' ))  !!}
                        @if ($errors->has('zip'))
                            <span class="text-danger">{{ @$errors->first('zip') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select class="form-control select2" name="country">
                            @foreach(\App\Models\Country::all() as $list)
                                <option value="{{@$list->sortname}}" {{ old('country', 'AU') == $list->sortname ? 'selected' : '' }}>{{@$list->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('country'))
                            <span class="text-danger">{{ @$errors->first('country') }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h3><i class="fas fa-link"></i> Related Files</h3>
                <div class="content-grid">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="related_files">Similar Related Files</label>
                        <select class="form-control js-data-example-ajaxcc select2" name="related_files[]" multiple>
                        </select>
                        @if ($errors->has('related_files'))
                            <span class="text-danger">{{ @$errors->first('related_files') }}</span>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <!-- Skills & History -->
        <div class="form-content-section">
            <section class="form-section">
                <h3><i class="fas fa-briefcase"></i> Professional Details</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="nomi_occupation">Nominated Occupation</label>
                        {!! Form::text('nomi_occupation', old('nomi_occupation'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter occupation' ))  !!}
                        @if ($errors->has('nomi_occupation'))
                            <span class="text-danger">{{ @$errors->first('nomi_occupation') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="skill_assessment">Skill Assessment</label>
                        <select class="form-control" name="skill_assessment">
                            <option value="">Select</option>
                            <option value="Yes" {{ old('skill_assessment') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('skill_assessment') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                        @if ($errors->has('skill_assessment'))
                            <span class="text-danger">{{ @$errors->first('skill_assessment') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="high_quali_aus">Highest Qualification in Australia</label>
                        {!! Form::text('high_quali_aus', old('high_quali_aus'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
                        @if ($errors->has('high_quali_aus'))
                            <span class="text-danger">{{ @$errors->first('high_quali_aus') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="high_quali_overseas">Highest Qualification Overseas</label>
                        {!! Form::text('high_quali_overseas', old('high_quali_overseas'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter qualification' ))  !!}
                        @if ($errors->has('high_quali_overseas'))
                            <span class="text-danger">{{ @$errors->first('high_quali_overseas') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="relevant_work_exp_aus">Relevant work experience in Australia</label>
                        {!! Form::text('relevant_work_exp_aus', old('relevant_work_exp_aus'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'e.g., 2 years' ))  !!}
                        @if ($errors->has('relevant_work_exp_aus'))
                            <span class="text-danger">{{ @$errors->first('relevant_work_exp_aus') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="relevant_work_exp_over">Relevant work experience in Overseas</label>
                        {!! Form::text('relevant_work_exp_over', old('relevant_work_exp_over'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'e.g., 5 years' ))  !!}
                        @if ($errors->has('relevant_work_exp_over'))
                            <span class="text-danger">{{ @$errors->first('relevant_work_exp_over') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="married_partner">Overall English score</label>
                        {!! Form::text('married_partner', old('married_partner'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter score' ))  !!}
                        @if ($errors->has('married_partner'))
                            <span class="text-danger">{{ @$errors->first('married_partner') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label style="display:block;" for="naati_py">Naati/PY</label>
                        <div style="white-space: nowrap;">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="Naati" value="Naati" name="naati_py[]" {{ in_array('Naati', old('naati_py', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="Naati">Naati</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="py" value="PY" name="naati_py[]" {{ in_array('PY', old('naati_py', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="py">PY</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total_points">Total Points</label>
                        {!! Form::text('total_points', old('total_points'), array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter points' ))  !!}
                        @if ($errors->has('total_points'))
                            <span class="text-danger">{{ @$errors->first('total_points') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="start_process">When You want to start Process</label>
                        <select class="form-control" name="start_process">
                            <option value="">Select</option>
                            <option value="As soon As Possible" {{ old('start_process') == 'As soon As Possible' ? 'selected' : '' }}>As soon As Possible</option>
                            <option value="In Next 3 Months" {{ old('start_process') == 'In Next 3 Months' ? 'selected' : '' }}>In Next 3 Months</option>
                            <option value="In Next 6 Months" {{ old('start_process') == 'In Next 6 Months' ? 'selected' : '' }}>In Next 6 Months</option>
                            <option value="Advise Only" {{ old('start_process') == 'Advise Only' ? 'selected' : '' }}>Advise Only</option>
                        </select>
                        @if ($errors->has('start_process'))
                            <span class="text-danger">{{ @$errors->first('start_process') }}</span>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <!-- Internal Information -->
        <div class="form-content-section">
            <section class="form-section">
                <h3><i class="fas fa-cogs"></i> Internal Information</h3>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="service">Service <span class="span_req">*</span></label>
                        <select class="form-control select2" name="service" data-valid="required" required>
                            <option value="">- Select Lead Service -</option>
                            @foreach(\App\Models\LeadService::orderby('name', 'ASC')->get() as $leadservlist)
                                <option value="{{$leadservlist->name}}" {{ old('service') == $leadservlist->name ? 'selected' : '' }}>{{$leadservlist->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('service'))
                            <span class="text-danger">{{ @$errors->first('service') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="assign_to">Assign To <span class="span_req">*</span></label>
                        <select name="assign_to" id="assign_to" class="form-control select2" data-valid="required" required>
                            <option value="">- Select Assignee -</option>
                            <?php
                                $admins = \App\Models\Admin::where('role','!=',7)->orderby('first_name','ASC')->get();
                                foreach($admins as $admin){
                                    $branchname = \App\Models\Branch::where('id',$admin->office_id)->first();
                            ?>
                            <option value="<?php echo $admin->id; ?>" {{ old('assign_to') == $admin->id ? 'selected' : '' }}><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
                            <?php } ?>
                        </select>
                        @if ($errors->has('assign_to'))
                            <span class="text-danger">{{ @$errors->first('assign_to') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" data-valid="">
                            <option value="">Select Status</option>
                            <option value="Unassigned" {{ old('status') == 'Unassigned' ? 'selected' : '' }}>Unassigned</option>
                            <option value="Assigned" {{ old('status') == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="In-Progress" {{ old('status') == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
                            <option value="Closed" {{ old('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        @if ($errors->has('status'))
                            <span class="text-danger">{{ @$errors->first('status') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="lead_quality">Quality <span class="span_req">*</span></label>
                        <select name="lead_quality" id="lead_quality" class="form-control" data-valid="required" required>
                            <option value="1" {{ old('lead_quality', '1') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('lead_quality') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('lead_quality') == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ old('lead_quality') == '4' ? 'selected' : '' }}>4</option>
                            <option value="5" {{ old('lead_quality') == '5' ? 'selected' : '' }}>5</option>
                        </select>
                        @if ($errors->has('lead_quality'))
                            <span class="text-danger">{{ @$errors->first('lead_quality') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="lead_source">Source <span class="span_req">*</span></label>
                        <select name="source" id="lead_source" class="form-control select2" data-valid="" required>
                            <option value="">- Source -</option>
                            <option value="Sub Agent" {{ old('source') == 'Sub Agent' ? 'selected' : '' }}>Sub Agent</option>
                            @foreach(\App\Models\Source::all() as $sources)
                                <option value="{{$sources->name}}" {{ old('source') == $sources->name ? 'selected' : '' }}>{{$sources->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('lead_source'))
                            <span class="text-danger">{{ @$errors->first('lead_source') }}</span>
                        @endif
                    </div>
                    <div class="form-group is_subagent" style="display:none;">
                        <label for="subagent">Sub Agent <span class="span_req">*</span></label>
                        <select class="form-control select2" name="subagent">  
                            <option value="">-- Choose a sub agent --</option>
                            @foreach(\App\Models\Agent::all() as $agentlist)
                                <option value="{{$agentlist->id}}" {{ old('subagent') == $agentlist->id ? 'selected' : '' }}>{{$agentlist->full_name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('subagent'))
                            <span class="text-danger">{{ @$errors->first('subagent') }}</span>
                        @endif
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="tags_label">Tags/Label</label>
                        <select multiple class="form-control select2" name="tagname[]">
                            <option value="">-- Search & Select tag --</option>
                            @foreach(\App\Models\Tag::all() as $tags)
                                <option value="{{$tags->id}}" {{ in_array($tags->id, old('tagname', [])) ? 'selected' : '' }}>{{$tags->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="comments_note">Comments / Note</label>
                        <textarea class="form-control" name="comments_note" placeholder="Enter comments or notes" rows="4">{{old('comments_note')}}</textarea>
                        @if ($errors->has('comments_note')) 
                            <span class="text-danger">{{ @$errors->first('comments_note') }}</span>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        {!! Form::close()  !!}
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
// Function to initialize select2 when available
function initSelect2() {
    if (typeof $.fn.select2 !== 'undefined') {
        // Initialize all select2 dropdowns that aren't already initialized
        $('.select2').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                try {
                    $(this).select2({
                        width: '100%',
                        theme: 'default',
                        dropdownAutoWidth: true
                    });
                } catch(e) {
                    console.warn('Error initializing select2:', e);
                }
            }
        });
        return true;
    }
    return false;
}

// Phone and email validation
jQuery(document).ready(function($){
    // Initialize select2 - wait for vendor libraries if available
    function initializeSelect2WhenReady() {
        if (typeof window.vendorLibsReady !== 'undefined') {
            // Use the vendorLibsReady promise if available
            window.vendorLibsReady.then(function() {
                initSelect2();
            }).catch(function() {
                // Fallback if promise fails
                setTimeout(function() {
                    if (!initSelect2()) {
                        console.warn('Select2 initialization failed after vendor libs promise.');
                    }
                }, 500);
            });
        } else {
            // Fallback: try immediately, then retry
            if (!initSelect2()) {
                var retryCount = 0;
                var maxRetries = 10;
                var retryInterval = setInterval(function() {
                    retryCount++;
                    if (initSelect2() || retryCount >= maxRetries) {
                        clearInterval(retryInterval);
                        if (retryCount >= maxRetries && typeof $.fn.select2 === 'undefined') {
                            console.warn('Select2 library failed to load after ' + maxRetries + ' attempts.');
                        }
                    }
                }, 200);
            }
        }
    }
    
    initializeSelect2WhenReady();
    
    $('#checkphone').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
            $.ajax({
                url: '{{URL::to('admin/checkclientexist')}}',
                type:'GET',
                data:{vl:v,type:'phone'},
                success:function(res){
                    if(res == 1){
                        alert('Phone number is already exist in our record.');
                    }
                }
            });
        }
    });
    
    $('#checkemail').on('blur', function(){
        var v = $(this).val();
        if(v != ''){
            $.ajax({
                url: '{{URL::to('admin/checkclientexist')}}',
                type:'GET',
                data:{vl:v,type:'email'},
                success:function(res){
                    if(res == 1){
                        alert('Email is already exist in our record.');
                    }
                }
            });
        }
    });

    // Related files select2
    $('.js-data-example-ajaxcc').select2({
        multiple: true,
        closeOnSelect: false,
        minimumInputLength: 1,
        ajax: {
            url: '{{URL::to('/clients/get-recipients')}}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.items
                };
            },
            cache: true
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });

    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text;
        }
        var $container = $(
            "<div class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +
                "<div class='ag-flex ag-align-start'>" +
                    "<div class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                    "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small></div>" +
                "</div>" +
            "</div>" +
            "<div class='ag-flex ag-flex-column ag-align-end'>" +
                "<span class='ui label yellow select2-result-repository__statistics'></span>" +
            "</div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__title").text(repo.name);
        $container.find(".select2-result-repository__description").text(repo.email);
        $container.find(".select2-result-repository__statistics").append(repo.status);
        return $container;
    }

    function formatRepoSelection (repo) {
        return repo.name || repo.text;
    }

    // Show/hide subagent field based on source
    $('#lead_source').on('change', function(){
        if($(this).val() == 'Sub Agent'){
            $('.is_subagent').show();
        } else {
            $('.is_subagent').hide();
        }
    });
    
    // Trigger on page load if source is already Sub Agent
    if($('#lead_source').val() == 'Sub Agent'){
        $('.is_subagent').show();
    }
});
</script>
@endsection
