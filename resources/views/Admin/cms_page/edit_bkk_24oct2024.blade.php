@extends('layouts.admin')
@section('title', 'Edit Page')

@section('content')

<!-- Main Content -->
<div class="main-content">
<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<!-- Flash Message Start -->
					<div class="server-error">
						@include('../Elements/flash-message')
					</div>
					<!-- Flash Message End -->
				</div> 
				<div class="col-md-12">
					<div class="card card-primary">
					  <div class="card-header">
						<h3 class="card-title">Edit Page</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  <!-- form start -->
                	  {{ Form::open(array('url' => 'admin/cms_pages/edit', 'name'=>"edit-template", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					   {{ Form::hidden('id', @$fetchedData->id) }}
						<div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="{{route('admin.cms_pages.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								{{ Form::button('<i class="fa fa-save"></i> Update Page', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-template")' ]) }}
							</div>
							<div class="form-group row"> 
								<label for="title" class="col-sm-2 col-form-label">Name <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								{{ Form::text('title', @$fetchedData->title, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Name' )) }}
								@if ($errors->has('title'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('title') }}</strong>
									</span> 
								@endif
								</div>
							</div>
							
							<div class="form-group row">
								<label for="slug" class="col-sm-2 col-form-label">Slug <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									{{ Form::text('slug', @$fetchedData->slug, array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Slug' )) }}
									@if ($errors->has('slug'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('slug') }}</strong>
										</span>
									@endif
								</div>
							</div>
						  
							<div class="form-group row"> 
								<label for="image" class="col-sm-2 col-form-label">Image</label>
								<div class="col-sm-10">
									<input type="hidden" id="old_image" name="old_image" value="{{@$fetchedData->image}}" />
									<input type="file" name="image" class="form-control" autocomplete="off"  />									
									<div class="show-uploded-img">	
										@if(@$fetchedData->image != '')
											<img width="70" src="{{URL::to('/public/img/cmspage')}}/{{@$fetchedData->image}}" class="img-avatar"/>
										@endif
										@if ($errors->has('image'))
											<span class="custom-error" role="alert">
												<strong>{{ @$errors->first('image') }}</strong>
											</span> 
										@endif
									</div>  
								</div>
							</div>
							<div class="form-group row">
								<label for="description" class="col-sm-2 col-form-label">Description <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									<!--<textarea name="description" data-valid="required" value="" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">{{@$fetchedData->content}}</textarea>-->
									
									<textarea class="form-control"  id="description" placeholder="Please Add Description Here" rows="5" name="description">{{@$fetchedData->content}}</textarea>

									@if ($errors->has('description'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('description') }}</strong>
										</span> 
									@endif
								</div> 
							</div>
							<div class="form-group row"> 
								<label for="meta_title" class="col-sm-2 col-form-label">Meta Title </label> 
								<div class="col-sm-10">
								{{ Form::text('meta_title', @$fetchedData->meta_title, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Meta Title' )) }}
								@if ($errors->has('meta_title'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('meta_title') }}</strong>
									</span> 
								@endif
								</div>
							</div>
							<div class="form-group row">
								<label for="meta_description" class="col-sm-2 col-form-label">Meta Description </label>
								<div class="col-sm-10">
									<textarea name="meta_description" data-valid="" value="" class="form-control" placeholder="Please Add Description Here">{{@$fetchedData->meta_description}}</textarea>
									@if ($errors->has('meta_description'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('meta_description') }}</strong>
										</span> 
									@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="meta_keyward" class="col-sm-2 col-form-label">Meta Keyward</label>
								<div class="col-sm-10">
								{{ Form::text('meta_keyward', @$fetchedData->meta_keyward, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Meta Keyward' )) }}
								@if ($errors->has('meta_keyward'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('meta_keyward') }}</strong>
									</span> 
								@endif
								</div>
							</div>
							<div class="form-group float-right">
								{{ Form::button('<i class="fa fa-save"></i> Update Page', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("edit-template")' ]) }}
							</div> 
						</div> 
					  {{ Form::close() }}
					</div>	   
				</div>	
			</div>
		</div>
	</section>
</div>
@endsection
@section('scripts')
<script src="{{ asset('public/assets/ckeditor/ckeditor.js') }}" type="text/javascript"></script>
<script>
var sharedCKEditorToolbarConfig = {
    toolbar: [
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [  'Table', 'HorizontalRule',   'SpecialChar', 'PageBreak' ] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor', 'EmojiPanel' ] },
            { name: 'document', items: [ 'ExportPdf', 'Preview', 'Print', '-', 'Templates' ] },
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' , 'Source' ] },
        ] ,
    extraPlugins: 'textwatcher,textmatch,autocomplete,emoji'
};
CKEDITOR.replace('description',sharedCKEditorToolbarConfig);
</script>
@endsection