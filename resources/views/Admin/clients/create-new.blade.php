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

/* Related Files Select2 specific styling */
.js-data-example-ajaxcc + .select2-container {
    width: 100% !important;
}

.select2-container--default .select2-search--inline .select2-search__field {
    padding: 6px 8px !important;
    font-size: 13px !important;
    border: none !important;
    outline: none !important;
    min-width: 200px !important;
}

.select2-container--default .select2-search--inline .select2-search__field:focus {
    outline: none !important;
    border: none !important;
}

.select2-dropdown {
    border: 2px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    margin-top: 4px !important;
}

.select2-results__option {
    padding: 0 !important;
}

.select2-results__option--highlighted {
    background-color: #f1f5f9 !important;
}

/* Checkbox styling */
.form-check-input {
    accent-color: #6366f1;
    cursor: pointer;
    margin: 0;
    margin-right: 8px;
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

.form-check-label {
    cursor: pointer;
    font-size: 13px;
    color: #475569;
    margin: 0;
    display: flex;
    align-items: center;
}

/* Naati/PY Checkbox Styling - Fixed */
.naati-checkbox-wrapper {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    padding: 8px 0;
}

.naati-checkbox-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    background: #ffffff;
    transition: all 0.2s ease;
    cursor: pointer;
    user-select: none;
    margin: 0;
    position: relative;
}

.naati-checkbox-item:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.naati-checkbox-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
    accent-color: #6366f1;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
    pointer-events: auto;
}

.naati-checkbox-label {
    margin: 0;
    cursor: pointer;
    font-size: 13px;
    color: #475569;
    font-weight: 500;
    user-select: none;
    pointer-events: none;
    position: relative;
    z-index: 1;
}

.naati-checkbox-item input[type="checkbox"]:checked ~ .naati-checkbox-label {
    color: #6366f1 !important;
    font-weight: 600 !important;
}

.naati-checkbox-item:has(input[type="checkbox"]:checked),
.naati-checkbox-item.checked {
    border-color: #6366f1 !important;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05)) !important;
}

.naati-checkbox-item.checked .naati-checkbox-label {
    color: #6366f1 !important;
    font-weight: 600 !important;
}

/* Contact Information Section - Two Column Design */
.contact-information-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 0;
}

.contact-column {
    display: flex;
    flex-direction: column;
}

.contact-column-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.contact-column-header h4 {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.contact-column-header h4 i {
    color: #6366f1;
    font-size: 14px;
}

.contact-add-btn {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
}

.contact-add-btn:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 4px 10px rgba(99, 102, 241, 0.35);
    transform: translateY(-1px);
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    padding: 10px;
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.contact-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.contact-item-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    box-shadow: 0 1px 3px rgba(59, 130, 246, 0.3);
}

.contact-item-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 6px 8px;
    font-size: 13px;
    color: #1e293b;
    outline: none;
}

.contact-item-input:focus {
    background: #f8fafc;
    border-radius: 4px;
}

.contact-item-check {
    color: #10b981;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border: 2px solid #10b981;
    border-radius: 4px;
    background: rgba(16, 185, 129, 0.1);
    flex-shrink: 0;
}

.contact-item-remove {
    color: #ef4444;
    cursor: pointer;
    font-size: 14px;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.contact-item-remove:hover {
    background: #fef2f2;
    color: #dc2626;
}

.contact-item-hidden-inputs {
    display: none;
}

@media (max-width: 768px) {
    .contact-information-section {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* Modal Styles */
#phone-modal,
#email-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

#phone-modal > div,
#email-modal > div {
    background: white;
    padding: 24px;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    max-height: 90vh;
    overflow-y: auto;
}

#phone-modal h3,
#email-modal h3 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
}

#phone-modal .cus_field_input,
#email-modal .cus_field_input {
    display: flex;
    align-items: stretch;
    gap: 0;
}

