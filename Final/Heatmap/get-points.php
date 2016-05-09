<?php

    echo exec("python HeatPlot.py");
    $myfile = fopen("heat.txt","r");
    echo fread($myfile,filesize("heat.txt"));
    echo "end";
    fclose($myfile);
    
?>