<?php
ini_set('max_execution_time', 0);
$connstr="host=localhost dbname=postgis_22_sample user=postgres password=root";
//$connection=pg_connect($connstr);
$fl=0;
if ($connection=pg_connect($connstr))
{
  // echo "hello!!";
}

$srclat=$_POST['sourcelat'];
$srclong=$_POST['sourcelong'];
$destlat=$_POST['destlat'];
$destlong=$_POST['destlong'];
$route=3;
//echo $sourcelat." ".$sourcelong." ".$destlat." ".$destlong;
while ($route > 0) {
        //echo $route . " ";+
        //select destination busstop
        $qdst = "SELECT lat,lng,route_id,direction,bus_stop_id from bus_stop_heads WHERE route_id=$route and 
       ST_Distance(ST_GeographyFromText('POINT($destlong $destlat)'),location)<300
       ORDER BY ST_Distance(ST_GeographyFromText('POINT($destlong $destlat)'),location)
       LIMIT 1";
        $qt   = pg_query($connection, $qdst);
        //echo pg_num_rows($qt);
        //var_dump($qt);
        if(pg_num_rows($qt)==0)
        {
            $fl++;
            //echo $fl;
            $route-=1;
            continue;
        }
        $qtt  = pg_fetch_row($qt);
        //echo "kkllllllllllllllllllll".$qtt;
        /*
           */
        
        //echo $qtt[0]."    ";
        //to check if a point lies in threshhold range
        if ($qtt) {
            
            
            
            //select source busstop
            $qsrc = "SELECT lat,lng,route_id,direction,bus_stop_id from bus_stop_heads WHERE route_id=$route
       AND direction='$qtt[3]' and 
       ST_Distance(ST_GeographyFromText('POINT($srclong $srclat)'),location)<300
       ORDER BY ST_Distance(ST_GeographyFromText('POINT($srclong $srclat)'),location)
       LIMIT 1";
            $qt2  = pg_query($connection, $qsrc);
            $qtt2 = pg_fetch_row($qt2);
            if(pg_num_rows($qt2)==0){
                $fl++;
            //echo $fl;
            $route-=1;
            continue;
               
            }
            echo "dest," . $qtt[0] . "," . $qtt[1] . "," . $qtt[2] . "," . $qtt[3] . "," . $qtt[4] . "\n";
            echo "source," . $qtt2[0] . "," . $qtt2[1] . "," . $qtt2[2] . "," . $qtt2[3] . "," . $qtt2[4] . "\n";
            
            
            //direction check
            if ($qtt[4] < $qtt2[4]) {
                echo "opposite direction\n";
                $qdst = "SELECT lat,lng,route_id,direction,bus_stop_id from bus_stop_heads WHERE route_id=$route and direction!='$qtt[3]' and 
       ST_Distance(ST_GeographyFromText('POINT($destlong $destlat)'),location)<300
       ORDER BY ST_Distance(ST_GeographyFromText('POINT($destlong $destlat)'),location)
       LIMIT 1";
                $qt   = pg_query($connection, $qdst);
                $qtt  = pg_fetch_row($qt);
                echo "dest," . $qtt[0] . "," . $qtt[1] . "," . $qtt[2] . "," . $qtt[3] . "," . $qtt[4] . "\n";
                
                $qsrc = "SELECT lat,lng,route_id,direction,bus_stop_id from bus_stop_heads WHERE route_id=$route AND direction='$qtt[3]' and 
       ST_Distance(ST_GeographyFromText('POINT($srclong $srclat)'),location)<300
     ORDER BY ST_Distance(ST_GeographyFromText('POINT($srclong $srclat)'),location)
       LIMIT 1";
                $qt2  = pg_query($connection, $qsrc);
                $qtt2 = pg_fetch_row($qt2);
                echo " source," . $qtt2[0] . "," . $qtt2[1] . "," . $qtt2[2] . "," . $qtt2[3] . "," . $qtt2[4] . "\n";
            }
            
            
    echo "ll\n";        
            
            
            
            //selecting intermediate busstops
            //echo "busstop" . "\n";
          /*  $queryt = "SELECT ST_AsText(bus_stop_heads.location),wait_time.whole_day,wait_time.early_morning, wait_time.morning,
wait_time.noon,wait_time.evening,wait_time.night from bus_stop_heads JOIN wait_time ON wait_time.route_id=bus_stop_heads.route_id AND 
wait_time.bus_stop_id=bus_stop_heads.bus_stop_id AND wait_time.direction=bus_stop_heads.direction
 WHERE bus_stop_heads.route_id=$route AND bus_stop_heads.direction='$qtt[3]' AND (bus_stop_heads.bus_stop_id>$qtt2[4]-1 AND bus_stop_heads.bus_stop_id<$qtt[4]+1)";*/
            $queryt="SELECT bus_stop_heads.lat,bus_stop_heads.lng,wait_time.whole_day,wait_time.early_morning, wait_time.morning,
wait_time.noon,wait_time.evening,wait_time.night,distance_spatial_spread.spatial_spread,distance_spatial_spread.distance,travel_time.whole_day,bus_stop_heads.route_id,bus_stop_heads.bus_stop_id,trail_participation.whole_day,trail_participation.early_morning,trail_participation.morning,trail_participation.noon,trail_participation.evening,trail_participation.night 
 from bus_stop_heads

JOIN wait_time ON
 wait_time.route_id=bus_stop_heads.route_id AND 
wait_time.bus_stop_id=bus_stop_heads.bus_stop_id AND 
wait_time.direction=bus_stop_heads.direction

JOIN distance_spatial_spread ON
distance_spatial_spread.route_id=bus_stop_heads.route_id AND
distance_spatial_spread.bus_stop_id=bus_stop_heads.bus_stop_id AND 
distance_spatial_spread.direction=bus_stop_heads.direction

JOIN travel_time ON
travel_time.route_id=bus_stop_heads.route_id AND
travel_time.bus_stop_id=bus_stop_heads.bus_stop_id AND 
travel_time.direction=bus_stop_heads.direction

JOIN trail_participation ON
trail_participation.route_id=bus_stop_heads.route_id AND
trail_participation.bus_stop_id=bus_stop_heads.bus_stop_id AND 
trail_participation.direction=bus_stop_heads.direction

 WHERE bus_stop_heads.route_id=$route AND 
bus_stop_heads.direction='$qtt[3]' AND 
(bus_stop_heads.bus_stop_id>$qtt2[4]-1 AND bus_stop_heads.bus_stop_id<$qtt[4]+1)";
            
    $qtb = pg_query($connection, $queryt);
            while ($qttb = pg_fetch_row($qtb)) {
                echo $qttb[0] . "," . $qttb[1] . "," . $qttb[2] . "," . $qttb[3] . "," . $qttb[4] . "," . $qttb[5] . "," . $qttb[6]. "," . $qttb[7]. "," . $qttb[8]. "," . $qttb[9]. "," . $qttb[10]. "," . $qttb[11]. "," . $qttb[12]. "," . $qttb[13]. "," . $qttb[14]. "," . $qttb[15]. "," . $qttb[16]. "," . $qttb[17]. "," . $qttb[18];
                echo "\n";
            }
            echo "lat\n";
            

            
            
            //trail plot
            
     $qns    = "SELECT slno FROM gps_trace WHERE (route_id=$route AND direction='$qtt2[3]' AND trail_id=10) ORDER BY ST_Distance(ST_GeographyFromText('POINT($qtt2[1] $qtt2[0])'),location_data) LIMIT 1";
            $qnspq  = pg_query($connection, $qns);
            $qnspfr = pg_fetch_row($qnspq);
            //echo $qnspfr[0].",";
    $qnd    = "SELECT slno FROM gps_trace WHERE (route_id=$route AND direction='$qtt2[3]' AND trail_id=10) ORDER BY ST_Distance(ST_GeographyFromText('POINT($qtt[1] $qtt[0])'),location_data) LIMIT 1";
            $qndpq  = pg_query($connection, $qnd);
            $qndpfr = pg_fetch_row($qndpq);
            //echo $qndpfr[0];
            //echo "trails" . "\n";
            
      $queryt = "SELECT lat,lng,route_id,slno from gps_trace WHERE (route_id=$route AND trail_id=10 AND direction='$qtt2[3]' AND (slno>$qnspfr[0] AND slno<$qndpfr[0]))
          ORDER BY slno";
            $qt     = pg_query($connection, $queryt);
            $count=0;
            while ($qtt = pg_fetch_row($qt)) {
                $color=$route;
                if($count<=20){
                    echo $qtt[0] . "," . $qtt[1] . "," . $qtt[2]. "," . $qtt[3]. "," .$color."\n";
                    $count++;
                }
                else
                {
                    if($count<=40)
                        $count++;
                    else
                        $count=0;
                }
            }
                
            echo "next_route\n";
        }
        $route = $route - 1;
        $qtt=0;
    }