#phone-modal .country_code,
#email-modal .country_code {
    display: flex;
    align-items: center;
}

#phone-modal .country_code input,
#email-modal .country_code input {
    width: 80px;
    padding: 8px 4px;
    border: 2px solid #e2e8f0;
    border-right: none;
    border-radius: 6px 0 0 6px;
    text-align: center;
    font-size: 13px;
    background: #f8fafc;
}

#phone-modal .tel_input,
#email-modal .tel_input {
    flex: 1;
    border-radius: 0 6px 6px 0;
    border-left: none;
}

#phone-modal .modal-actions,
#email-modal .modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 20px;
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
            <section class="form-section" style="margin-bottom: 0;">
                <h3><i class="fas fa-user"></i> Contact Information</h3>
                <div class="contact-information-section">
                    <!-- Phone Numbers Column -->
                    <div class="contact-column">
                        <div class="contact-column-header">
                            <h4><i class="fas fa-phone"></i> PHONE NUMBERS</h4>
                            <button type="button" class="contact-add-btn" onclick="addPhoneNumber()">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                        <div id="phone-numbers-container">
                            @if(old('phone'))
                                @php
                                    $phones = is_array(old('phone')) ? old('phone') : [old('phone')];
                                    $phoneTypes = is_array(old('contact_type')) ? old('contact_type') : [old('contact_type', 'Personal')];
                                    $countryCodes = is_array(old('country_code')) ? old('country_code') : [old('country_code', '+61')];
                                @endphp
                                @foreach($phones as $index => $phone)
                                    <div class="contact-item" data-index="{{ $index }}">
                                        <span class="contact-item-badge">{{ $phoneTypes[$index] ?? 'Personal' }}</span>
                                        <input type="text" class="contact-item-input" value="{{ $countryCodes[$index] ?? '+61' }} {{ $phone }}" readonly>
                                        <input type="hidden" name="phone[]" value="{{ $phone }}">
                                        <input type="hidden" name="contact_type[]" value="{{ $phoneTypes[$index] ?? 'Personal' }}">
                                        <input type="hidden" name="country_code[]" value="{{ $countryCodes[$index] ?? '+61' }}">
                                        <div class="contact-item-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="contact-item-remove" onclick="removePhoneNumber(this)">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div id="phone-empty-message" style="display: {{ old('phone') ? 'none' : 'block' }}; text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">
                            No phone numbers added yet. Click "+ Add" to add one.
                        </div>
                    </div>

                    <!-- Email Addresses Column -->
                    <div class="contact-column">
                        <div class="contact-column-header">
                            <h4><i class="fas fa-envelope"></i> EMAIL ADDRESSES</h4>
                            <button type="button" class="contact-add-btn" onclick="addEmailAddress()">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                        <div id="email-addresses-container">
                            @if(old('email'))
                                @php
                                    $emails = is_array(old('email')) ? old('email') : [old('email')];
                                    $emailTypes = is_array(old('email_type')) ? old('email_type') : [old('email_type', 'Personal')];
                                @endphp
                                @foreach($emails as $index => $email)
                                    <div class="contact-item" data-index="{{ $index }}">
                                        <span class="contact-item-badge">{{ $emailTypes[$index] ?? 'Personal' }}</span>
                                        <input type="text" class="contact-item-input" value="{{ $email }}" readonly>
                                        <input type="hidden" name="email[]" value="{{ $email }}">
                                        <input type="hidden" name="email_type[]" value="{{ $emailTypes[$index] ?? 'Personal' }}">
                                        <div class="contact-item-check">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="contact-item-remove" onclick="removeEmailAddress(this)">
                                            <i class="fas fa-times"></i>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div id="email-empty-message" style="display: {{ old('email') ? 'none' : 'block' }}; text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">
                            No email addresses added yet. Click "+ Add" to add one.
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Hidden modal/form for adding phone numbers -->
        <div id="phone-modal">
            <div>
                <h3>Add Phone Number</h3>
                <div class="form-group">
                    <label for="modal_contact_type">Contact Type <span class="span_req">*</span></label>
                    <select id="modal_contact_type" class="form-control">
                        <option value="Personal">Personal</option>
                        <option value="Office">Office</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_phone">Phone Number <span class="span_req">*</span></label>
                    <div class="cus_field_input">
                        <div class="country_code"> 
                            <input class="telephone" id="modal_telephone" type="tel" readonly>
                        </div>	
                        <input type="text" id="modal_phone" class="form-control tel_input" placeholder="Enter phone number">
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePhoneModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="savePhoneNumber()">Add Phone</button>
                </div>
            </div>
        </div>

        <!-- Hidden modal/form for adding email addresses -->
        <div id="email-modal">
            <div>
                <h3>Add Email Address</h3>
                <div class="form-group">
                    <label for="modal_email_type">Email Type <span class="span_req">*</span></label>
                    <select id="modal_email_type" class="form-control">
                        <option value="Personal">Personal</option>
                        <option value="Business">Business</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_email">Email Address <span class="span_req">*</span></label>
                    <input type="email" id="modal_email" class="form-control" placeholder="Enter email address">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEmailModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveEmailAddress()">Add Email</button>
                </div>
            </div>
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
                        <label for="naati_py">Naati/PY</label>
                        <div class="naati-checkbox-wrapper">
                            <label class="naati-checkbox-item" for="Naati">
                                <input type="checkbox" id="Naati" value="Naati" name="naati_py[]" {{ in_array('Naati', old('naati_py', [])) ? 'checked' : '' }}>
                                <span class="naati-checkbox-label">Naati</span>
                            </label>
                            <label class="naati-checkbox-item" for="py">
                                <input type="checkbox" id="py" value="PY" name="naati_py[]" {{ in_array('PY', old('naati_py', [])) ? 'checked' : '' }}>
                                <span class="naati-checkbox-label">PY</span>
                            </label>
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

