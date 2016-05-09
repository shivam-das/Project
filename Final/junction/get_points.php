<?php
$point_list=[];
foreach (glob("input/*.txt") as $file_name) {
    $file_handle = fopen($file_name, "r");
    $points =[];
    // echo "now reading $file_name <br>";
    // while (!feof($file_handle)) {
    //     $line = fgets($file_handle);
    //     $point[] = $line;
    //     // echo $line;
    // }
    fgetcsv($file_handle);
    
    while(! feof($file_handle)) {
        $line=fgetcsv($file_handle);
        $point=[floatval($line[0]),floatval($line[1])];
        $points[]=$point;
    }

    $point_list[$file_name]= $points;
    fclose($file_handle);
}

echo json_encode($point_list);