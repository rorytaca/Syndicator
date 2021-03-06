<!--
==========================================================================================================
   syndication_processes.php
==========================================================================================================
    This is the lib of individual functions called to process any product syndication. Each different site has its own unique procedural function
    that will be called at a set time interval by 'automate_processes.php' by a CRON JOB
-->
<?php
    
    //Common Variables across procedures
    $contact_info = "John Doe, 123 Fake St, New York, NY 12345, Tel: 212-212-2112"; //Fake contact info
    $email_headers = "From: joe@example.com\r\nReply-To: joe@example.com";    //Fake emails


    //Handles form submissions to URL: http://www.dealcatcher.com/share_a_coupon
    function submit_dealcatchers_form($arr) {
        $formActionUrl = 'http://www.dealcatcher.com/do/deals/add_coupon';

        //Dealcatcher.com uses strange naming convention for input 'name' attributes. i.e input name='merchant[name]'... replace '[]'s with "%5D", "%5B"
        // ^ assumption Based off of curl "http://www.dealcatcher.com/do/deals/add_coupon?merchant"%"5Bname"%"5D=test&user_coupon"%"5Bcoupon_type"%"5D=1&url"%"5Bsize90"%"5D=&coupon_code"%"5Binfo"%"5D=&discount"%"5Binfo"%"5D=test&user_coupon"%"5Bexpiration_date(2i)"%"5D=12&user_coupon"%"5Bexpiration_date(3i)"%"5D=3&user_coupon"%"5Bexpiration_date(1i)"%"5D=2014" -H "Cookie: __cfduid=d4afa876e309b969746455ebaea15f45d1417565957; _ga=GA1.3.739219950.1417565956; _dealcatcher_session=8da9a6129b0206db900f17e72f936586; _gat=1" -H "Accept-Encoding: gzip,deflate,sdch" -H "Accept-Language: en-US,en;q=0.8" -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36" -H "Accept: */*" -H "Referer: http://www.dealcatcher.com/share_a_coupon" -H "X-Requested-With: XMLHttpRequest" -H "Connection: keep-alive" --compressed
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
  
        //DEBUG PRINT:   THIS LINE CAN BE USED TO VIEW WHAT THE CURL IS RETURNING IN TEXT. DISABLED FOR PRODUCTION
        //print_r(curl_getinfo($curl_connection));               
        
        //PRINT THIS LINE TO ERROR LOG
        $date = date_create();
        if (curl_errno($curl_connection) == 0) {
            //PRINT PASS TO LOG
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .' PASS: ' .curl_errno($curl_connection) . '-' . curl_error($curl_connection) . ' ON - ' . $formActionUrl . 'POST: ' . $post_string . PHP_EOL;
        } else {
            //Print Error number and msg to log
            $to_log = date_format($date, 'U = Y-m-d H:i:s') . ' FAIL: ' .curl_errno($curl_connection) . '-' . curl_error($curl_connection) . ' ON - ' . $formActionUrl. 'POST: ' . $post_string . PHP_EOL;
        }
        //ADD $to_log RESULT TO txt
        file_put_contents("../../logs/automated-syndication-logs.txt", $to_log, FILE_APPEND);
        
        //CLOSE CURL RESOURCE
        curl_close($curl_connection);
    }
    
    //Handles automated e-mailing submissions to URL: http://www.timeout.com/newyork/get-listed ... Emailing to 'thisweek.ny@timeout.com'
    //If platform is expanded to hold category tags for EVENTS/PRODUCTS, we can add some logic to send out emails to different locations within timeout.com.... i.e. nightlife.ny@timeout.com, shopping.ny@timeout.com
    function submit_timeout_newyork_email($arr) {
        //Variables for email
        //$to = 'thisweek.ny@timeout.com';                                     //FOR TESTING CHANGE TO - $to = "syndicatordemo@gmail.com";
        $to = "syndicatordemo@gmail.com";
        
        $subject = 'Event Listing: ' . $arr['name'];
        $headers = "From: joe@example.com\r\nReply-To: joe@example.com";    //Fake emails for header  

        //Variables for email body - filtered with existance check ternary op just incase they don't exist on db
        $name = $arr['name'];
        $address = (array_key_exists("address",$arr) ? $arr['address'] : 'N/A');
        $date = (array_key_exists("expiration_date",$arr) ? $arr['date'] : 'N/A');
        $price = (array_key_exists("price",$arr) ? $arr['price'] : 'N/A');
        $phone = (array_key_exists("phone",$arr) ? $arr['phone'] : 'N/A');
        $website = (array_key_exists("website",$arr) ? $arr['website'] : 'N/A');
        
        //Email body              
        $message =
            'Event Name: ' . $name . PHP_EOL.
            'Address: ' . $address . PHP_EOL . PHP_EOL .
            'Date: ' . $date . PHP_EOL.
            'Price: ' . $price . PHP_EOL.
            'Phone: ' . $phone . PHP_EOL.
            'Website: ' . $website . PHP_EOL. PHP_EOL .
            'Contact Info: ' . $contact_info . PHP_EOL;
            
        //Send Email
        $mail_sent = @mail( $to, $subject, $message, $email_headers );
        
        $date = date_create();
        //Append Logs with result
        if ($mail_sent) {
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .'PASS: EMAIL ' . $subject . ' > ' . $to . PHP_EOL;
        } else {
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .'FAIL: EMAIL ' . $subject . ' > ' . $to . PHP_EOL;
        }
        file_put_contents("../../logs/automated-syndication-logs.txt", $to_log, FILE_APPEND);
    }
    
    //Handles form submissions to URL: http://www.retailmenot.com/submit
    //A bit more difficult than first process... Not a clear POST path, log in required first, cookies need to be preserved
    function submit_retailmenot_form($arr) {
        //cURL Post to log in to website
        $login_url = "http://www.retailmenot.com/ajax/login.php";
        $post_data = "email=syndicatordemo%40gmail.com&password=Xy123456%21&source=us-rmn-desktop";      //Pre-prepaired the data-string for this cURL
        
        $ch = curl_init();
        $agent = $_SERVER["HTTP_USER_AGENT"];
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_URL, $login_url );
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        // Save session data
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

        $login_result = curl_exec($ch);    //RESULT of login
        //print 'log in attempt: ' . curl_errno($ch) . '-' . curl_error($ch) . ' ON - ' . $login_url . 'POST: ' . $post_data . PHP_EOL;

        //Need to continue on and cURL post the coupon form
        $form_url = 'http://www.retailmenot.com/submit';
        curl_setopt($ch, CURLOPT_URL, $form_url);
        
        //post data dependent on our own data
        $post_data['domain'] = $arr['website'];
        $post_data['ft_tip'] = $arr['description'];
        //post data injected by remote site, necessary for form
        $post_data['offerType'] = 'tip';    //variable according to site form
        $post_data['source'] = '/submit';
        $post_data['slice'] = 'control';
        $post_data['csplit'] = 'control';
        $post_data['vsplit'] = 'control';
        $post_data['cread'] = 'control';
        
        //Stringify Post array data
        if (is_array($post_data)) {
            foreach ($post_data as $key => $value) {
                $post_items[] = $key . '=' . $value;
            }
             $post_string = implode('&', $post_items);
        } else {
            $post_string = $post_data.'='.$value;
        }
        //$post_string = implode('&', $post_items);
        
        //SET STRING TO POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        
        //EXECUTE POST CURL
        $submit_result = curl_exec($ch);
        
        $date = date_create();
        //PRINT THIS LINE TO ERROR LOG
        if (curl_errno($ch) == 0) {
            //PRINT PASS TO LOG
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .' PASS: ' .curl_errno($ch) . '-' . curl_error($ch) . ' ON - ' .  $form_url . 'POST: ' . $post_string . PHP_EOL;
        } else {
            //Print Error number and msg to log
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .' FAIL: ' .curl_errno($ch) . '-' . curl_error($ch) . ' ON - ' .  $form_url . 'POST: ' . $post_string . PHP_EOL;
        }
        //ADD $to_log RESULT TO txt
        file_put_contents("../../logs/automated-syndication-logs.txt", $to_log, FILE_APPEND);
        //CLOSE CURL RESOURCE
        curl_close($ch);        
    }
    
    //Handles form submissions to URL: https://www.mycoupons.com/submit-a-coupon/
    //Not 100% sure this ones going to work at all. Theres an AJAX launched filter on unfocus event in the Store/Website input field that cleans the field if its not one of the pre-approved vendors I'm assuming!
    //Going to try to push the header through anyway, not sure if this sanitation is only on the user-viewable form or will interupt a header request as well.
    function submit_mycoupons_form($arr) {
        $formActionUrl = 'https://www.mycoupons.com/api/coupon/submit';
        
        $post_data['merchant'] = 'http://XXXXXXX.com';  // I would put http://pulsd.com in here but I don't want to run a spam script with your companies name in the headers being sent out
        $post_data['type'] = 'online';
        $post_data['url'] = $arr['website'];
        $post_data['descrption'] = $arr['description'];
        $post_data['code'] = '';
        
        $exp_date = explode('-',$arr['expiration_date']);   //Break down expiration date string: MM-DD-YYYY to [0],[1],[2]
        $exp_date_new = implode ('/', $exp_date); //concat back to MM/DD/YYYY
        $post_data['dateExpiration'] = $exp_date_new;
        
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
  
        //DEBUG PRINT:   THIS LINE CAN BE USED TO VIEW WHAT THE CURL IS RETURNING IN TEXT. DISABLED FOR PRODUCTION
        //print_r(curl_getinfo($curl_connection));               
        $date = date_create();
        //PRINT THIS LINE TO ERROR LOG
        if (curl_errno($curl_connection) == 0) {
            //PRINT PASS TO LOG
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .' PASS: ' .curl_errno($curl_connection) . '-' . curl_error($curl_connection) . ' ON - ' . $formActionUrl . 'POST: ' . $post_string . PHP_EOL;
        } else {
            //Print Error number and msg to log
            $to_log = date_format($date, 'U = Y-m-d H:i:s') .' FAIL: ' .curl_errno($curl_connection) . '-' . curl_error($curl_connection) . ' ON - ' . $formActionUrl. 'POST: ' . $post_string . PHP_EOL;
        }
        //ADD $to_log RESULT TO txt
        file_put_contents("../../logs/automated-syndication-logs.txt", $to_log, FILE_APPEND);
        
        //CLOSE CURL RESOURCE
        curl_close($curl_connection);
    }
    
    //Handles automated e-mailing submissions to URL: http://www.bargainist.com/ ... Emailing to 'deals@bargainist.com'
    function submit_bargainist_email($arr) {
        //Variables for email
        //$to = 'deals@bargainist.com';                                     //FOR TESTING CHANGE TO - $to = "syndicatordemo@gmail.com";
        $to = "syndicatordemo@gmail.com";
       
        $subject = 'Event Listing: ' . $arr['name'];
        $headers = "From: joe@example.com\r\nReply-To: joe@example.com";    //Fake emails for header  

        //Variables for email body - filtered with existance check ternary op just incase they don't exist on db
        $name = $arr['name'];
        $address = (array_key_exists("address",$arr) ? $arr['address'] : 'N/A');
        $date = (array_key_exists("expiration_date",$arr) ? $arr['date'] : 'N/A');
        $price = (array_key_exists("price",$arr) ? $arr['price'] : 'N/A');
        $phone = (array_key_exists("phone",$arr) ? $arr['phone'] : 'N/A');
        $website = (array_key_exists("website",$arr) ? $arr['website'] : 'N/A');
        
        //Email body              
        $message =
            'Event Name: ' . $name . PHP_EOL.
            'Address: ' . $address . PHP_EOL . PHP_EOL .
            'Date: ' . $date . PHP_EOL.
            'Price: ' . $price . PHP_EOL.
            'Phone: ' . $phone . PHP_EOL.
            'Website: ' . $website . PHP_EOL. PHP_EOL .
            'Contact Info: ' . $contact_info . PHP_EOL;
            
        //Send Email
        $mail_sent = @mail( $to, $subject, $message, $email_headers );
        $date = date_create();
        //Append Logs with result
        if ($mail_sent) {
            $to_log = date_format($date, 'U = Y-m-d H:i:s') . ' PASS: EMAIL ' . $subject . ' > ' . $to . PHP_EOL;
        } else {
            $to_log = date_format($date, 'U = Y-m-d H:i:s') . ' FAIL: EMAIL ' . $subject . ' > ' . $to . PHP_EOL;
        }
        file_put_contents("../../logs/automated-syndication-logs.txt", $to_log, FILE_APPEND);
    }
?>