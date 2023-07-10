<?php

if (1==1 || date('i')%2==0) {
    $languages = array_slice(array_keys(mysqli_fetch_assoc(mysqli_query1($con, "SELECT * FROM translation LIMIT 1"))), 2);
    $r = mysqli_fetch_assoc(mysqli_query1($con, "SELECT * FROM translation WHERE en!='' AND (" . implode(' IS NULL OR ', $languages) . " IS NULL) ORDER BY RAND() LIMIT 1"));
    $r=shuffle_assoc($r);
    $lang = array_search('', $r);
    if (!empty($r['en'])) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" . str_replace('cn', 'zh-CN', $lang) . "&dt=t&q=" . urlencode($r['en']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rg = (array)json_decode(curl_exec($ch), true);
        curl_close($ch);
        $dst = null;
        foreach ($rg[0] AS $s) {
            $dst[] = trim($s[0]);
        }
        $dst = implode(' ', $dst);

        if (!empty($dst)) {
            mysqli_query1($con, "UPDATE translation SET timestamp=" . time() . ",$lang='" . mysqli_real_escape_string($con, $dst) . "' WHERE hash='$r[hash]'");
            //sn('Translated:' . PHP_EOL . $r['en'] . PHP_EOL . PHP_EOL . $lang . PHP_EOL . $dst);
        } else {
            sn("Translation to $lang response empty: " . $r['en']);
        }
    } else {
        require_once '../../translate.php';
        if (mysqli_fetch_array(mysqli_query1($con,"SELECT MAX(timestamp) FROM translation"))[0]>translate_db('updated')){
            $code='<?php'.PHP_EOL.'function translate_db($hash,$lang=null){'.PHP_EOL;
            $rr=mysqli_query1($con,"SELECT * FROM translation ORDER BY timestamp ASC");
            while($r=mysqli_fetch_assoc($rr)){
                $last_time=$r['timestamp'];
                $code.="if(\$hash=='$r[hash]'){".PHP_EOL;
                foreach (array_slice($r,3) AS $lang=>$translation){
                    $translation=str_replace("'","\'",$translation);
                    $code.=" if(\$lang=='$lang') return '$translation';".PHP_EOL;
                }
                $code.='}'.PHP_EOL;
            }
            $code.="if(\$hash=='updated') return ".time().";".PHP_EOL;
            $code.='}'.PHP_EOL.'?>';
            file_put_contents('../../translate.php',$code);
            sn('Translation file updated');
        }
        //sn('Nothing to translate');
    }
}