<script>
// Form validation before submission
document.getElementById('createClientForm').addEventListener('submit', function(e) {
    var phoneCount = document.querySelectorAll('#phone-numbers-container .contact-item').length;
    var emailCount = document.querySelectorAll('#email-addresses-container .contact-item').length;
    
    if (phoneCount === 0) {
        e.preventDefault();
        alert('Please add at least one phone number');
        return false;
    }
    
    if (emailCount === 0) {
        e.preventDefault();
        alert('Please add at least one email address');
        return false;
    }
    
    // Set the first phone and email as the main ones for backward compatibility
    var firstPhone = document.querySelector('#phone-numbers-container .contact-item input[name="phone[]"]');
    var firstEmail = document.querySelector('#email-addresses-container .contact-item input[name="email[]"]');
    var firstPhoneType = document.querySelector('#phone-numbers-container .contact-item input[name="contact_type[]"]');
    var firstEmailType = document.querySelector('#email-addresses-container .contact-item input[name="email_type[]"]');
    var firstCountryCode = document.querySelector('#phone-numbers-container .contact-item input[name="country_code[]"]');
    
    if (firstPhone) {
        // Create hidden inputs for main phone/email if they don't exist
        if (!document.querySelector('input[name="phone"]')) {
            var phoneInput = document.createElement('input');
            phoneInput.type = 'hidden';
            phoneInput.name = 'phone';
            phoneInput.value = firstPhone.value;
            this.appendChild(phoneInput);
        } else {
            document.querySelector('input[name="phone"]').value = firstPhone.value;
        }
        
        if (!document.querySelector('input[name="contact_type"]')) {
            var contactTypeInput = document.createElement('input');
            contactTypeInput.type = 'hidden';
            contactTypeInput.name = 'contact_type';
            contactTypeInput.value = firstPhoneType ? firstPhoneType.value : 'Personal';
            this.appendChild(contactTypeInput);
        }
        
        if (!document.querySelector('input[name="country_code"]')) {
            var countryCodeInput = document.createElement('input');
            countryCodeInput.type = 'hidden';
            countryCodeInput.name = 'country_code';
            countryCodeInput.value = firstCountryCode ? firstCountryCode.value : '+61';
            this.appendChild(countryCodeInput);
        }
    }
    
    if (firstEmail) {
        if (!document.querySelector('input[name="email"]')) {
            var emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'email';
            emailInput.value = firstEmail.value;
            this.appendChild(emailInput);
        } else {
            document.querySelector('input[name="email"]').value = firstEmail.value;
        }
        
        if (!document.querySelector('input[name="email_type"]')) {
            var emailTypeInput = document.createElement('input');
            emailTypeInput.type = 'hidden';
            emailTypeInput.name = 'email_type';
            emailTypeInput.value = firstEmailType ? firstEmailType.value : 'Personal';
            this.appendChild(emailTypeInput);
        }
    }
});
</script>

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

    // Related files select2 - Initialize after Select2 is ready
    function initializeRelatedFilesSelect2() {
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('Select2 not loaded yet, retrying...');
            setTimeout(initializeRelatedFilesSelect2, 200);
            return;
        }
        
        // Destroy existing instance if any
        if ($('.js-data-example-ajaxcc').hasClass('select2-hidden-accessible')) {
            $('.js-data-example-ajaxcc').select2('destroy');
        }
        
        $('.js-data-example-ajaxcc').select2({
            multiple: true,
            closeOnSelect: false,
            minimumInputLength: 1,
            placeholder: 'Type to search for related clients...',
            allowClear: true,
            ajax: {
                url: '{{URL::to('/clients/get-recipients')}}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    if (!data || !data.items) {
                        return { results: [] };
                    }
                    return {
                        results: data.items.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name || item.text || 'Unknown',
                                name: item.name || item.text || 'Unknown',
                                email: item.email || '',
                                status: item.status || 'Client'
                            };
                        })
                    };
                },
                cache: true
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection,
            escapeMarkup: function(markup) {
                return markup; // Let our custom formatter handle the markup
            }
        });
    }
    
    // Initialize when ready
    if (typeof window.vendorLibsReady !== 'undefined') {
        window.vendorLibsReady.then(function() {
            setTimeout(initializeRelatedFilesSelect2, 100);
        }).catch(function() {
            setTimeout(initializeRelatedFilesSelect2, 500);
        });
    } else {
        // Fallback: try after a delay
        setTimeout(initializeRelatedFilesSelect2, 500);
    }

    function formatRepo (repo) {
        if (repo.loading) {
            return repo.text || 'Searching...';
        }
        
        var name = repo.name || repo.text || 'Unknown';
        var email = repo.email || '';
        var status = repo.status || 'Client';
        
        var $container = $(
            "<div class='select2-result-repository' style='padding: 8px; border-bottom: 1px solid #e2e8f0;'>" +
                "<div style='display: flex; justify-content: space-between; align-items: center;'>" +
                    "<div style='flex: 1;'>" +
                        "<div style='font-weight: 600; color: #1e293b; margin-bottom: 4px;'>" + name + "</div>" +
                        "<div style='font-size: 12px; color: #64748b;'>" + email + "</div>" +
                    "</div>" +
                    "<div style='margin-left: 12px;'>" +
                        "<span style='background: #fbbf24; color: #78350f; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;'>" + status + "</span>" +
                    "</div>" +
                "</div>" +
            "</div>"
        );
        return $container;
    }

    function formatRepoSelection (repo) {
        return repo.name || repo.text || 'Unknown';
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
    
    // Handle Naati/PY checkbox state changes
    // Use 'change' event only - label's 'for' attribute handles clicks automatically
    $('.naati-checkbox-item input[type="checkbox"]').on('change', function() {
        updateCheckboxState(this);
    });
    
    // Initialize checked state on page load
    $('.naati-checkbox-item input[type="checkbox"]').each(function() {
        updateCheckboxState(this);
    });
    
    function updateCheckboxState(checkbox) {
        var $item = $(checkbox).closest('.naati-checkbox-item');
        var $label = $item.find('.naati-checkbox-label');
        
        if ($(checkbox).is(':checked')) {
            $item.addClass('checked');
            $label.css({
                'color': '#6366f1',
                'font-weight': '600'
            });
        } else {
            $item.removeClass('checked');
            $label.css({
                'color': '#475569',
                'font-weight': '500'
            });
        }
    }
});

