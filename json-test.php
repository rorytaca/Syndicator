
<?php
    //This is a script for testing to see the return value when you CURL the FIREBASE db
    
    $url = 'https://amber-inferno-7558.firebaseio.com/.json';
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
            print_r($item['name']);
            print "<br />";
            
            //Automated Process Called here
            
            
            
            
            
        }
    }
?>