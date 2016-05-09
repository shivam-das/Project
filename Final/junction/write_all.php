<?php

$rows=$_POST["rows"];
//landmarks,intersections

$filename="output.txt";

// echo $filename;

$myfile = file_put_contents($filename, $rows.PHP_EOL);

echo "$filename successfully written";