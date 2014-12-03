<?php

    include('php/scripts/form_submissions.php');
    
    
    $arr['name'] = 'Test Name';
    $arr['description'] = 'Just a description';
    $arr['expiration_date'] = '12-09-2014'; 
    
    //DEAL CATCHERS TEST - MOST BASIC FORM SUBMISSION
    submit_dealcatchers_form($arr);

?>