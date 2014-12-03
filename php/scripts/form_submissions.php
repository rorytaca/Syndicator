<?php
    //Handles form submissions to URL: http://www.dealcatcher.com/share_a_coupon
    function submit_dealcatchers_form($arr) {
        $formActionUrl = 'http://www.dealcatcher.com/do/deals/add_coupon';
        //set $post data from array
        //Dealcatcher.com uses strange naming convention for input 'name' attributes. i.e input name='merchant[name]'... replace '[]'s with "%5D", "%5B"
        //Based off of     curl "http://www.dealcatcher.com/do/deals/add_coupon?merchant"%"5Bname"%"5D=test&user_coupon"%"5Bcoupon_type"%"5D=1&url"%"5Bsize90"%"5D=&coupon_code"%"5Binfo"%"5D=&discount"%"5Binfo"%"5D=test&user_coupon"%"5Bexpiration_date(2i)"%"5D=12&user_coupon"%"5Bexpiration_date(3i)"%"5D=3&user_coupon"%"5Bexpiration_date(1i)"%"5D=2014" -H "Cookie: __cfduid=d4afa876e309b969746455ebaea15f45d1417565957; _ga=GA1.3.739219950.1417565956; _dealcatcher_session=8da9a6129b0206db900f17e72f936586; _gat=1" -H "Accept-Encoding: gzip,deflate,sdch" -H "Accept-Language: en-US,en;q=0.8" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36" -H "Accept: */*" -H "Referer: http://www.dealcatcher.com/share_a_coupon" -H "X-Requested-With: XMLHttpRequest" -H "Connection: keep-alive" --compressed

        $post_data['merchant"%"5Bname"%"5D'] = $arr['name'];
        $post_data['user_coupon"%"5Bcoupon_type"%"5D'] = 1; //1 = 'coupon' select on site form
        $post_data['url"%"5Bsize90"%"5D'] = ''; //Leave blank
        $post_data['discount"%"5Binfo"%"5D'] = $arr['description'];
        
        $exp_date = explode('-',$arr['expiration_date']);   //Break down expiration date string: MO-DA-YEAR to [0],[1],[2]
        
        $post_data['user_coupon"%"5Bexpiration_date(2i)"%"5D'] = $exp_date[0];  //set equal to expiration MONTH
        $post_data['user_coupon"%"5Bexpiration_date(3i)"%"5D'] = $exp_date[1];  //set equal to expiration DAY
        $post_data['user_coupon"%"5Bexpiration_date(1i)"%"5D'] = $exp_date[2];  //set equal to expiration YEAR
        
        //Stringify Post array data
        foreach ( $post_data as $key => $value) {
            $post_items[] = $key . '=' . $value;
        }
        $post_string = implode ('&', $post_items);
        
        //create cURL connection
        $curl_connection = curl_init($formActionUrl);
        //SET CURL OPTIONS
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
        
        //SET STRING TO POST
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
        
        //EXECUTE POST CURL
        $result = curl_exec($curl_connection);
  
        //DEBUG PRINT
        //print_r(curl_getinfo($curl_connection));                 THIS LINE CAN BE USED TO VIEW WHAT THE CURL IS RETURNING IN TEXT. DISABLED FOR PRODUCTION
        
        //PRINT THIS LINE TO ERROR LOG
        if (curl_errno($curl_connection) == 0) {
            //PRINT PASS TO LOG
            $to_log = 'PASS: ' .curl_errno($curl_connection) . '-' . curl_error($curl_connection) . ' ON - ' . $formActionUrl . 'POST: ' . $post_string . PHP_EOL;
        } else {
            //Print Error number and msg to log
            $to_log = 'FAIL: ' .curl_errno($curl_connection) . '-' . curl_error($curl_connection) . ' ON - ' . $formActionUrl. 'POST: ' . $post_string . PHP_EOL;
        }
        //ADD $to_log RESULT TO txt
        file_put_contents("logs/automated-syndication-logs.txt", $to_log, FILE_APPEND);
        
        //CLOSE CURL RESOURCE
        curl_close($curl_connection);
    }
?>