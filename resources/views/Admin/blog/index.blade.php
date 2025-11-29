@extends('layouts.admin')
@section('title', 'Blogs')

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
							<h4>Blogs</h4>
							<div class="card-header-action">	
								<!-- <div class="card-title"> -->
									<a href="{{route('admin.blog.create')}}" class="btn btn-primary"><i class="fa fa-plus"></i> New Blog</a> 
								<!-- </div> 							 -->
									<!-- <a href="{{URL::to('admin/quotations/template/create')}}"  class="btn btn-primary is_checked_clientn">Create Template</a> -->
							</div>
						</div>
						<div class="card-body">
							<div class="tab-content" id="quotationContent">
								<div class="card-tools card_tools">
									<!-- <div class="input-group input-group-sm" style="width: 150px;">
										<input type="text" name="table_search" class="form-control float-right" placeholder="Search">
										<div class="input-group-append">
											<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
										</div>
									</div>
								</div> -->
								<div class="tab-pane fade show active" id="active_quotation" role="tabpanel" aria-labelledby="active_quotation-tab">
									<div class="table-responsive common_table"> 
									<table id="hoteltable" class="table table-bordered table-hover text-nowrap">
							  <thead>
								<tr>
								  <th>Image</th>
								  <th>Title</th>
								  <th>Slug</th>
								  <th>Category</th>
								  <th>Status</th>
								  <th class="no-sort">Action</th>
								</tr> 
							  </thead>
							  <tbody class="tdata">	
								@foreach (@$lists as $list)	
								<!-- <tr id="id_{{@$list->id}}">  -->
								  <!-- <td>{{@$list->id}}</td>  -->
								 {{-- @if($list->image)--}}
								  <!--<td><img src="{{URL::to('/public/img/blog/')}}/{{@$list->image}}" alt="" style="width:80px;height:80px;border-radius: 50%;"></td>--> 
								  {{--@else--}}
								  <!--<td><img src="{{URL::to('/public/img/avatars/no_image.jpeg')}}" alt="" style="width:80px;height:80px;border-radius: 50%;"></td>--> 
								 {{-- @endif--}}
								  
								    <?php
                                    if(isset($list->image) && $list->image != ""){
                                        $extension = pathinfo($list->image, PATHINFO_EXTENSION); //echo $extension;
                                        if( strtolower($extension) == 'mp4' ){ ?>
                                            <td><img src="{{URL::to('/public/img/avatars/mp4-outline.png')}}" alt="" style="width:80px;height:80px;border-radius: 50%;"></td>
                                        <?php } else if(strtolower($extension) == 'pdf') { ?>
                                            <td><img src="{{URL::to('/public/img/avatars/pdf_icon.png')}}" alt="" style="width:80px;height:80px;border-radius: 50%;"></td>
                                        <?php } else { ?>
                                            <td><img src="{{URL::to('/public/img/blog/')}}/{{@$list->image}}" alt="" style="width:80px;height:80px;border-radius: 50%;"></td>
                                        <?php
                                        }
                                    } else {?>
                                        <td><img src="{{URL::to('/public/img/avatars/no_image.jpeg')}}" alt="" style="width:80px;height:80px;border-radius: 50%;"></td>
                                    <?php } ?>
								  
								  <td style="white-space: initial;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '50', '...') }}</td> 
								  <td style="white-space: initial;">{{ @$list->slug }}</td> 
								  <td style="white-space: initial;">{{ @$list->categorydetail->name }}</td> 
								  <td style="white-space: initial;"><input data-id="{{@$list->id}}"  data-status="{{@$list->status}}" data-col="status" data-table="blogs" class="change-status" value="1" type="checkbox" name="status" {{ (@$list->status == 1 ? 'checked' : '')}} data-bootstrap-switch></td> 
								  <td>
									<!-- <div class="nav-item dropdown action_dropdown"> -->
										<!-- <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a> -->
										<!-- <div class="dropdown-menu"> -->
											<a class="btn btn-info" href="{{URL::to('/admin/blog/edit/'.base64_encode(convert_uuencode(@$list->id)))}}"> Edit</a>
											<a class="btn btn-danger" href="javascript:;" onClick="deleteAction({{@$list->id}}, 'blogs')"> Delete</a>
										<!-- </div>
									</div>  -->
								  </td>
								</tr>	
							  @endforeach	
								
							  </tbody> 
							 
							</table>
								
								</div>
								<div class="card-footer">
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

@endsection