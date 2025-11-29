$(function() {
	
	$('#pdfdoc').on( 'change', function() {
   myfile= $( this ).val();
   var ext = myfile.split('.').pop();
   if(ext=="pdf"){
      $('.show_custom_file_error').html('');
	    $('#savebtn').prop('disabled', false);
   } else{
       $('.show_custom_file_error').html('<div class="alert alert-danger" role="alert">Only PDF file is accepted.</div>');
	   $('#savebtn').prop('disabled', true);
   }
});
	
	$(document).delegate('.showimages','click', function(){
		var image_type = $(this).attr('data-type');
		$('#media_modal').modal('show');
		$('#mtype').val(image_type);
		$.ajax({
			url: media_index_url,
			type: 'get',
			success: function(response){
				$('.gallery_list_data ul').html(response);
			}
		});
	});
	
	$(document).delegate('.selectimage','click', function(e){
	
		var id = $(this).attr('dataid');
		console.log($(this).attr('removeid'));

		var image = $(this).attr('dataimage');	
		var mtype = $('#mtype').val();	
		$('.'+mtype).html('<input type="hidden" id="'+mtype+'" name="'+mtype+'" value="'+id+'"><img src="'+image+'" style="width:100px!important;"><a href="javascript:;" class="removepackimage" rdatatype="'+mtype+'" id="'+id+'"><i class="fa fa-times"></i></a>');
		
		$('#media_modal').modal('hide');
		
	});
	$(document).delegate('.removepackimage','click', function(){

		var id = $(this).attr('dataid');	
		var rdatatype = $(this).attr('rdatatype');	
		var image = $(this).attr('dataimage');	
		$('.'+rdatatype).html('');
	});
	$(document).delegate('.removeimage','click', function(e){
		$("#loader").show();
		var id = $(this).attr('id');
		console.log($(this).attr('removeid'));

		
		 $.ajax({
			url: media_remove_url,
			type: 'get',
			 dataType: 'json',
			data: {imageid:	id}, 
			success: function(response){
				$("#loader").hide();
				var obj = response;
				if(obj.success){
					$("#l_"+id).remove();
					//$('.show_custom_msg').html('<div class="alert alert-success" role="alert">Image removed successfully</div>');
				}else{
					//$('.show_custom_msg').html('<div class="alert alert-danger" role="alert">There is something problem. Please try again</div>');
				}
			}
		}); 
		
	});
    // preventing page from redirecting
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $("h1").text("Drag here");
    });

    $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });

    // Drag enter
    $('.upload-area').on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $("h1").text("Drop");
    });

    // Drag over
    $('.upload-area').on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $("h1").text("Drop");
    });

    // Drop
    $('.upload-area').on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();

        $("h1").text("Upload");

        var file = e.originalEvent.dataTransfer.files;
        var fd = new FormData();

        fd.append('file', file[0]);

        uploadData(fd);
    });

    // Open file selector on div click
	$(document).delegate('#uploadfile','click', function(){
        $("#file").click();
    });

    // file selected
	$(document).delegate('#file','change', function(){
console.log('ffff');
        var fd = new FormData();

        var files = $('#file')[0].files[0];

        fd.append('file',files);

        uploadData(fd);
    });
});

// Sending AJAX request and upload file
function uploadData(formdata){
$("#loader").show();
    $.ajax({
        url: media_url,
		headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
        type: 'post',
        data: formdata,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response){
			$("#loader").hide();
            //addThumbnail(response);
			var obj = response; 
			if(obj.success){
				$('.gallery_list_data ul').prepend('<li id="l_'+obj.id+'"><img width="100" height="100" class="selectimage" dataimage="'+media_image_url+'/'+obj.imagedata+'" d="s_'+obj.id+'" dataid="'+obj.id+'" src="'+media_image_url+'/'+obj.imagedata+'"><a href="javascript:;" class="removeimage" removeid="sho_'+obj.id+'" id="'+obj.id+'"><i class="fa fa-times"></i></a></li>');
				$('.nav-tabs a[href="#vert-tabs-media"]').tab('show');
				alert('Successfully uploaded');
			}else{
				alert('There is something problem. Please try again');
				//$('.show_custom_msg').html('<div class="alert alert-danger" role="alert">There is something problem. Please try again</div>');
			}
        }
    }); 
}

// Added thumbnail
function addThumbnail(data){
    $("#uploadfile h1").remove(); 
    var len = $("#uploadfile div.thumbnail").length;

    var num = Number(len);
    num = num + 1;

    var name = data.name;
    var size = convertSize(data.size);
    var src = data.src;

    // Creating an thumbnail
    $("#uploadfile").append('<div id="thumbnail_'+num+'" class="thumbnail"></div>');
    $("#thumbnail_"+num).append('<img src="'+src+'" width="100%" height="78%">');
    $("#thumbnail_"+num).append('<span class="size">'+size+'<span>');

}

// Bytes conversion
function convertSize(size) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (size == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(size) / Math.log(1024)));
    return Math.round(size / Math.pow(1024, i), 2) + ' ' + sizes[i];
}