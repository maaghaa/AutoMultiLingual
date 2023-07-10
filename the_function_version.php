<?php


//Please consider that you have to have an active connection to the database $con

/**
 * You can use WW keyword inside the text to add non-translatable word in it. 
 * for example the text can be "Hello dear WW!";
 * And then you can add extra parameters to the function that each of them gets replaces with one of the (WW)s.
 * Example: Lang("Hello dear WW!",$user_nickname)
*/
function Lang(string $text){
    global $lang,$con;

    //If no language is selected set en as default
    if (empty($lang)) {$lang='en';}

    //trim and lower the text so that non-important changes in the string don't cause inserting duplicated rows
    $text=strtolower(trim($text));
    
    $hash=md5($text);

    $rt=mysqli_query($con,"SELECT $lang FROM translation WHERE hash='$hash'");

    if (mysqli_num_rows($rt)>0){
        $rt=mysqli_fetch_array(0);
    }else{
        mysqli_query($con,"INSERT INTO translation (hash, timestamp, en) VALUES ('$hash', time(), '$text')");
    }
    
    if (empty($rt)) $rt=$text;

    $args=func_get_args();

    for($a=1;$a<count($args);$a++){
        $rt=preg_replace('/WW/',trim($args[$a]),$rt,1);
    }

    return $rt;
}

?>