// Phone Number Management
let phoneCounter = {{ old('phone') ? (is_array(old('phone')) ? count(old('phone')) : 1) : 0 }};
let emailCounter = {{ old('email') ? (is_array(old('email')) ? count(old('email')) : 1) : 0 }};

function addPhoneNumber() {
    document.getElementById('phone-modal').style.display = 'flex';
    document.getElementById('modal_phone').value = '';
    document.getElementById('modal_contact_type').value = 'Personal';
    
    // Initialize telephone input - wait for libraries to load
    setTimeout(function() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.intlTelInput !== 'undefined') {
            // Destroy existing instance if any
            if (jQuery('#modal_telephone').data('intlTelInput')) {
                jQuery('#modal_telephone').intlTelInput('destroy');
            }
            // Initialize with jQuery plugin
            jQuery('#modal_telephone').intlTelInput({
                preferredCountries: ["au", "in", "us", "gb"],
                initialCountry: "au"
            });
        } else if (typeof intlTelInput !== 'undefined') {
            // Fallback to vanilla JS version
            var telInput = document.querySelector("#modal_telephone");
            if (telInput && !telInput.intlTelInput) {
                intlTelInput(telInput, {
                    preferredCountries: ["au", "in", "us", "gb"],
                    initialCountry: "au"
                });
            }
        }
    }, 100);
}

