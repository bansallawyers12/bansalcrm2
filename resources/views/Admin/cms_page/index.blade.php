@extends('layouts.admin')
@section('title', 'Pages')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
</style>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
                <div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>Pages</h4>
						</div>
                    </div> 
                </div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card"> 
						<div class="card-header">
                            <div class="col-md-6">   
                                <div class="card-title">
                                    <a href="{{route('admin.cms_pages.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Page</a> 
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="card-tools card_tools">
                                    <form action="{{route('admin.cms_pages.index')}}" method="get">
                                    
                                        <div class="input-group input-group-sm" >
                                            <input type="text" name="search_term" class="form-control float-right" value="<?php if(isset($_REQUEST['search_term']) && $_REQUEST['search_term'] !=""){echo $_REQUEST['search_term'];}?>" placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
						</div>  
						<div class="card-body table-responsive">
							<table id="hoteltable" class="table table-bordered table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>Id</th>
                                  <th>Image</th>
								  <th>Title</th>
								  <th>Slug</th>
								  <th class="no-sort">Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
							  
								@foreach (@$lists as $list)	
								<tr id="id_{{@$list->id}}"> 
								 <td>{{ ++$i }}</td>
                                  <td style="white-space: initial;"><img src="{{asset('img/cmspage')}}/{{$list->image}}" style="width: 50px;height: 50px;"/></td> 
								  <td style="white-space: initial;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '50', '...') }}</td> 
								  <td style="white-space: initial;">{{ @$list->slug }}</td> 
				
								  <td>
									<a href="{{URL::to('/admin/cms_pages/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"><i class="fa fa-edit"></i></a>
									<a href="javascript:;" onClick="deleteAction({{@$list->id}}, 'cms_pages')"> <i class="fa fa-trash"></i></a>

								  </td>
								</tr>	
							  @endforeach	
								
							  </tbody> 
							 
							</table>
							<div class="card-footer hide">
							{!! $lists->appends(\Request::except('page'))->render() !!}
							 </div>
						  </div>
					</div>	
				</div>
			</div>
		</div>
	</section>
</div>

@endsection