//echo $fl;
    if($fl==3)
    {
        
        //-------------------------Modified---------------------------------------------//
        $route=3;
//echo $sourcelat." ".$sourcelong." ".$destlat." ".$destlong;
while ($route > 0) {
        //select destination busstop
        $qdst = "SELECT lat,lng,route_id,direction,bus_stop_id from bus_stop_heads WHERE route_id=$route and 
       ST_Distance(ST_GeographyFromText('POINT($destlong $destlat)'),location)<300
       ORDER BY ST_Distance(ST_GeographyFromText('POINT($destlong $destlat)'),location)
       LIMIT 1";
        $qt   = pg_query($connection, $qdst);
        //echo pg_num_rows($qt);
        //var_dump($qt);
        if(pg_num_rows($qt)==0)
        {
            $fl++;
            //echo $fl;
            $route-=1;
            continue;
        }
        $qtt  = pg_fetch_row($qt);
        
        if ($qtt) {
            
            $sRoute=3;
        while ($sRoute > 0) {
            //select source busstop
                $qsrc = "SELECT lat,lng,route_id,direction,bus_stop_id from bus_stop_heads WHERE route_id=$sRoute
           AND direction='$qtt[3]' and 
           ST_Distance(ST_GeographyFromText('POINT($srclong $srclat)'),location)<300
           ORDER BY ST_Distance(ST_GeographyFromText('POINT($srclong $srclat)'),location)
           LIMIT 1";
                $qt2  = pg_query($connection, $qsrc);
                
                if(pg_num_rows($qt2)==0){
                    
                //echo $fl;
                $sRoute-=1;
                continue;
                }
            $qtt2 = pg_fetch_row($qt2);
            echo "dest," . $qtt[0] . "," . $qtt[1] . "," . $qtt[2] . "," . $qtt[3] . "," . $qtt[4] . "\n";
            echo "source," . $qtt2[0] . "," . $qtt2[1] . "," . $qtt2[2] . "," . $qtt2[3] . "," . $qtt2[4] . "\n";
            
                //echo $fl;
                $sRoute-=1;
            }
            $route-=1;
        }
    //Destination route break point
    $qdbpt = "Select slat,slng,route_id,direction,bus_stop_id from breakpoint where route_id=$qtt[2] and direction = '$qtt[3]' and break_route_id = $qtt2[2] order by abs(bus_stop_id-$qtt[4]) limit 1";
    
    $qdbpt1 = pg_query($connection, $qdbpt);
    $qdbpt2 = pg_fetch_row($qdbpt1);
    if(pg_num_rows($qdbpt1)==0)
    {
        echo "No data";
    }
    //echo "source". $qdbpt2[0].",".$qdbpt2[1].",".$qdbpt2[2].",".$qdbpt2[3].",".$qdbpt2[4]."\n";
    echo "ll\n";
    //Source route break point
    $qsbpt = "Select slat,slng,route_id,direction,bus_stop_id from breakpoint where route_id = $qtt2[2] and direction = '$qtt2[3]'
              and break_route_id = $qtt[2] order by abs(bus_stop_id-$qtt2[4]) limit 1";
    $qsbpt1 = pg_query($connection, $qsbpt);
    $qsbpt2 = pg_fetch_row($qsbpt1);
    if(pg_num_rows($qsbpt1)==0)
    {
        echo "No data".$qtt2[2]." ".$qtt2[3]." not printing\n";
    }
    //echo "source_bp".$qsbpt2[0].",".$qsbpt2[1].",".$qsbpt2[2].",".$qsbpt2[3].",".$qsbpt2[4]."\n";
    
}
    //Destination bus_stops    
    $queryt="SELECT bus_stop_heads.lat,bus_stop_heads.lng,wait_time.whole_day,wait_time.early_morning, wait_time.morning,
wait_time.noon,wait_time.evening,wait_time.night,distance_spatial_spread.spatial_spread,distance_spatial_spread.distance,travel_time.whole_day,bus_stop_heads.route_id,bus_stop_heads.bus_stop_id,trail_participation.whole_day,trail_participation.early_morning,trail_participation.morning,trail_participation.noon,trail_participation.evening,trail_participation.night 
 from bus_stop_heads

JOIN wait_time ON
 wait_time.route_id=bus_stop_heads.route_id AND 
wait_time.bus_stop_id=bus_stop_heads.bus_stop_id AND 
wait_time.direction=bus_stop_heads.direction

JOIN distance_spatial_spread ON
distance_spatial_spread.route_id=bus_stop_heads.route_id AND
distance_spatial_spread.bus_stop_id=bus_stop_heads.bus_stop_id AND 
distance_spatial_spread.direction=bus_stop_heads.direction

JOIN travel_time ON
travel_time.route_id=bus_stop_heads.route_id AND
travel_time.bus_stop_id=bus_stop_heads.bus_stop_id AND 
travel_time.direction=bus_stop_heads.direction

JOIN trail_participation ON
trail_participation.route_id=bus_stop_heads.route_id AND
trail_participation.bus_stop_id=bus_stop_heads.bus_stop_id AND 
trail_participation.direction=bus_stop_heads.direction


WHERE bus_stop_heads.route_id=$qtt[2] AND 
bus_stop_heads.direction='$qtt[3]' AND 
((bus_stop_heads.bus_stop_id<$qdbpt2[4]+1 AND bus_stop_heads.bus_stop_id>$qtt[4]-1)OR(bus_stop_heads.bus_stop_id<$qtt[4]+1 AND bus_stop_heads.bus_stop_id>$qdbpt2[4]-1))";
            
    $qtb = pg_query($connection, $queryt);
        if(pg_num_rows($qtb)==0)
    {
        echo "No data".$qtt[2]." ".$qtt[3]." ".$qdbpt2[4]." ".$qtt[4]."\n";
    }
            while ($qttb = pg_fetch_row($qtb)) {
                echo $qttb[0] . "," . $qttb[1] . "," . $qttb[2] . "," . $qttb[3] . "," . $qttb[4] . "," . $qttb[5] . "," . $qttb[6]. "," . $qttb[7]. "," . $qttb[8]. "," . $qttb[9]. "," . $qttb[10]. "," . $qttb[11]. "," . $qttb[12]. "," . $qttb[13]. "," . $qttb[14]. "," . $qttb[15]. "," . $qttb[16]. "," . $qttb[17]. "," . $qttb[18];
                echo "\n";
            }
    //Bus stops between source and source breakpoint
    $queryt2="SELECT bus_stop_heads.lat,bus_stop_heads.lng,wait_time.whole_day,wait_time.early_morning, wait_time.morning,
wait_time.noon,wait_time.evening,wait_time.night,distance_spatial_spread.spatial_spread,distance_spatial_spread.distance,travel_time.whole_day,bus_stop_heads.route_id,bus_stop_heads.bus_stop_id,trail_participation.whole_day,trail_participation.early_morning,trail_participation.morning,trail_participation.noon,trail_participation.evening,trail_participation.night 
 from bus_stop_heads

JOIN wait_time ON
 wait_time.route_id=bus_stop_heads.route_id AND 
wait_time.bus_stop_id=bus_stop_heads.bus_stop_id AND 
wait_time.direction=bus_stop_heads.direction

JOIN distance_spatial_spread ON
distance_spatial_spread.route_id=bus_stop_heads.route_id AND
distance_spatial_spread.bus_stop_id=bus_stop_heads.bus_stop_id AND 
distance_spatial_spread.direction=bus_stop_heads.direction

JOIN travel_time ON
travel_time.route_id=bus_stop_heads.route_id AND
travel_time.bus_stop_id=bus_stop_heads.bus_stop_id AND 
travel_time.direction=bus_stop_heads.direction

JOIN trail_participation ON
trail_participation.route_id=bus_stop_heads.route_id AND
trail_participation.bus_stop_id=bus_stop_heads.bus_stop_id AND 
trail_participation.direction=bus_stop_heads.direction


WHERE bus_stop_heads.route_id=$qtt2[2] AND 
bus_stop_heads.direction='$qtt2[3]' AND 
((bus_stop_heads.bus_stop_id<$qsbpt2[4]+1 AND bus_stop_heads.bus_stop_id>$qtt2[4]-1)or(bus_stop_heads.bus_stop_id<$qtt2[4]+1 AND bus_stop_heads.bus_stop_id>$qsbpt2[4]-1))";
            
    $qtb2 = pg_query($connection, $queryt2);
        if(pg_num_rows($qtb2)==0)
    {
       echo "No data".$qtt2[2]." ".$qtt2[3]." ".$qtt2[4]." ".$qsbpt2[4]."\n";
    }
            while ($qttb2 = pg_fetch_row($qtb2)) {
                echo $qttb2[0] . "," . $qttb2[1] . "," . $qttb2[2] . "," . $qttb2[3] . "," . $qttb2[4] . "," . $qttb2[5] . "," . $qttb2[6]. "," . $qttb2[7]. "," . $qttb2[8]. "," . $qttb2[9]. "," . $qttb2[10]. "," . $qttb2[11]. "," . $qttb2[12]. "," . $qttb2[13]. "," . $qttb2[14]. "," . $qttb2[15]. "," . $qttb2[16]. "," . $qttb2[17]. "," . $qttb2[18];
                echo "\n";
            }
            
        
        
        echo "lat\n";
                 //trail plot
            
     $qns    = "SELECT slno FROM gps_trace WHERE (route_id=$qtt[2] AND direction='$qtt[3]' AND trail_id=10) ORDER BY ST_Distance(ST_GeographyFromText('POINT($qdbpt2[1] $qdbpt2[0])'),location_data) LIMIT 1";
            $qnspq  = pg_query($connection, $qns);
            $qnspfr = pg_fetch_row($qnspq);
            //echo $qnspfr[0]."\n";
    $qnd    = "SELECT slno FROM gps_trace WHERE (route_id=$qtt[2] AND direction='$qtt[3]' AND trail_id=10) ORDER BY ST_Distance(ST_GeographyFromText('POINT($qtt[1] $qtt[0])'),location_data) LIMIT 1";
            $qndpq  = pg_query($connection, $qnd);
            $qndpfr = pg_fetch_row($qndpq);
            //echo $qndpfr[0]."\n";
            
        
        //echo "trails" . "\n";
        
      $Queryt = "SELECT lat,lng,route_id,slno from gps_trace WHERE (route_id=$qtt[2] AND trail_id=10 AND direction='$qtt[3]' AND ((slno<$qnspfr[0] AND slno>$qndpfr[0])OR(slno<$qndpfr[0] AND slno>$qnspfr[0]))) ORDER BY slno";
            $qt     = pg_query($connection, $Queryt);
            $count=0;
            while ($qtt = pg_fetch_row($qt)) {
                $color=$qtt[2];
                if($count<=20){
                    echo $qtt[0] . "," . $qtt[1] . "," . $qtt[2]. "," . $qtt[3]. "," .$color."\n";
                    $count++;
                }
                else
                {
                    if($count<=40)
                        $count++;
                    else
                        $count=0;
                }
            }
       // echo "break"."\n";
        $qns2    = "SELECT slno FROM gps_trace WHERE (route_id=$qtt2[2] AND direction='$qtt2[3]' AND trail_id=10) ORDER BY ST_Distance(ST_GeographyFromText('POINT($qsbpt2[1] $qsbpt2[0])'),location_data) LIMIT 1";
            $qnspq2  = pg_query($connection, $qns2);
            $qnspfr2 = pg_fetch_row($qnspq2);
            //echo $qnspfr2[0]."\n";
    $qnd2    = "SELECT slno FROM gps_trace WHERE (route_id=$qtt2[2] AND direction='$qtt2[3]' AND trail_id=10) ORDER BY ST_Distance(ST_GeographyFromText('POINT($qtt2[1] $qtt2[0])'),location_data) LIMIT 1";
            $qndpq2  = pg_query($connection, $qnd2);
            $qndpfr2 = pg_fetch_row($qndpq2);
            //echo $qndpfr2[0]."\n";
            
        
        //echo "trails" . "\n";
            
      $Queryt2 = "SELECT lat,lng,route_id,slno from gps_trace WHERE (route_id=$qtt2[2] AND trail_id=10 AND direction='$qtt2[3]' AND ((slno<$qnspfr2[0] AND slno>$qndpfr2[0])OR(slno<$qndpfr2[0] AND slno>$qnspfr2[0])))
          ORDER BY slno";
            $qt     = pg_query($connection, $Queryt2);
            $count=0;
            while ($qstt = pg_fetch_row($qt)) {
                $color=$qtt2[2];
                if($count<=20){
                    echo $qstt[0] . "," . $qstt[1] . "," . $qstt[2]. "," . $qstt[3]. "," .$color."\n";
                    $count++;
                }
                else
                {
                    if($count<=40)
                        $count++;
                    else
                        $count=0;
                }
            }
                
            echo "next_route\n";
        echo $qdbpt2[0].",".$qdbpt2[1]."\n";
        echo $qsbpt2[0].",".$qsbpt2[1]."\n";
        echo "breakpoint\n";
        echo "nope";
    }
//echo "pp";


   