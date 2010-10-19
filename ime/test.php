<?php
    function ex_file_get_contents($server='',$timeout=10){
        $data = '';
        if(isset($server)){
            $fp = @fsockopen($server, 80,$errno, $errstr, $timeout);
            if($fp){
                while($eof_check=@fread($fp, 1024)){
                    $data .= $eof_check;
                }
                @fclose($fp);
            }else{
                return false;
            }
        }else{
            return false;
        }
        return $data;
    } 
function adtune_get_text($param){

    $url = "http://lotate.adtune.jp/ad_txt.php?type=text";
    foreach ($param as $key => $value) {
        $url .= '&' . $key . '=' . urlencode($value);
    }
    //echo $url;
    $tag = ex_file_get_contents($url,60);
    return mb_convert_encoding($tag, $param['oe'], "auto" );
    
}
//パートナーサイトキー
$param['pskey']='ie88j4sb';
$param['host']=$_SERVER['HTTP_HOST'];
$param['ip']=$_SERVER['REMOTE_ADDR'];
$param['ref']=$_SERVER['HTTP_REFERER'];
$param['url']=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$param['useragent']=$_SERVER['HTTP_USER_AGENT'];
//edit_parameter
// 取得タグの文字エンコード指定
// Shift-Jisで取得したい場合            : SJIS
// UTF-8で取得したい場合(デフォルト値)  : UTF8
/ EUC-JPで取得したい場合               : EUCJP
$param['oe']='UTF8';

//バナータグを取得
$ad_text = adtune_get_text($param);
echo $ad_text;
?>