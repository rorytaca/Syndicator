<!--
==========================================================================================================
    Automate_processes.php
==========================================================================================================
    This is the script that is executed by a CRON JOB on the hosting server at a given interval.
    
    1. Get Data from Firebaseio DB in JSON form
    2. Check each data node for their status value
    3. if 'status' != 1, node has not been sydnicated yet in which case
    4. Call all 5 individual syndication procedures from 'php/scripts/syndication_processes.php' per uncheckd node
    
-->
<?php
    
    include('php/scripts/syndication_processes.php');   //Syndication functions in here
    $url = 'https://amber-inferno-7558.firebaseio.com/.json';   //DB root location
    
    //Function executes a put cURL, update STATUS of obj to TRUE
    function putcURLtoFireBase($item,$objname) {
        //if [status] is not true, launch processes
        if ($item['status'] != 1) {
            print_r($objname);
            
            print "<br />";
            
            $purl = 'https://amber-inferno-7558.firebaseio.com/' . $objname . '.json';
              //Automated Process Called here
            print "updating db json obj".PHP_EOL;
            $data = $item;
            $data['status'] = true;
            $data_json = json_encode($data);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $purl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            if(!$response) {
                print PHP_EOL.'FAIL '.curl_errno($ch) . '-' . curl_error($ch);
            } else {
                print PHP_EOL.'PASS: '.curl_errno($ch) . '-' . curl_error($ch);
            }
            curl_close($ch);
        }
    }

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

    
    //submit_timeout_newyork_email($json);
    if (is_array($json)) {
        foreach($json as  $key => $val) {
            //CALL ALL 5 SYNDICATION PROCEDURES HERE $val
            
            submit_dealcatchers_form($val)
            submit_timeout_newyork_email($val);
            submit_retailmenot_form($val);
             
            putcURLtoFireBase($val, $key);
        }    
    } else {
        //CALL ALL 5 SYNDICATION PROCEDURES HERE AS WEL FOR $json
        
        submit_dealcatchers_form($json)
        submit_timeout_newyork_email($json);
        submit_retailmenot_form($json);
        
        putcURLtoFireBase($json);
    }

?>