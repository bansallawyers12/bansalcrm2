<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use mysqli;
use Illuminate\Support\Facades\Log;

class WpAppointmentToCrm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WpAppointmentToCrm:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cron is used to capture all wp site appointments to CRM admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {  
      try {
        //Source WP connection
        $sourceHost = '69.90.160.170';
        $sourceUsername = 'aatap306_wp1';
        $sourcePassword = '59zxc8FmbY';
        $sourceDatabase = 'aatap306_wp1';


        //Connect to WP connection
        $sourceConn = new mysqli($sourceHost, $sourceUsername, $sourcePassword, $sourceDatabase);
        if ($sourceConn->connect_error) {
            die("Connection failed: " . $sourceConn->connect_error);
        } 
      
      
        //destination CRM connection
        $destHost = '127.0.0.1';
        $destUsername = 'bansalc_db';
        $destPassword = 'np9vTWsalQ3q';
        $destDatabase = 'bansalc_db2';
      
        //Connect to CRM connection
        $destConn = new mysqli($destHost, $destUsername, $destPassword, $destDatabase);
        if ($destConn->connect_error) {
            die("Destination Connection failed: " . $destConn->connect_error);
        } 
      
      
        //Check record from WP already exist or not          
        $sql_Exist = "SELECT wp_appointment_id  FROM appointments WHERE wp_appointment_id IS NOT NULL order by wp_appointment_id desc limit 1";
        $result_Exist = $destConn->query($sql_Exist);   
        //echo "num_rows_exist==".$result_Exist->num_rows;die('@@@');
        $row_Exist = $result_Exist->fetch_assoc();
        //echo "wp_appointment_id==".$row_Exist['wp_appointment_id'];die('@@@');
      
        if( $result_Exist->num_rows > 0 ){ //if is not null
          	//Record fetch from Wp      
            $sql = "SELECT ap.id,ap.service_id,ap.starts_at,ap.ends_at,ap.customer_id,ap.status,
                    ap.payment_id,ap.payment_method,ap.payment_status,ap.paid_amount,
                    ap.created_at,ap.busy_from,ap.busy_to,

                    cu.first_name,cu.last_name,cu.phone_number,cu.email
                    FROM 
                    wp_bkntc_appointments as ap
                    LEFT JOIN wp_bkntc_customers as cu ON ap.customer_id=cu.id  WHERE ap.id > '".$row_Exist['wp_appointment_id']."' AND ap.customer_id=6576"; 
        } else { //if it is null
          
        	//Record fetch from Wp      
            $sql = "SELECT ap.id,ap.service_id,ap.starts_at,ap.ends_at,ap.customer_id,ap.status,
                    ap.payment_id,ap.payment_method,ap.payment_status,ap.paid_amount,
                    ap.created_at,ap.busy_from,ap.busy_to,

                    cu.first_name,cu.last_name,cu.phone_number,cu.email
                    FROM 
                    wp_bkntc_appointments as ap
                    LEFT JOIN wp_bkntc_customers as cu ON ap.customer_id=cu.id  WHERE ap.customer_id=6576";
        } //end else
      
        //echo $sql;
		$result = $sourceConn->query($sql);
        //echo "num_rows==".$result->num_rows;die('@@@');
      
        if ($result->num_rows > 0) 
        {
          while($row = $result->fetch_assoc()) 
          {
            /*Final
                      Bansal immigration.com.au                 =>    bansalcrm
                      1 Migration Advice                        =>    1 nature_of_enquiry    1 book_service   ajay calendar
                      2 Migration Consultation                  =>    2 nature_of_enquiry    2 book_service   shubam calendar
                      3 Student visa/ Admission                 =>    5 nature_of_enquiry    3 book_service   Education calendar
                      4 Tourist visa                            =>    4 nature_of_enquiry    4 book_service   Tourist visa  calendar
                      5 UK/CANADA/ EUROPE TO AUSTRALIA          =>    8 nature_of_enquiry    6 book_service   ajay calendar
                      6 Course Change/ Admission/ Student Visa  =>    5 nature_of_enquiry    3 book_service   Education calendar*/

            if( isset($row["service_id"]) && $row["service_id"] !=""){
                if( $row["service_id"] == 1){ //WP = Migration Advice
                  $service_id = 1; //CRM  ajay calendar
                  $noe_id = 1; //CRM  ajay calendar
                } 
                else if($row["service_id"] == 2 ) { //WP = Migration Consultation
                  $service_id = 2; //CRM shubam calendar
                  $noe_id = 2; //CRM  shubam calendar
                } 
                else if($row["service_id"] == 3 ) { //WP = Student visa/ Admission
                  $service_id = 3; //CRM Education calendar
                  $noe_id = 5; //CRM  Education calendar
                } 
                else if($row["service_id"] == 4 ) { //WP = Tourist visa 
                  $service_id = 4; //CRM  Tourist visa  calendar
                  $noe_id = 4; //CRM   Tourist visa  calendar
                } 
                else if($row["service_id"] == 5 ) { //WP = UK/CANADA/ EUROPE TO AUSTRALIA 
                  $service_id = 6; //CRM  ajay calendar
                  $noe_id = 8; //CRM   ajay calendar
                } 
                else if($row["service_id"] == 6 ) { //WP = Course Change/ Admission/ Student Visa
                  $service_id = 3; //CRM  Education calendar
                  $noe_id = 5; //CRM   Education calendar
                }
            } 

            $full_name = $row["first_name"]." ".$row["last_name"];

            if( isset($row["status"]) && $row["status"] !=""){
              if( $row["status"] == 'approved'){
                $status = 1;
              } else if($row["status"] == 'pending' ) {
                $status = 0;
              }
            } else {
              $status = 0;
            }

            $timestamp_start = $row["starts_at"];
            $new_timestamp_start = strtotime('+1 hour', $timestamp_start);

            $timestamp_end = $row["ends_at"];
            $new_timestamp_end = strtotime('+1 hour', $timestamp_end);

            date_default_timezone_set('Australia/Melbourne');

            $appointment_date = date("Y-m-d",$new_timestamp_start);
            $appointment_time_24format = date("H:i:s", $new_timestamp_start);

            $timeslot_start =  date("h:i A", $new_timestamp_start);
            $timeslot_end =  date("h:i A", $new_timestamp_end);
            $timeslot_full = $timeslot_start."-".$timeslot_end;

            $created_at = $row["created_at"];
            $new_created_at = strtotime('+1 hour', $created_at);
            $created_at = date("Y-m-d H:i:s", $new_created_at);
            $updated_at = date("Y-m-d H:i:s", $new_created_at);
            
            
            //Check from admins table at CRM
            $sql_check11 = "SELECT id FROM admins where email= '".$row["email"]."' OR  phone= '".$row["phone_number"]."'  ";
            $result_check11 = $destConn->query($sql_check11);
            //echo "num_rows_check11==".$result_check11->num_rows;die('@@@');
            if ($result_check11->num_rows > 0) {
              $row_check11 = $result_check11->fetch_assoc();
              //echo "<pre>row_check11="; print_r($row_check11);die('@@@');
              if( !empty($row_check11) ){
                $client_id = $row_check11['id'];
              } else {
                $client_id = "";
              }
            } else {
              $client_id_ins = strtoupper($full_name).date('his');
              $parts = explode(" ", $full_name);
              $last_name = array_pop($parts);
              $first_name = implode(" ", $parts);

              $sql_ins1 = "INSERT INTO
                    admins ( client_id,role,last_name, first_name,email,phone,wp_customer_id,password )
                    VALUES ( '".$client_id_ins."', '7','".$last_name."','".$first_name."','".$row["email"]."','".$row["phone_number"]."' ,'".$row["customer_id"]."','')";
              if ($destConn->query($sql_ins1) === TRUE) {
                $client_id = $destConn->insert_id;
                echo "New records created successfully";
              } else {
                echo "Error: " . $sql_ins1 . "<br>" . $destConn->error;
              }
            }


            $sql_ins = "INSERT INTO appointments 
                    (
                        wp_appointment_id,
                        client_id,
                        service_id, 
                        noe_id,

                        full_name,
                        email,
                        phone,

                        date,
                        time,
                        timeslot_full,

                        status,
                        created_at,
                        updated_at,

                        payment_id,
                        payment_method,
                        payment_status,
                        paid_amount
                    ) 
                    VALUES 
                    (
                        '".$row["id"]."',
                        '".$client_id."',
                        '".$service_id."',
                        '".$noe_id."', 

                        '".$full_name."',  
                        '".$row["email"]."',
                        '".$row["phone_number"]."',

                        '".$appointment_date."',
                        '".$appointment_time_24format."', 
                        '".$timeslot_full."',

                        '".$status."',
                        '".$created_at."',
                        '".$updated_at."',

                        '".$row["payment_id"]."',
                        '".$row["payment_method"]."',
                        '".$row["payment_status"]."',
                        '".$row["paid_amount"]."'
                    )";

            if ($destConn->query($sql_ins) === TRUE) {
                echo "New records created successfully";
            } else {
              echo "Error: " . $sql_ins . "<br>" . $destConn->error;
            }
         } //end while
          
      } else {
        echo "0 results";
      } //end else
      
       $sourceConn->close(); //Close WP connections
       $destConn->close(); //Close CRM connections
        
        } catch (\Exception $e) {
            Log::channel('cron')->error($e->getMessage());
        }
    }
}
