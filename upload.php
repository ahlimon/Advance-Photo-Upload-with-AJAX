<?php

if(isset($_FILES)){
   
    $i = 1;
    foreach($_FILES as $key => $data){
           
        if (move_uploaded_file($data['tmp_name'], __DIR__ . '/uploads/' . $i . '--' . $data['name'])) {
            //echo "success";
            print_r($data['name']);
        } else {
            echo "error";
        }
       
        $i++;
    }
}