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
    if (empty($lang)) {$lang='en';}

    $text=trim($text);
    
    $hash=md5(strtolower($text));

    $rt=mysqli_fetch_array(mysqli_query($con,"SELECT $lang FROM translation WHERE hash='$hash'"))[0];
    
    if (empty($rt)) $rt=$text;

    $args=func_get_args();

    for($a=1;$a<count($args);$a++){
        $rt=preg_replace('/WW/',trim($args[$a]),$rt,1);
    }

    return $rt;
}

?>