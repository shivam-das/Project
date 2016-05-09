<?php
    if(0>$_FILES['file']['error']){
        echo 'Error: '.$_FILES['files']['error'].<br>
    }
    else{
        move_uploaded_file($_FILES['file']['tmp_name'], 'input/'.$_FILES['file']['name']);
    }

?>
