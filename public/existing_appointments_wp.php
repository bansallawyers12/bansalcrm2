<?php
phpinfo();
die('@@@');
// destination database connection parameters
/*$destHost = '127.0.0.1';
$destUsername = 'bansalc_db';
$destPassword = 'np9vTWsalQ3q';
$destDatabase = 'bansalc_db2';*/


$destHost = '69.90.160.170';
$destUsername = 'aatap306_wp1';
$destPassword = '59zxc8FmbY';
$destDatabase = 'aatap306_wp1';

//95.217.167.50
// Connect to the destination database
$destConn = new mysqli($destHost, $destUsername, $destPassword, $destDatabase);
if ($destConn->connect_error) {
    die("Connection failed11: " . $destConn->connect_error);
} /*else {
  die('success11');
}*/

echo $sql = "SELECT ap.id,ap.service_id,ap.starts_at,ap.ends_at,ap.customer_id,ap.status,
        ap.payment_id,ap.payment_method,ap.payment_status,ap.paid_amount,
        ap.created_at,ap.busy_from,ap.busy_to,
        
        cu.first_name,cu.last_name,cu.phone_number,cu.email
        FROM 
        wp_bkntc_appointments as ap
        LEFT JOIN wp_bkntc_customers as cu ON ap.customer_id=cu.id"; //WHERE ap.customer_id=6576
$result = $destConn->query($sql);   
echo "num_rows==".$result->num_rows;die('@@@');




$destConn->close(); // Close connections
?>