function closePhoneModal() {
    document.getElementById('phone-modal').style.display = 'none';
}

function savePhoneNumber() {
    var phoneType = document.getElementById('modal_contact_type').value;
    var phoneInput = document.getElementById('modal_phone').value;
    var countryCode = '+61';
    
    // Get country code from intlTelInput
    if (typeof jQuery !== 'undefined' && jQuery('#modal_telephone').data('intlTelInput')) {
        var iti = jQuery('#modal_telephone').intlTelInput('getSelectedCountryData');
        if (iti && iti.dialCode) {
            countryCode = '+' + iti.dialCode;
        }
    } else if (typeof intlTelInput !== 'undefined') {
        var telInput = document.querySelector("#modal_telephone");
        if (telInput && telInput.intlTelInput) {
            var iti = telInput.intlTelInput;
            var countryData = iti.getSelectedCountryData();
            if (countryData && countryData.dialCode) {
                countryCode = '+' + countryData.dialCode;
            }
        }
    }
    
    if (!phoneInput.trim()) {
        alert('Please enter a phone number');
        return;
    }
    
    var fullPhone = countryCode + ' ' + phoneInput;
    var container = document.getElementById('phone-numbers-container');
    var index = phoneCounter++;
    
    var phoneItem = document.createElement('div');
    phoneItem.className = 'contact-item';
    phoneItem.setAttribute('data-index', index);
    phoneItem.innerHTML = `
        <span class="contact-item-badge">${phoneType}</span>
        <input type="text" class="contact-item-input" value="${fullPhone}" readonly>
        <input type="hidden" name="phone[]" value="${phoneInput}">
        <input type="hidden" name="contact_type[]" value="${phoneType}">
        <input type="hidden" name="country_code[]" value="${countryCode}">
        <div class="contact-item-check">
            <i class="fas fa-check"></i>
        </div>
        <div class="contact-item-remove" onclick="removePhoneNumber(this)">
            <i class="fas fa-times"></i>
        </div>
    `;
    
    container.appendChild(phoneItem);
    
    // Hide empty message
    var emptyMsg = document.getElementById('phone-empty-message');
    if (emptyMsg) {
        emptyMsg.style.display = 'none';
    }
    
    closePhoneModal();
    
    // Validate phone uniqueness
    validatePhoneUniqueness(phoneInput);
}

