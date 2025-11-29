@extends('layouts.frontend')
@section('content')
<?php $dest = json_decode($pagedata); ?>
<div class="single_package"> 
	<div class="inner_single_package">
		<div class="container-fluid">
			<div class="row"> 
				<div class="list_image">
					<img src="{{@$dest->data->image_base_path}}{{@$dest->data->pagedetail->image}}" class="img-fluid" alt=""/>
					<div class="opacity_banner"></div> 
				</div>
			</div>
			<div class="row"> 
				<div class="col-md-12"> 
					
					<h2>{{@$dest->data->pagedetail->title}}</h2>
				</div>
				<div class="col-md-12">
					<?php echo htmlspecialchars_decode(stripslashes(@$dest->data->pagedetail->content)); ?>
					</div>
			</div>
		</div>
	</div>
</div>	
@endsection