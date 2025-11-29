@extends('layouts.admin')
@section('title', 'Update Block Slot')

@section('content')
<!-- Main Content -->
<style>
.date {max-width: 330px;font-size: 14px;line-height: 21px;margin: 0px auto;background: #d3d4ec;padding: 8px;border-radius: 5px;}
.h4Cls {background-color: #6777ef;color: #fff;font-size: 14px;font-weight: 700;padding: 10px 10px 10px 10px;}
.add-more { float:right;}
.timeSlotCls { padding-top: 30px;}
</style>

<?php
$start_time = date('g:i A',strtotime($fetchedData->start_time));
$end_time =  date('g:i A',strtotime($fetchedData->end_time));
$startTime = new DateTime($start_time);
$endTime = new DateTime($end_time);
$interval = new DateInterval('PT15M');
$period = new DatePeriod($startTime, $interval, $endTime);


$startHour = date('H',strtotime($fetchedData->start_time));
$endHour = date('H',strtotime($fetchedData->end_time));

$startMinutes = date('i',strtotime($fetchedData->start_time));
$endMinutes = date('i',strtotime($fetchedData->end_time));

//dd(count($disableSlotArr));

if( isset($disableSlotArr) && !empty($disableSlotArr) && count($disableSlotArr) >0 )
{
    $disabledatesLatestArr = array();
    foreach($disableSlotArr as $disKey=>$disVal) {
        $disabledatesLatestArr[] = date("d/m/Y",strtotime($disVal->disabledates));
    }
} else {
    $disabledatesLatestArr = array();
}
?>

<div class="main-content">
	<section class="section">
		<div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update Block Slot</h4>
                            <div class="card-header-action">
                                <a href="{{route('admin.feature.appointmentdisabledate.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="col-3 col-md-3 col-lg-3">
			        	{{--@include('../Elements/Admin/setting')--}}
		        </div>-->

                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="h4Cls">
                                <?php
                                if(isset($fetchedData->person_id)){
                                    //$title = "<b>Service Title - ".$fetchedData->title."</b>";
                                    if($fetchedData->person_id == 1){
                                        $title = "Ajay";
                                    } else if($fetchedData->person_id == 2){
                                        $title = "Shubam";
                                    } else if($fetchedData->person_id == 3){
                                        $title = "Tourist";
                                    } else if($fetchedData->person_id == 4){
                                        $title = "Education";
                                    }
                                    $title .=  "<br/>Daily Timings - ".date('g:i A',strtotime($fetchedData->start_time))." - ".date('g:i A',strtotime($fetchedData->end_time));
                                    $title .=  "<br/>Weekend - ".$fetchedData->weekend;
                                    echo  $title;
                                }?>
                            </h4>

                            <button type="button" id="add-more" class="btn btn-primary add-more">Add Slot</button>

                            {{ Form::open(array('url' => 'admin/appointment-dates-disable/edit', 'id'=>'myForm','name'=>"edit-partnertype", 'autocomplete'=>'off', "enctype"=>"multipart/form-data")) }}
                            {{ Form::hidden('id', @$fetchedData->id) }}
                            <div id="time-slots-container">
                                <?php
                                //dd($disableSlotArr);
                                if(isset($disableSlotArr) && count($disableSlotArr) >0 ) {
                                    foreach ($disableSlotArr as $diskey => $disval) {
                                ?>

                                <div class="row" style="margin-top:70px;">
                                    <div class="col-3 col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="time-slot-1" class="timeSlotCls">Slot <?php echo $diskey+1;?>:</label>
                                        </div>
                                    </div>

                                    <div class="col-3 col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <input type="text" class="form-control date" id="date_<?php echo $diskey;?>" name="disabledates[<?php echo $diskey;?>][]" value="<?php echo $disval['disabledates'];?>"/>
                                            @if ($errors->has('disabledates'))
                                                <span class="custom-error" role="alert">
                                                    <strong>{{ @$errors->first('disabledates') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-3 col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <select id="select-box-container_<?php echo $diskey;?>" name="slots[<?php echo $diskey;?>][]" multiple>
                                                <?php
                                                if( !empty($disval['slots']) ) {
                                                    if ( str_contains($disval['slots'], ',') ) {
                                                        $slotsArr = explode(",",$disval['slots']);
                                                    } else {
                                                        $slotsArr = array($disval['slots']);
                                                    } //dd($slotsArr)
                                                    ?>
                                                    <option value="">Select Slot</option>
                                                    <?php
                                                    foreach ($period as $time) {
                                                        $timeFormat = $time->format('g:i A');
                                                        $selected = in_array($timeFormat, $slotsArr) ? "selected" : "";
                                                    ?>
                                                        <option <?php echo $selected;?> value="<?php echo $time->format('g:i A'); ?>"><?php echo $time->format('g:i A'); ?></option>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                    $slotsArr = array();
                                                    foreach ($period as $time) {
                                                        $timeFormat = $time->format('g:i A');
                                                    ?>
                                                        <option @if($disval['slots'] == $timeFormat ) selected @endif value="<?php echo $time->format('g:i A'); ?>"><?php echo $time->format('g:i A'); ?></option>
                                                    <?php
                                                    }
                                                } ?>
                                            </select>

                                            @if ($errors->has('slots'))
                                                <span class="custom-error" role="alert">
                                                    <strong>{{ @$errors->first('slots') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-3 col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <input type="checkbox" class="select_all" id="select_all_<?php echo $diskey;?>"  value="" <?php if (isset($disval['block_all']) && $disval['block_all'] == 1) echo 'checked'; ?> > Block Full Day
				                            <input type="hidden" id="block_all_<?php echo $diskey;?>" name="block_all[<?php echo $diskey;?>][]" value="<?php echo $disval['block_all'];?>">
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                else
                                { //If no slot exist?>
                                    <div class="row" style="margin-top:70px;">
                                        <div class="col-3 col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="time-slot-1" class="timeSlotCls">Slot 1:</label>
                                            </div>
                                        </div>

                                        <div class="col-3 col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for=""></label>
                                                <input type="text" class="form-control date" name="disabledates[0][]" data-valid="required"/>
                                                @if ($errors->has('disabledates'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('disabledates') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-3 col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <select id="select-box-container_1" name="slots[0][]" multiple>
                                                    <option value="">Select Slot</option>
                                                    <?php foreach ($period as $time) { ?>
                                                    <option value="<?php echo $time->format('g:i A'); ?>"><?php echo $time->format('g:i A'); ?></option>
                                                    <?php } ?>
                                                </select>

                                                @if ($errors->has('slots'))
                                                    <span class="custom-error" role="alert">
                                                        <strong>{{ @$errors->first('slots') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-3 col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <input type="checkbox" class="select_all" id="select_all_1"  value=""> Block Full Day
                                                <input type="hidden" id="block_all_1" name="block_all[0][]" value="0">
                                            </div>
                                        </div>

                                    </div>
                                <?php
                                }?>
                            </div>

                            <div class="form-group float-right">
                                {{ Form::submit('Save', ['class'=>'btn btn-primary' ]) }}
                            </div>

                            {{ Form::close() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
	</section>
</div>

@endsection
@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script>
jQuery(document).ready(function($){
    // Handle Select All functionality
    $(document).delegate('.select_all', 'change', function(){
        var select_all_id =  $(this).attr('id');
        var select_all_id_arr = select_all_id.split('_');
        if ( $('#'+select_all_id).prop('checked') ) {
            $('#select-box-container_' + select_all_id_arr[2] + ' option').filter(function() {
                return $(this).val() !== "";  // Only select options where the value is not blank
            }).prop('selected', true);
            $('#block_all_'+select_all_id_arr[2]).val(1);
        } else {
            $('#select-box-container_'+select_all_id_arr[2]+' option').prop('selected', false);
            $('#block_all_'+select_all_id_arr[2]).val(0);
        }
    });
    //1st add more
    $(document).delegate('#select-box-container_1', 'change', function(){
        var selectedValue = $(this).val();
        if(selectedValue != ""){
            $('#select_all_1').prop('checked', false);
            $('#block_all_1').val(0);
        }
    });
    //after 1st add more
    $(document).delegate('[id^=select-box-container_] select', 'change', function(){
        var parentDivId = $(this).closest('div').attr('id');
        var selectedValue = $(this).val();
        console.log('Selected value:', selectedValue, 'from', parentDivId);
        var parentDivIdArr = parentDivId.split('_');
        if(selectedValue != ""){
            $('#select_all_'+parentDivIdArr[1]).prop('checked', false);
            $('#block_all_'+parentDivIdArr[1]).val(0);
        }
    });

    //at edit page
    $(document).delegate('[id^=select-box-container_]', 'change', function(){
        var parentDivId = $(this).attr('id');
        var selectedValue = $(this).val();
        //console.log('Selected value:', selectedValue);
        var parentDivIdArr = parentDivId.split('_');
        if(selectedValue != ""){
            $('#select_all_'+parentDivIdArr[1]).prop('checked', false);
            $('#block_all_'+parentDivIdArr[1]).val(0);
        }
    });



    var startHour = '<?php echo $startHour;?>';
    var endHour = '<?php echo $endHour;?>';

    var startMinutes = '<?php echo $startMinutes;?>';
    if(startMinutes == '00'){
        startMinutes = 0;
    }
    var endMinutes = '<?php echo $endMinutes;?>';
    if(endMinutes == '00'){
        endMinutes = 0;
    }
    //console.log(startHour+'=='+endHour+'=='+startMinutes+'=='+endMinutes);
    // Function to format time in 12-hour AM/PM format
    function formatTime(hours, minutes) {
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // The hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        return hours + ':' + minutes + ' ' + ampm;
    }

    //When start and end minute both r zero
    function generateTimeSlots1(startHour, endHour, intervalMinutes,slotCount1) {
        const $selectBox = $('<select name="slots['+slotCount1+'][]" multiple></select>');
        let currentHour = startHour;
        let currentMinutes = 0;

        while (currentHour < endHour || (currentHour === endHour && currentMinutes < 60 )) {
            if (currentHour === endHour && currentMinutes === 0) break;
            const timeSlot = formatTime(currentHour, currentMinutes);
            const $option = $('<option></option>').val(timeSlot).text(timeSlot);
            $selectBox.append($option);

            // Increment the time by the interval
            currentMinutes += intervalMinutes;
            if (currentMinutes >= 60) {
                currentMinutes = 0;
                currentHour++;
            }
        }
        return $selectBox;
    }

    //When end minute r not zero
    function generateTimeSlots2(startHour, endHour, endMinutes, intervalMinutes,slotCount1) {
        const $selectBox = $('<select name="slots['+slotCount1+'][]" multiple></select>');
        let currentHour = startHour;
        let currentMinutes = 0;

        while (currentHour < endHour || (currentHour === endHour && currentMinutes <= endMinutes)) {
            if (currentHour === endHour && currentMinutes === 0) break;
            const timeSlot = formatTime(currentHour, currentMinutes);
            const $option = $('<option></option>').val(timeSlot).text(timeSlot);
            $selectBox.append($option);

            // Increment the time by the interval
            currentMinutes += intervalMinutes;
            if (currentMinutes >= 60) {
                currentMinutes = 0;
                currentHour++;
            }
        }

        return $selectBox;
    }


    //When start and end minute both r not zero
    function generateTimeSlots3(startHour, startMinute, endHour, endMinute, intervalMinutes,slotCount1) {
        const $selectBox = $('<select name="slots['+slotCount1+'][]" multiple></select>');
        let currentHour = startHour;
        let currentMinute = startMinute;

        while (currentHour < endHour || (currentHour === endHour && currentMinute <= endMinute)) {
            if (currentHour === endHour && currentMinutes === 0) break;
            const timeSlot = formatTime(currentHour, currentMinute);
            const $option = $('<option></option>').val(timeSlot).text(timeSlot);
            $selectBox.append($option);

            // Increment the time by the interval
            currentMinute += intervalMinutes;
            if (currentMinute >= 60) {
                currentMinute = 0;
                currentHour++;
            }
        }
        return $selectBox;
    }


    var totalRowsCnt = '<?php echo count($disableSlotArr);?>';
    if(totalRowsCnt >0) {
        var slotCount = totalRowsCnt;
    } else {
        var slotCount = 1;
    }
    $('#add-more').click(function() {
        var slotCount1 = slotCount;
        slotCount++;

        console.log(startHour+'=='+endHour+'=='+startMinutes+'=='+endMinutes);
        if( startMinutes == 0 && endMinutes == 0 ) {
            var $newSelectBox = generateTimeSlots1(startHour, endHour,15,slotCount1);
        } else if( startMinutes == 0 && endMinutes != 0 ) {
            var $newSelectBox = generateTimeSlots2(startHour, endHour, endMinutes,15,slotCount1);
        } else if( startMinutes != 0 && endMinutes != 0 ) {
            var $newSelectBox = generateTimeSlots3(startHour, startMinutes, endHour,endMinutes,15,slotCount1);
        }
        //const newSlot = '<div class="row"><div class="col-3 col-md-3 col-lg-3"><div class="form-group"><label for="time-slot-1" class="timeSlotCls">Slot '+slotCount+':</label></div></div><div class="col-3 col-md-3 col-lg-3"><div class="form-group"><label for=""></label></div></div><div class="col-6 col-md-6 col-lg-6"><div class="form-group"><label for=""></label><input type="text" class="form-control date" style="width: 47%;margin-left: -258px;" name="disabledates['+slotCount1+'][]"><div id="select-box-container_'+slotCount+'" style="margin-top: -60px;"></div><input type="checkbox" class="select_all" id="select_all_'+slotCount+'" name="select_all" value="1"> Block Full Day</div></div></div>';
        const newSlot = '<div class="row"><div class="col-3 col-md-3 col-lg-3"><div class="form-group"><label for="time-slot-1" class="timeSlotCls">Slot '+slotCount+':</label></div></div><div class="col-3 col-md-3 col-lg-3"><div class="form-group"><label for=""></label></div></div><div class="col-3 col-md-3 col-lg-3"><div class="form-group"><label for=""></label><input type="text" class="form-control date" style="margin-left: -258px;" name="disabledates['+slotCount1+'][]"><div id="select-box-container_'+slotCount+'" style="margin-top: -60px;"></div></div></div><div class="col-3 col-md-3 col-lg-3"><div class="form-group"><input type="checkbox" class="select_all" id="select_all_'+slotCount+'" value=""> Block Full Day  <input type="hidden" id="block_all_'+slotCount+'" name="block_all['+slotCount1+'][]" value="0"></div></div></div>';
        $('#time-slots-container').append(newSlot);
        $('#select-box-container_'+slotCount).html($newSelectBox);
        initializeDatepicker('.date');
    });

    // Function to initialize datepicker
    function initializeDatepicker(selector) {
        $(selector).datepicker({
            inline: true,
            startDate: new Date(),
            daysOfWeekDisabled: daysOfWeek,
            format: 'dd/mm/yyyy',
            autoclose: true
        });
    }

    var daysOfWeek = <?php echo json_encode($weekendd);?>;
    //var disabledatesF = <?php echo json_encode($disabledatesF); ?>;
    $('.date').datepicker({
        inline: true,
        startDate: new Date(),
        daysOfWeekDisabled: daysOfWeek,
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    var totalRowsCnt = '<?php echo count($disableSlotArr);?>';
    if(totalRowsCnt >0) {
        var disabledatesLatestArr = <?php echo json_encode($disabledatesLatestArr);?>; //console.log(disabledatesLatestArr);
        if(disabledatesLatestArr.length >0) {
            $('.date').each(function(index) {
                if (index < disabledatesLatestArr.length) {
                    $(this).datepicker('setDate', disabledatesLatestArr[index]);
                }
            });
        }
    }
    else
    {
        $('.date').datepicker({
            inline: true,
            startDate: new Date(),
            daysOfWeekDisabled: daysOfWeek,
            format: 'dd/mm/yyyy',
            autoclose: true
        });
        var disabledatesF = '';
        $('.date').datepicker('setDate', disabledatesF)
    }

    // Initialize datepicker on existing datepicker inputs (if any)
    initializeDatepicker('.date');
});

</script>
@endsection

