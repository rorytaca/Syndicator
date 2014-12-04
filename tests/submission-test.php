<!--
==========================================================================================================
    submission-test.php
==========================================================================================================
    This is another script that I used to debug an individual syndication processes. Called them manually for my web browser and printed out json returns or curl logs
    

-->

<?php
    //This script is for testing all the functions in syndication_process.php:
    //Tests are using pre-defined variables and not the JSON objects that will actually be fed into functions by 'php/scripts/automate_processes.php';
    include('php/scripts/syndication_processes.php');
    
    
    $arr['name'] = 'Test Name';
    $arr['description'] = 'Just a description';
    $arr['expiration_date'] = '12-09-2014'; 
    
    //DEALCATCHERS.COM TEST - 'MOST BASIC' FORM SUBMISSION OF THE FIVE
    //submit_dealcatchers_form($arr);                   //DEBUGGED AND WORKING
    
    //TIMEOUT.COM/NEWYORK TEST - 'MOST BASIC' EMAIL SUBMISSION OF THE FIVE
    $arr['name'] = 'Test Name';
    $arr['address'] = '456 Test Ave, New York, NY 12345';
    $arr['price'] = '99.99';
    $arr['phone'] = '123-456-7890';
    $arr['website'] = 'http://www.website.com/';
    
    echo 'Testing timeout newyork email: process initiated';
    submit_timeout_newyork_email($arr);
    
    
    
    
    //$arr['price'] = '';
    //$arr['price'] = '';
    //$arr['price'] = '';

?>