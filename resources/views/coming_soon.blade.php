<?php
dd('Coming soon...');
$url = "https://mail.zoho.com/api/accounts/$accountId/messages";

$param = [
    "fromAddress"=> "xxxxx@orientha.com.co",
    "toAddress"=> "xxxxxx@gmail.com",
    "subject"=> "Email - Always and Forever",
    "content"=> "Email can never be dead ..."
];

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Authorization:'.$AuthToken));
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($param));

$result2 = curl_exec($ch2);
$result2 = json_decode($result2, true);

curl_close($ch);
print_r($result2);
?>