function removePhoneNumber(element) {
    if (confirm('Are you sure you want to remove this phone number?')) {
        var container = document.getElementById('phone-numbers-container');
        element.closest('.contact-item').remove();
        
        // Show empty message if no phones left
        if (container.querySelectorAll('.contact-item').length === 0) {
            var emptyMsg = document.getElementById('phone-empty-message');
            if (emptyMsg) {
                emptyMsg.style.display = 'block';
            }
        }
    }
}

function addEmailAddress() {
    document.getElementById('email-modal').style.display = 'flex';
    document.getElementById('modal_email').value = '';
    document.getElementById('modal_email_type').value = 'Personal';
}

function closeEmailModal() {
    document.getElementById('email-modal').style.display = 'none';
}

function saveEmailAddress() {
    var emailType = document.getElementById('modal_email_type').value;
    var emailInput = document.getElementById('modal_email').value;
    
    if (!emailInput.trim()) {
        alert('Please enter an email address');
        return;
    }
    
    // Basic email validation
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput)) {
        alert('Please enter a valid email address');
        return;
    }
    
    var container = document.getElementById('email-addresses-container');
    var index = emailCounter++;
    
    var emailItem = document.createElement('div');
    emailItem.className = 'contact-item';
    emailItem.setAttribute('data-index', index);
    emailItem.innerHTML = `
        <span class="contact-item-badge">${emailType}</span>
        <input type="text" class="contact-item-input" value="${emailInput}" readonly>
        <input type="hidden" name="email[]" value="${emailInput}">
        <input type="hidden" name="email_type[]" value="${emailType}">
        <div class="contact-item-check">
            <i class="fas fa-check"></i>
        </div>
        <div class="contact-item-remove" onclick="removeEmailAddress(this)">
            <i class="fas fa-times"></i>
        </div>
    `;
    
    container.appendChild(emailItem);
    
    // Hide empty message
    var emptyMsg = document.getElementById('email-empty-message');
    if (emptyMsg) {
        emptyMsg.style.display = 'none';
    }
    
    closeEmailModal();
    
    // Validate email uniqueness
    validateEmailUniqueness(emailInput);
}

function removeEmailAddress(element) {
    if (confirm('Are you sure you want to remove this email address?')) {
        var container = document.getElementById('email-addresses-container');
        element.closest('.contact-item').remove();
        
        // Show empty message if no emails left
        if (container.querySelectorAll('.contact-item').length === 0) {
            var emptyMsg = document.getElementById('email-empty-message');
            if (emptyMsg) {
                emptyMsg.style.display = 'block';
            }
        }
    }
}

function validatePhoneUniqueness(phone) {
    jQuery.ajax({
        url: '{{URL::to('admin/checkclientexist')}}',
        type: 'GET',
        data: {vl: phone, type: 'phone'},
        success: function(res) {
            if(res == 1) {
                alert('Phone number is already exist in our record.');
            }
        }
    });
}

function validateEmailUniqueness(email) {
    jQuery.ajax({
        url: '{{URL::to('admin/checkclientexist')}}',
        type: 'GET',
        data: {vl: email, type: 'email'},
        success: function(res) {
            if(res == 1) {
                alert('Email is already exist in our record.');
            }
        }
    });
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    var phoneModal = document.getElementById('phone-modal');
    var emailModal = document.getElementById('email-modal');
    
    if (event.target === phoneModal) {
        closePhoneModal();
    }
    if (event.target === emailModal) {
        closeEmailModal();
    }
});
</script>
@endsection
