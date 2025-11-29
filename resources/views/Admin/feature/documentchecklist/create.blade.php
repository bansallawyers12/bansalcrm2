@extends('layouts.admin')
@section('title', 'Add Checklist')

@section('content')

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-body">
            {{ Form::open(array('url' => 'admin/documentchecklist/store', 'name'=>"add-checklist", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Add Checklist</h4>
                                <div class="card-header-action">
                                    <a href="{{route('admin.feature.documentchecklist.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 col-md-3 col-lg-3">
                        @include('../Elements/Admin/setting')
                    </div>
                    <div class="col-9 col-md-9 col-lg-9">
                        <div class="card">
                            <div class="card-body">
                                <div id="accordion">
                                    <div class="accordion">
                                        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#primary_info" aria-expanded="true">
                                            <h4>Checklist Information</h4>
                                        </div>
                                        <div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
                                            <div class="row">
                                                <div class="col-12 col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label for="name">Name <span class="span_req">*</span></label>
                                                        {{ Form::text('name', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
                                                        @if ($errors->has('name'))
                                                            <span class="custom-error" role="alert">
                                                                <strong>{{ $errors->first('name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label for="doc_type">Document Type <span class="span_req">*</span></label>
                                                        <select name="doc_type" class="form-control" data-valid="required">
                                                            <option value="">Select Document Type</option>
                                                            <option value="1">Personal</option>
                                                            <option value="2">Visa</option>
                                                        </select>
                                                        @if ($errors->has('doc_type'))
                                                            <span class="custom-error" role="alert">
                                                                <strong>{{ $errors->first('doc_type') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group float-right">
                                    {{ Form::submit('Save Checklist', ['class'=>'btn btn-primary' ]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </section>
</div>

@endsection
