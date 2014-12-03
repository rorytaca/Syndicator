<?php
    //This is the roll-call script that will be called by CRON JOBS to initiate all syndication functions

    //cURL to fetch data set from firebase 
    $url = 'https://amber-inferno-7558.firebaseio.com/.json'
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url
    ));
    
    //Send and close request
    $resp = curl_exec($curl);
    curl_close($curl);
    
    //decode JSON data into array
    $json = json_decode($resp, TRUE);
    foreach($json as $item) {
       
        //if [status] is not true, launch processes
        if ($item['status'] != 1) {
            //Automated Processes Called here
            
            
            
            //CURL POST to update status to 1
            
        }
    }
?>