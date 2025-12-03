<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="keyword" content="Bansal CRM">
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> 
	<meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; script-src-attr 'unsafe-inline' 'unsafe-hashes'; style-src 'self' 'unsafe-inline' https:;">
	<title>Bansal CRM | <?php echo $__env->yieldContent('title'); ?></title>
	<link rel="icon" type="image/png" href="<?php echo e(asset('img/favicon.png')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/app.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/iziToast.min.css')); ?>">
	 <link rel="stylesheet" href="<?php echo e(asset('css/fullcalendar.min.css')); ?>">
	<!-- TinyMCE - No CSS needed -->
	<link rel="stylesheet" href="<?php echo e(asset('css/daterangepicker.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-timepicker.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/select2.min.css')); ?>">
	<!-- Template CSS -->
	<!--<link rel="stylesheet" href="">-->
	<!--<link rel="stylesheet" href="">-->
  
	<link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-formhelpers.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('css/intlTelInput.css')); ?>">
  
	<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
  
	<link rel="stylesheet" href="<?php echo e(asset('css/components.css')); ?>">
	<!-- Custom style CSS -->
	<link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
	<!-- Modern Search CSS -->
	<link rel="stylesheet" href="<?php echo e(asset('css/modern-search.css')); ?>">
	
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">-->
    
    <link rel="stylesheet" href="<?php echo e(asset('css/dataTables_min_latest.css')); ?>">
    

<!-- <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>-->
<script src="<?php echo e(asset('js/jquery_min_latest.js')); ?>"></script>   

