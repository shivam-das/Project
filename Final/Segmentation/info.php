<?php
$data = json_decode(stripslashes($_POST['data']));
/*$srclat=$_POST['lat'];
$srclong=$_POST['lng'];
$line = $srclat.",".$srclong;

*/
$filename = "newfile.txt";
foreach($data as $line){
$myfile = fopen($filename, "a") or die("Unable to open file!");
fwrite($myfile,$line.PHP_EOL);
}
echo $line;
fclose($myfile); 
?>