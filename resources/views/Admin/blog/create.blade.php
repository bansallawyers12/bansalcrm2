@extends('layouts.admin')
@section('title', 'Add New Blog')

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
						<h3 class="card-title">Create Blog</h3>
					  </div> 
					  <!-- /.card-header -->
					  <!-- form start -->
					  {{ Form::open(array('url' => 'admin/blog/store', 'name'=>"add-blog", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
					   
						<div class="card-body">
							<div class="form-group" style="text-align:right;">
								<a style="margin-right:5px;" href="{{route('admin.blog.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>  
								{{ Form::button('<i class="fa fa-save"></i> Save Blog', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-blog")' ]) }}
							</div>
							<div class="form-group row"> 
								<label for="title" class="col-sm-2 col-form-label">Title <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
								{{ Form::text('title', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Title' )) }}
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
									{{ Form::text('slug', '', array('class' => 'form-control', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Slug' )) }}
									@if ($errors->has('slug'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('slug') }}</strong>
										</span> 
									@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="parent_category" class="col-sm-2 col-form-label">Category <span style="color:#ff0000;">*</span></label>
								<div class="col-sm-10">
									<select class="form-control" name="parent_category">
										<option value="">- Select Category -</option>
										@if($categories)
											@foreach($categories as $category)
												<?php $dash=''; ?>
												<option value="{{$category->id}}">{{$category->name}}</option>
												@if(count($category->subcategory))	@include('/Admin/blogcategory/subCategoryList-option',['subcategories' => $category->subcategory])
												@endif
											@endforeach 
										@endif
									</select>
									@if ($errors->has('parent_category'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('parent_category') }}</strong>
										</span> 
									@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="image" class="col-sm-2 col-form-label">Featured Image/Video </label>
								<div class="col-sm-10">
									<div class="custom-file">
										<input type="file" id="image" name="image" class="custom-file-input" autocomplete="off" data-valid="">
										<label class="custom-file-label" for="logo">Choose file</label>
										<!--<span class="file_note" style="line-height: 30px;">Please Image Size should be 600/400 ( Video-max size - 8mb ).</span>-->
									</div>
									@if ($errors->has('image'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('image') }}</strong>
										</span> 
									@endif
								</div>
							</div>
							<div class="form-group row"> 
								<label for="short_description" class="col-sm-2 col-form-label">Short Description </label>
								<div class="col-sm-10">
								{{ Form::text('short_description', '', array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Short Description' )) }}
								@if ($errors->has('short_description'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('short_description') }}</strong>
									</span> 
								@endif
								</div>
							</div>
							<div class="form-group row">
								<label for="description" class="col-sm-2 col-form-label">Description</label>
								<div class="col-sm-10">
									<!--<textarea name="description" data-valid="" value="" class="textarea" placeholder="Please Add Description Here" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>-->
									<textarea class="form-control"  id="description" placeholder="Please Add Description Here" rows="5" name="description"></textarea>

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
                                <label for="meta_keyword" class="col-sm-2 col-form-label">Meta Keyword</label>
                                <div class="col-sm-10">
                                    {{ Form::text('meta_keyword', @$fetchedData->meta_keyword, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter Meta Keyword' )) }}
                                    @if ($errors->has('meta_keyword'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('meta_keyword') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="youtube_url" class="col-sm-2 col-form-label">Youtube Video Url</label>
                                <div class="col-sm-10">
                                    {{ Form::text('youtube_url', @$fetchedData->youtube_url, array('class' => 'form-control', 'data-valid'=>'', 'autocomplete'=>'off','placeholder'=>'Enter youtube video url' )) }}
                                    @if ($errors->has('youtube_url'))
                                        <span class="custom-error" role="alert">
                                            <strong>{{ @$errors->first('youtube_url') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            
                            <div class="form-group row"> 
								<label for="pdf_doc" class="col-sm-2 col-form-label">PDF/Video</label>
								<div class="col-sm-10">
									<div class="custom-file">
										<input type="file" id="pdf_doc" name="pdf_doc" class="custom-file-input" autocomplete="off" data-valid="">
										<label class="custom-file-label" for="logo">Choose file</label>
										<span class="file_note" style="line-height: 30px;">Please Upload PDF/Video</span>
									</div>
									@if ($errors->has('pdf_doc'))
										<span class="custom-error" role="alert">
											<strong>{{ @$errors->first('pdf_doc') }}</strong>
										</span> 
									@endif
								</div>
							</div>
                            
							<div class="form-group row">
								<label for="status" class="col-sm-2 col-form-label">Is Active</label>
								<div class="col-sm-10">
									<input value="1" type="checkbox" name="status" data-bootstrap-switch>
								</div>
							</div> 
							<div class="form-group float-right">
								{{ Form::button('<i class="fa fa-save"></i> Save Blog', ['class'=>'btn btn-primary', 'onClick'=>'customValidate("add-blog")' ]) }}
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
<script src="{{ asset('public/assets/ckfinder/ckfinder.js') }}" type="text/javascript"></script>
<script>
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
	  $('.if_image').hide();
    }
  };
  
  

    /*var sharedCKEditorToolbarConfig = {
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
    CKEDITOR.replace('description',sharedCKEditorToolbarConfig);*/
    var description = CKEDITOR.replace('description'); //sharedCKEditorToolbarConfig
    CKFinder.setupCKEditor(description);
</script>
@endsection