<style>
.dropbtn {
  background-color: transparent;
 border:0;
}
.ui.yellow.label, .ui.yellow.labels .label, .select2resultrepositorystatistics .yellow {background-color: #fbbd08!important;border-color: #fbbd08!important;color: #fff!important;}
.dropbtn:hover, .dropbtn:focus {
  background-color: transparent;
   border:0;
}

.mydropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 160px;
  overflow: auto;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.mydropdown a:hover {background-color: #ddd;}

.show {display: block;}
</style>
</head>
<body >
	<div class="loader"></div>
		<div class="popuploader" style="display: none;"></div>
	<div id="app">
		<div class="main-wrapper main-wrapper-1">
			<div class="navbar-bg"></div>
			<!--Header-->
			<?php echo $__env->make('../Elements/Admin/header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
			<!--Left Side Bar-->
			<?php echo $__env->make('../Elements/Admin/left-side-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

			<?php echo $__env->yieldContent('content'); ?>
				
			<?php echo $__env->make('../Elements/Admin/footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
		</div>
	</div>

   

		<?php if(@Settings::sitedata('date_format') != 'none'){
			   $date_format = @Settings::sitedata('date_format');
			 //  $time_format = @Settings::sitedata('time_format');
			 if($date_format == 'd/m/Y'){
			     $dataformat =  'DD/MM/YYYY';
			 }else if($date_format == 'm/d/Y'){
			     $dataformat =  'MM/DD/YYYY';
			 }else if($date_format == 'Y-m-d'){
		    	 $dataformat = 'YYYY-MM-DD';
			 }else{
			    $dataformat = 'YYYY-MM-DD';
			 }
			}else{
			  $dataformat = 'YYYY-MM-DD';
			}
			?>
				<script>
				    var site_url = '<?php echo e(URL::to('/')); ?>';
				     var dataformat = '<?php echo e($dataformat); ?>';
				    </script>	 
	<!--<script src=""></script> -->  
	<script src="<?php echo e(asset('js/app.min.js')); ?>"></script>   
	<script src="<?php echo e(asset('js/fullcalendar.min.js')); ?>"></script>
  
	<!--<script src=""></script>-->
  
	<script src="<?php echo e(asset('js/datatables.min.js')); ?>"></script>   
	<script src="<?php echo e(asset('js/dataTables.bootstrap4.js')); ?>"></script>   
	 <!-- JS Libraies -->
	<!--<script src=""></script>--> 
	<!-- Page Specific JS File -->	
	<!--<script src="<?php echo e(asset('js/index.js')); ?>"></script> -->  
	<script src="<?php echo e(asset('assets/tinymce/js/tinymce/tinymce.min.js')); ?>"></script>
	<script src="<?php echo e(asset('js/tinymce-init.js')); ?>"></script>
	<script src="<?php echo e(asset('js/tinymce-summernote-compat.js')); ?>"></script> 
	<script src="<?php echo e(asset('js/daterangepicker.js')); ?>"></script> 
	<script src="<?php echo e(asset('js/bootstrap-timepicker.min.js')); ?>"></script> 
	
	<script src="<?php echo e(asset('js/select2.full.min.js')); ?>"></script> 
	<!--<script src=""></script>--> 
	<script src="<?php echo e(asset('js/bootstrap-formhelpers.min.js')); ?>"></script> 
	<script src="<?php echo e(asset('js/intlTelInput.js')); ?>"></script> 
	<script src="<?php echo e(asset('js/custom-form-validation.js')); ?>"></script> 
	<script src="<?php echo e(asset('js/scripts.js')); ?>"></script> 
	<!-- Template JS File -->
	<script src="<?php echo e(asset('js/iziToast.min.js')); ?>"></script>   

	<!-- Custom JS File -->	
	<script src="<?php echo e(asset('js/custom.js')); ?>">
	</script>
	<!-- Modern Search JS -->
	<script src="<?php echo e(asset('js/modern-search.js')); ?>"></script> 
	<script>
		$(document).ready(function () {
		   
		    $(".tel_input").on("blur", function() {
                /*if (/^0/.test(this.value)) {
                   //this.value = this.value.replace(/^0/, "")
                } else {
                    //this.value =  "0" + this.value;
                }*/
                this.value =  this.value;
            });
            
		    $('.assineeselect2').select2({
		       dropdownParent: $('#checkinmodal'),
		    });
		 
			// Modern search is now initialized in modern-search.js
			// This provides better performance with debouncing, keyboard shortcuts, and categorization
			

$(document).delegate('.opencheckin', 'click', function(){
	
	$('#checkinmodal').modal('show');
});
	$(document).delegate('.visitpurpose', 'blur', function(){
		var visitpurpose = $(this).val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_visit_purpose',
			type:'POST',
			data:{id: appliid,visit_purpose:visitpurpose},
			success: function(responses){
				 $.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});
				
			}
		});
	});
	
	$(document).delegate('.savevisitcomment', 'click', function(){
		var visitcomment = $('.visit_comment').val();
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/update_visit_comment',
			type:'POST',
			data:{id: appliid,visit_comment:visitcomment},
			success: function(responses){
				// $('.popuploader').hide();
				$('.visit_comment').val('');
				$.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});
			}
		});
	});
	
	$(document).delegate('.attendsession', 'click', function(){
		var appliid = $(this).attr('data-id');
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/attend_session',
			type:'POST',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data:{id: appliid,waitcountdata: $('#waitcountdata').val()},
			success: function(response){
				
				 var obj = $.parseJSON(response);
				if(obj.status){
					$.ajax({
					url: site_url+'/admin/get-checkin-detail',
					type:'GET',
					data:{id: appliid},
					success: function(res){
						 $('.popuploader').hide();
						$('.showchecindetail').html(res);
					}
				});
					$('.checindata #id_'+appliid).remove();
				}else{
					alert(obj.message);
				}
			}
		});
	});
	
	$(document).delegate('.completesession', 'click', function(){
		var appliid = $(this).attr('data-id');
		var attendcountdata = $('#attendcountdata').val();
		console.log('Complete Session clicked - ID:', appliid, 'Attend Count:', attendcountdata);
		$('.popuploader').show();
		$.ajax({
			url: site_url+'/admin/complete_session',
			type:'POST',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data:{id: appliid, attendcountdata: attendcountdata},
			success: function(response){
				console.log('Complete Session Response:', response);
				try {
					var obj = $.parseJSON(response);
					if(obj.status){
						$.ajax({
							url: site_url+'/admin/get-checkin-detail',
							type:'GET',
							data:{id: appliid},
							success: function(res){
								$('.popuploader').hide();
								$('.showchecindetail').html(res);
								alert('Session completed successfully!');
							},
							error: function(xhr, status, error){
								$('.popuploader').hide();
								console.error('Error fetching checkin detail:', error);
								alert('Session completed but failed to refresh details.');
							}
						});
						$('.checindata #id_'+appliid).remove();
					}else{
						$('.popuploader').hide();
						alert(obj.message);
					}
				} catch(e) {
					$('.popuploader').hide();
					console.error('Error parsing response:', e);
					alert('Error processing response');
				}
			},
			error: function(xhr, status, error){
				$('.popuploader').hide();
				console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
				alert('Failed to complete session. Error: ' + error + '\nStatus: ' + xhr.status + '\nResponse: ' + xhr.responseText);
			}
		});
	});
	$(document).delegate('.opencheckindetail', 'click', function(){
		$('#checkindetailmodal').modal('show');
		$('.popuploader').show();
		var appliid = $(this).attr('id');
		$.ajax({
				url: site_url+'/admin/get-checkin-detail',
				type:'GET',
				data:{id: appliid},
				success: function(responses){
					 $('.popuploader').hide();
					$('.showchecindetail').html(responses);
				}
			});
	});
			/* $(".niceCountryInputSelector").each(function(i,e){
				new NiceCountryInput(e).init();
			}); */ 
			//$('.country_input').flagStrap(); 
			$(".telephone").intlTelInput(); 
			$('.drop_table_data button').on('click', function(){
				$('.client_dropdown_list').toggleClass('active');
			});
			$('.client_dropdown_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){ 
				if ($(this).is(":checked")) {
				$('.client_table_data table tr td').show();
				$('.client_table_data table tr th').show();
				$('.client_dropdown_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.client_dropdown_list label.dropdown-option input').prop('checked', false);
					$('.client_table_data table tr td').hide(); 
					$('.client_table_data table tr th').hide();
					$('.client_table_data table tr td:nth-child(1)').show();
					$('.client_table_data table tr th:nth-child(1)').show();
					$('.client_table_data table tr td:nth-child(2)').show();
					$('.client_table_data table tr th:nth-child(2)').show();
					$('.client_table_data table tr td:nth-child(17)').show();
					$('.client_table_data table tr th:nth-child(17)').show();
				}
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.client_table_data table tr td:nth-child('+val+')').show();
					$('.client_table_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.client_dropdown_list label.dropdown-option.all input').prop('checked', false);
						$('.client_table_data table tr td:nth-child('+val+')').hide();
						$('.client_table_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
          
          
             /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            //// active student list display show/hide start ///////
            /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            $('.student_drop_table_data button').on('click', function(){
				$('.student_dropdown_list').toggleClass('active');
			});

			$('.student_dropdown_list label.dropdown-option input').on('click', function(){
			    var val = $(this).val(); //console.log('val='+val);
			    if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.student_table_data table tr td').show();
                        $('.student_table_data table tr th').show();
                        $('.student_dropdown_list label.dropdown-option input').prop('checked', true);
                    } else {
                        $('.student_dropdown_list label.dropdown-option input').prop('checked', false);
                        $('.student_table_data table tr td').hide();
                        $('.student_table_data table tr th').hide();

                        $('.student_table_data table tr td:nth-child(1)').show();
                        $('.student_table_data table tr th:nth-child(1)').show();

                        $('.student_table_data table tr td:nth-child(2)').show();
                        $('.student_table_data table tr th:nth-child(2)').show();

                        $('.student_table_data table tr td:nth-child(22)').show();
                        $('.student_table_data table tr th:nth-child(22)').show();
                    }
                }
                else {
                    if ($(this).is(":checked")) {
                        $('.student_table_data table tr td:nth-child('+val+')').show();
                        $('.student_table_data table tr th:nth-child('+val+')').show();
                    } else{
						$('.student_dropdown_list label.dropdown-option.all input').prop('checked', false);
						$('.student_table_data table tr td:nth-child('+val+')').hide();
						$('.student_table_data table tr th:nth-child('+val+')').hide();
				    }
				}
			});
            /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            //// active student list display show/hide end ///////
            /////////////////////////////////////////////////
            /////////////////////////////////////////////////
          
          
           /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            //// inactive student list display show/hide start ///////
            /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            $('.student_drop_table_data1 button').on('click', function(){
				$('.student_dropdown_list1').toggleClass('active');
			});

			$('.student_dropdown_list1 label.dropdown-option input').on('click', function(){
			    var val = $(this).val(); //console.log('val='+val);
			    if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.student_table_data1 table tr td').show();
                        $('.student_table_data1 table tr th').show();
                        $('.student_dropdown_list1 label.dropdown-option input').prop('checked', true);
                    } else {
                        $('.student_dropdown_list1 label.dropdown-option input').prop('checked', false);
                        $('.student_table_data1 table tr td').hide();
                        $('.student_table_data1 table tr th').hide();

                        $('.student_table_data1 table tr td:nth-child(1)').show();
                        $('.student_table_data1 table tr th:nth-child(1)').show();

                        $('.student_table_data1 table tr td:nth-child(2)').show();
                        $('.student_table_data1 table tr th:nth-child(2)').show();

                        $('.student_table_data1 table tr td:nth-child(22)').show();
                        $('.student_table_data1 table tr th:nth-child(22)').show();
                    }
                }
                else {
                    if ($(this).is(":checked")) {
                        $('.student_table_data1 table tr td:nth-child('+val+')').show();
                        $('.student_table_data1 table tr th:nth-child('+val+')').show();
                    } else{
						$('.student_dropdown_list1 label.dropdown-option.all input').prop('checked', false);
						$('.student_table_data1 table tr td:nth-child('+val+')').hide();
						$('.student_table_data1 table tr th:nth-child('+val+')').hide();
				    }
				}
			});
            /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            //// inactive student list display show/hide end ///////
            /////////////////////////////////////////////////
            /////////////////////////////////////////////////
            
			
			$('.drop_table_data button').on('click', function(){
				$('.client_report_list').toggleClass('active');
			});
			$('.client_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.client_report_data table tr td').show();
				$('.client_report_data table tr th').show();
				$('.client_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.client_report_list label.dropdown-option input').prop('checked', false);
					$('.client_report_data table tr td').hide(); 
					$('.client_report_data table tr th').hide();
					$('.client_report_data table tr td:nth-child(1)').show();
					$('.client_report_data table tr th:nth-child(1)').show();
					$('.client_report_data table tr td:nth-child(2)').show();
					$('.client_report_data table tr th:nth-child(2)').show();
					$('.client_report_data table tr td:nth-child(11)').show();
					$('.client_report_data table tr th:nth-child(11)').show();
				}
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.client_report_data table tr td:nth-child('+val+')').show();
					$('.client_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.client_report_list label.dropdown-option.all input').prop('checked', false);
						$('.client_report_data table tr td:nth-child('+val+')').hide();
						$('.client_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
			
			$('.drop_table_data button').on('click', function(){
				$('.application_report_list').toggleClass('active');
			});
			$('.application_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.application_report_data table tr td').show();
				$('.application_report_data table tr th').show();
				$('.application_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.application_report_list label.dropdown-option input').prop('checked', false);
					$('.application_report_data table tr td').hide(); 
					$('.application_report_data table tr th').hide();
					$('.application_report_data table tr td:nth-child(1)').show();
					$('.application_report_data table tr th:nth-child(1)').show();
					$('.application_report_data table tr td:nth-child(2)').show();
					$('.application_report_data table tr th:nth-child(2)').show();
					$('.application_report_data table tr td:nth-child(3)').show();
					$('.application_report_data table tr th:nth-child(3)').show();
					$('.application_report_data table tr td:nth-child(5)').show();
					$('.application_report_data table tr th:nth-child(5)').show(); 
					$('.application_report_data table tr td:nth-child(7)').show();
					$('.application_report_data table tr th:nth-child(7)').show(); 
				}
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.application_report_data table tr td:nth-child('+val+')').show();
					$('.application_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.application_report_list label.dropdown-option.all input').prop('checked', false);
						$('.application_report_data table tr td:nth-child('+val+')').hide();
						$('.application_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
			
			$('.drop_table_data button').on('click', function(){
				$('.officevisit_report_list').toggleClass('active');
			});
			$('.officevisit_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) {
				$('.officevisit_report_data table tr td').show();
				$('.officevisit_report_data table tr th').show();
				$('.officevisit_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.officevisit_report_list label.dropdown-option input').prop('checked', false);
					$('.officevisit_report_data table tr td').hide(); 
					$('.officevisit_report_data table tr th').hide();
					$('.officevisit_report_data table tr td:nth-child(1)').show();
					$('.officevisit_report_data table tr th:nth-child(1)').show();
					$('.officevisit_report_data table tr td:nth-child(2)').show();
					$('.officevisit_report_data table tr th:nth-child(2)').show();
					$('.officevisit_report_data table tr td:nth-child(4)').show();
					$('.officevisit_report_data table tr th:nth-child(4)').show(); 
				}
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.officevisit_report_data table tr td:nth-child('+val+')').show();
					$('.officevisit_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.officevisit_report_list label.dropdown-option.all input').prop('checked', false);
						$('.officevisit_report_data table tr td:nth-child('+val+')').hide();
						$('.officevisit_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
			
			$('.drop_table_data button').on('click', function(){
				$('.invoice_report_list').toggleClass('active');
			});
			$('.invoice_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) { 
				$('.invoice_report_data table tr td').show();
				$('.invoice_report_data table tr th').show();
				$('.invoice_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.invoice_report_list label.dropdown-option input').prop('checked', false);
					$('.invoice_report_data table tr td').hide(); 
					$('.invoice_report_data table tr th').hide();
					$('.invoice_report_data table tr td:nth-child(1)').show();
					$('.invoice_report_data table tr th:nth-child(1)').show();
					$('.invoice_report_data table tr td:nth-child(2)').show();
					$('.invoice_report_data table tr th:nth-child(2)').show();
					$('.invoice_report_data table tr td:nth-child(4)').show();
					$('.invoice_report_data table tr th:nth-child(4)').show(); 
				} 
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.invoice_report_data table tr td:nth-child('+val+')').show();
					$('.invoice_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.invoice_report_list label.dropdown-option.all input').prop('checked', false);
						$('.invoice_report_data table tr td:nth-child('+val+')').hide();
						$('.invoice_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
			
			$('.drop_table_data button').on('click', function(){
				$('.saleforecast_applic_report_list').toggleClass('active');
			});
			$('.saleforecast_applic_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) { 
				$('.saleforecast_application_report_data table tr td').show();
				$('.saleforecast_application_report_data table tr th').show();
				$('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', false);
					$('.saleforecast_application_report_data table tr td').hide(); 
					$('.saleforecast_application_report_data table tr th').hide();
					$('.saleforecast_application_report_data table tr td:nth-child(1)').show();
					$('.saleforecast_application_report_data table tr th:nth-child(1)').show();
					$('.saleforecast_application_report_data table tr td:nth-child(2)').show();
					$('.saleforecast_application_report_data table tr th:nth-child(2)').show();
					$('.saleforecast_application_report_data table tr td:nth-child(4)').show();
					$('.saleforecast_application_report_data table tr th:nth-child(4)').show(); 
				} 
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.saleforecast_application_report_data table tr td:nth-child('+val+')').show();
					$('.saleforecast_application_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.saleforecast_applic_report_list label.dropdown-option.all input').prop('checked', false);
						$('.saleforecast_application_report_data table tr td:nth-child('+val+')').hide();
						$('.saleforecast_application_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
			
			$('.drop_table_data button').on('click', function(){
				$('.interest_service_report_list').toggleClass('active');
			});
			$('.interest_service_report_list label.dropdown-option input').on('click', function(){
			var val = $(this).val();
			if(val == 'all'){
				if ($(this).is(":checked")) { 
				$('.interest_service_report_data table tr td').show();
				$('.interest_service_report_data table tr th').show();
				$('.interest_service_report_list label.dropdown-option input').prop('checked', true);
				}else{
					$('.interest_service_report_list label.dropdown-option input').prop('checked', false);
					$('.interest_service_report_data table tr td').hide(); 
					$('.interest_service_report_data table tr th').hide();
					$('.interest_service_report_data table tr td:nth-child(1)').show();
					$('.interest_service_report_data table tr th:nth-child(1)').show();
					$('.interest_service_report_data table tr td:nth-child(2)').show();
					$('.interest_service_report_data table tr th:nth-child(2)').show();
					$('.interest_service_report_data table tr td:nth-child(10)').show();
					$('.interest_service_report_data table tr th:nth-child(10)').show(); 
					$('.interest_service_report_data table tr td:nth-child(14)').show();
					$('.interest_service_report_data table tr th:nth-child(14)').show(); 
				} 
				
			}else{
				
			 if ($(this).is(":checked")) {
					$('.interest_service_report_data table tr td:nth-child('+val+')').show();
					$('.interest_service_report_data table tr th:nth-child('+val+')').show();
				  }
				  else{
						$('.interest_service_report_list label.dropdown-option.all input').prop('checked', false);
						$('.interest_service_report_data table tr td:nth-child('+val+')').hide();
						$('.interest_service_report_data table tr th:nth-child('+val+')').hide();
				  }
				}
			});
			
			$('#personal_details .is_business').hide();
			$('#office_income_share .is_super_agent').hide();
			$('#office_income_share .is_sub_agent').hide();
					
			$('.modal-body form#addgroupinvoice .is_superagentinv').hide();
			
			$('#agentstructure input[name="struture"]').on('change', function(){
				var id = $(this).attr('id'); 
				if(id == 'individual'){
					$('#personal_details .is_business').hide();
					$('#personal_details .is_individual').show();
					$('#personal_details .is_business input').attr('data-valid', '');
					$('#personal_details .is_individual input').attr('data-valid', 'required');
				} 
				else{
					$('#personal_details .is_individual').hide();
					$('#personal_details .is_business').show(); 
					$('#personal_details .is_business input').attr('data-valid', 'required');
					$('#personal_details .is_individual input').attr('data-valid', '');
				}
			});
			$('.modal-body form#addgroupinvoice input[name="partner_type"]').on('change', function(){
				var invid = $(this).attr('id'); 
				if(invid == 'superagent_inv'){
					$('.modal-body form#addgroupinvoice .is_partnerinv').hide();
					$('.modal-body form#addgroupinvoice .is_superagentinv').show();
					$('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', '');
					$('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', 'required');
				} 
				else{
					$('.modal-body form#addgroupinvoice .is_superagentinv').hide();
					$('.modal-body form#addgroupinvoice .is_partnerinv').show(); 
					$('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', 'required');
					$('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', '');
				}
			});
			$('.modal .modal-body .is_partner').hide();
			$('.modal .modal-body .is_application').hide();
			$('.modal .modal-body input[name="related_to"]').on('change', function(){
				var relid = $(this).attr('id'); 
				if(relid == 'contact'){
					$('.modal .modal-body .is_partner').hide();
					$('.modal .modal-body .is_application').hide();
					$('.modal .modal-body .is_contact').show();
					$('.modal .modal-body .is_partner select').attr('data-valid', '');
					$('.modal .modal-body .is_application select').attr('data-valid', '');
					$('.modal .modal-body .is_contact select').attr('data-valid', 'required');
				} 
				else if(relid == 'partner'){
					$('.modal .modal-body .is_contact').hide();
					$('.modal .modal-body .is_application').hide();
					$('.modal .modal-body .is_partner').show();
					$('.modal .modal-body .is_contact select').attr('data-valid', '');
					$('.modal .modal-body .is_application select').attr('data-valid', '');
					$('.modal .modal-body .is_partner select').attr('data-valid', 'required');
				}
				else if(relid == 'application'){
					$('.modal .modal-body .is_contact').hide();
					$('.modal .modal-body .is_partner').hide();
					$('.modal .modal-body .is_application').show();
					$('.modal .modal-body .is_contact select').attr('data-valid', '');
					$('.modal .modal-body .is_partner select').attr('data-valid', '');
					$('.modal .modal-body .is_application select').attr('data-valid', 'required');
				}
				else{
					$('.modal .modal-body .is_contact').hide();
					$('.modal .modal-body .is_partner').hide(); 
					$('.modal .modal-body .is_application').hide(); 
					$('.modal .modal-body .is_contact input').attr('data-valid', '');
					$('.modal .modal-body .is_partner input').attr('data-valid', '');
					$('.modal .modal-body .is_application input').attr('data-valid', '');
				}
			});
			$('#agenttype input#super_agent').on('click', function(){
				if ($(this).is(":checked")) {
					$('#office_income_share .is_super_agent').show();
				} 
				else{
					$('#office_income_share .is_super_agent').hide();
				}
			});	
			$('#agenttype input#sub_agent').on('click', function(){
				if ($(this).is(":checked")) {
					$('#office_income_share .is_sub_agent').show();
				} 
				else{
					$('#office_income_share .is_sub_agent').hide();
				}
			});	 
			 
			$('#internal select[name="source"]').on('change', function(){				
				var sourceval = $(this).val(); 
				if(sourceval == 'Sub Agent'){
					$('#internal .is_subagent').show();
					$('#internal .is_subagent select').attr('data-valid', 'required');
				} 
				else{
					$('#internal .is_subagent').hide();
					$('#internal .is_subagent select').attr('data-valid', '');
				}
			});
			
			$('.card .card-body .grid_data').hide();
			$('.card .card-body .document_layout_type a.list').on('click', function(){
				$('.card .card-body .document_layout_type a').removeClass('active');
				$(this).addClass('active');
				$('.card .card-body .grid_data').hide();
				$('.card .card-body .list_data').show();
			});
			$('.card .card-body .document_layout_type a.grid').on('click', function(){
				$('.card .card-body .document_layout_type a').removeClass('active');
				$(this).addClass('active');
				$('.card .card-body .list_data').hide();
				$('.card .card-body .grid_data').show();
			});
			
		$('.js-data-example-ajax-check').on("select2:select", function(e) { 
  var data = e.params.data;
  console.log(data);
   $('#utype').val(data.status);
});	
			
			$('.js-data-example-ajax-check').select2({
		 multiple: true,
		 closeOnSelect: false,
		dropdownParent: $('#checkinmodal'),
		  ajax: {
			url: '<?php echo e(URL::to('/admin/clients/get-recipients')); ?>',
			dataType: 'json',
			processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			  
			},
			 cache: true
			
		  },
	templateResult: formatRepocheck,
	templateSelection: formatRepoSelectioncheck
});
function formatRepocheck (repo) {
  if (repo.loading) {
    return repo.text;
  }

  var $container = $(
    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

      "<div  class='ag-flex ag-align-start'>" +
        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +
      
      "</div>" +
      "</div>" +
	   "<div class='ag-flex ag-flex-column ag-align-end'>" +
        
        "<span class='select2resultrepositorystatistics'>" +
          
        "</span>" +
      "</div>" +
    "</div>"
  );

  $container.find(".select2-result-repository__title").text(repo.name);
  $container.find(".select2-result-repository__description").text(repo.email);
  if(repo.status == 'Archived'){
	  $container.find(".select2resultrepositorystatistics").append('<span class="ui label  select2-result-repository__statistics">'+repo.status+'</span>');
  }else{
	    $container.find(".select2resultrepositorystatistics").append('<span class="ui label yellow select2-result-repository__statistics">'+repo.status+'</span>');
  }
  
 
  return $container;
}

function formatRepoSelectioncheck (repo) {
  return repo.name || repo.text;
}

		/* $('.timepicker').timepicker({
			minuteStep: 1,
			showSeconds: true,
		}); */
	});	
	
$(document).ready(function(){
    
    document.getElementById('countbell_notification').parentNode.addEventListener('click', function(event){
       window.location = "/admin/all-notifications";
    })
    
    function load_unseen_notification(view = '')
    {
        $.ajax({
            url:"<?php echo e(URL::to('/admin/fetch-notification')); ?>",
            method:"GET",
            dataType:"json",
            success:function(data)
            {
                //$('.showallnotifications').html(data.notification);
                if(data.unseen_notification > 0)
                {
                    $('.countbell').html(data.unseen_notification);
                }
            }
        });
    }
    
    function load_unseen_messages(view = '')
    {
        load_unseen_notification();
		var playing = false;
        $.ajax({
            url:"<?php echo e(URL::to('/admin/fetch-messages')); ?>",
            method:"GET",
            success:function(data)
            {
                if(data != 0){
                    iziToast.show({
                        backgroundColor: 'rgba(0,0,255,0.3)',
                        messageColor: 'rgba(255,255,255)',
                        title: '',
                        message: data,
                        position: 'bottomRight'
                    });
                    $(this).toggleClass("down");
            
                    if (playing == false) {
                      document.getElementById('player').play();
                      playing = true;
                      $(this).text("stop sound");
                    
                    } else {
                        document.getElementById('player').pause();
                        playing = false;
                        $(this).text("restart sound");
                    }
                }
            }
        });
    }

     /*function load_InPersonWaitingCount(view = '') {
        $.ajax({
            url:"<?php echo e(URL::to('/admin/fetch-InPersonWaitingCount')); ?>",
            method:"GET",
            dataType:"json",
            success:function(data) {
                //$('.showallnotifications').html(data.notification);
                if(data.InPersonwaitingCount > 0){
                    $('.countInPersonWaitingAction').html(data.InPersonwaitingCount);
                }
            }
        });
    }*/
    
    /*function load_TotalActivityCount(view = '') {
        $.ajax({
            url:"<?php echo e(URL::to('/admin/fetch-TotalActivityCount')); ?>",
            method:"GET",
            dataType:"json",
            success:function(data) {
                if(data.assigneesCount > 0){
                    $('.countTotalActivityAction').html(data.assigneesCount);
                }
            }
        });
    } */

    
    setInterval(function(){
      //load_unseen_messages();
      
      
      
        //load_unseen_notification();
        //load_InPersonWaitingCount();
        //load_TotalActivityCount();
    },120000); //5000
  
});
	
	</script>
	
	<div id="checkinmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Create In Person Client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="checkinmodalsave" id="checkinmodalsave" action="<?php echo e(URL::to('/admin/checkin')); ?>" autocomplete="off" enctype="multipart/form-data">
				<?php echo csrf_field(); ?>
			
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Search Contact <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax-check" name="contact"></select>
								<?php if($errors->has('email_from')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('email_from')); ?></strong>
									</span> 
								<?php endif; ?>
							</div> 
						</div>
						<input type="hidden" id="utype" name="utype" value="">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Office <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control" name="office">
									<option value="">Select</option>
									<?php $__currentLoopData = \App\Models\Branch::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $of): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($of->id); ?>"><?php echo e($of->office_name); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Visit Purpose <span class="span_req">*</span></label>
								<textarea class="form-control" name="message"></textarea>
								<?php if($errors->has('message')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('message')); ?></strong>
									</span>  
								<?php endif; ?>
							</div>
						</div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Select In Person Assignee <span class="span_req">*</span></label>
								<?php
								$assignee = \App\Models\Admin::where('role','!=', '7')->get();
								?>
								<select class="form-control assineeselect2" name="assignee">
								<?php $__currentLoopData = $assignee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assigne): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($assigne->id); ?>"><?php echo e($assigne->first_name); ?> (<?php echo e($assigne->email); ?>)</option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
								<?php if($errors->has('message')): ?>
									<span class="custom-error" role="alert">
										<strong><?php echo e(@$errors->first('message')); ?></strong>
									</span>  
								<?php endif; ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('checkinmodalsave')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="checkindetailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">In Person Details</h5>
				<a style="margin-left:10px;" href="javascript:;"><i class="fa fa-trash"></i> Archive</a>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showchecindetail">
				
			</div>
		</div>
	</div>
</div>
<?php echo $__env->yieldContent('scripts'); ?>	
	<!--<script src=""></script>-->  
</body>
</html> <?php /**PATH C:\xampp\htdocs\bansalcrm\resources\views/layouts/admin.blade.php ENDPATH**/ ?>