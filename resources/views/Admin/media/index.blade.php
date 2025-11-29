 <div class="files-wraps view-grid">
						
							
                
@foreach($lists as $list)
	<div class="file-item image/jpeg is-image">

		<div title="au-banner" class="inner">
			<div class="file-thumb">
				<img src="{{URL::to('/public/img/media_gallery')}}/{{@$list->images}}" id="s_{{@$list->id}}" vfileid="{{@$list->id}}" vfile_name="{{@$list->images}}" vcc="{{@$fieldname}}" class="w_150">
			</div> 
			<div class="file-name">{{@$list->images}}</div> 
			<span class="file-checked-status" style="display: none;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M186.301 339.893L96 249.461l-32 30.507L186.301 402 448 140.506 416 110z"></path></svg></span>
		</div>

	</div>	
	@endforeach
    </div>
 {{$lists->appends(request()->query())->links()}}