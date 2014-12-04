<!--
==========================================================================================================
    curl-test.php
==========================================================================================================
    This is a script test to see the value returned when using CURL to log in to retailmenot.com and submit form    

-->
<?php

    //This is a script for testing to see the return value when you CURL the FIREBASE db
    include('php/scripts/syndication_processes.php');   //Syndication functions in here
    
    $arr = '';
    submit_retailmenot_form($arr);
    
    
?>