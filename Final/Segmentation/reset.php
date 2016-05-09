<?php

echo "Everything Fine";
$files = glob('SegmentedTrails/*');
foreach($files as $file){
    if(is_file($file)){
        unlink($file);
    }
}  

$files2 = glob('GroundTruth/*');
foreach($files2 as $file1){
    if(is_file($file1)){
        unlink($file1);
    }
}  

?>
       