<?php

$con = "Connection to the database with access to translation table";

$languages = array_slice(array_keys(mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM translation LIMIT 1"))), 2);
$r = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM translation WHERE en!='' AND (" . implode(' IS NULL OR ', $languages) . " IS NULL) ORDER BY RAND() LIMIT 1"));
$r = shuffle_assoc($r);
$lang = array_search('', $r);
if (!empty($r['en'])) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . str_replace('cn', 'zh-CN', $lang) . "&dt=t&q=" . urlencode($r['en']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $rg = (array)json_decode(curl_exec($ch), true);
    curl_close($ch);
    $dst = null;
    foreach ($rg[0] as $s) {
        $dst[] = trim($s[0]);
    }
    $dst = implode(' ', $dst);

    if (!empty($dst)) {
        mysqli_query($con, "UPDATE translation SET timestamp=" . time() . ",$lang='" . mysqli_real_escape_string($con, $dst) . "' WHERE hash='$r[hash]'");
        //sn('Translated:' . PHP_EOL . $r['en'] . PHP_EOL . PHP_EOL . $lang . PHP_EOL . $dst);
    } else {
        echo "Translation to $lang response empty. Most probably your IP got banned. source string: " . $r['en'];
    }
}


