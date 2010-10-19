<html>
<head>
<Meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>iMona-IME-</title>
</head>
<body>
<center>iMona-IME-</center>
<?php
$url = $_SERVER["QUERY_STRING"];
if($url == ""){
	$url = $_POST[url];
}
if($url == ""){
	$url = $_GET[url];
}

$ver ="PHP081207";
$log = "count.dat";
#$urllog = "url.log";

//アクセス数カウント
$fp = fopen($log, "r+"); // ファイル開く
if( !$fp ) {
  $fp = fopen($log, "w");
}
//flock($fp, LOCK_SH);
$count = fgets($fp, 10); // 9桁分値読み取り
if($count == ""){
	$count = 0;
}
$count++; // 値+1（カウントアップ）
rewind($fp); // ファイルポインタを先頭に戻す
fputs($fp, $count ); // 値書き込み
//flock($fp, LOCK_UN);
fclose($fp); // ファイル閉じる

//URL_LOG（さくら氏のURLLOGを見て追加）
#$fp = fopen($urllog, "a"); // ファイル開く
#if( !$fp ) {
#  $fp = fopen($log, "w");
#}
#fputs($fp, "$url<>" ); // 値書き込み
#fclose($fp); // ファイル閉じる


//アンカー変換
$url = str_replace("$","#",$url);

//kjm.kir.jpでの原因不明バグ解消
#$url = str_replace("%3fp=","/?p=",$url);
//上のはやめてURLをデコードすることにより解消
$url = urldecode($url);

?>
<a href="http://cgi.i-mobile.co.jp/ad_link.aspx?guid=on&asid=12422&pnm=7920&asn=1"><img border="0" src="http://cgi.i-mobile.co.jp/ad_img.aspx?guid=on&asid=12422&pnm=7920&asn=1&asz=0&atp=3&lnk=339900&bg=ffffff&txt=000000" alt="i-mobile"></a><br>
<?php
//表示
#if( preg_match ( "/"."hatena\.ne\.jp|kjm\.kir\.jp|pita\.st|114090\.(com|jp|biz|tv|org)|pic\.to"."/i", $url)  ) { #携帯対応または携帯ページの場合
#	echo "携帯対応ページです。そのままアクセス出来ます。<br>";
#}else{
#	echo "携帯非対応ページです。<br>";
#}
echo "0.元URL：<a href=\"http://$url\" accesskey=0>http://$url</a>を変換します。<hr>";
if ( preg_match ( "/"."\.gif\z|\.png\z|\.jpg\z|\.jpeg\z|\.bmp\z|\.ico\z"."/i", $url, $match ) ) {#画像の場合
	echo "<a href=\"http://lupo.jp/gl7.php?g_url=http://$url\" accesskey=1>1.ぐるっぽ</a><BR>
<a href=\"http://b.u.la/mise/henkan.cgi?url=$url\" accesskey=2>2.みせぶら</a><BR>
<a href=\"http://pic.to/$url\" accesskey=3>3.ピクト</a><BR>
<a href=\"http://mld.fileseek.net/cgi-bin/getimg.cgi?u=http://$url\" accesskey=4>4.ファイルシーク</a><BR>
<a href=\"http://s1.srea.jp/r.php?esi=http://$url\" accesskey=6>6.リサイズ</a><BR>
<a href=\"http://gw.mobile.goo.ne.jp/gw/http://$url\" accesskey=7>7.goo</a><BR>
<a href=\"http://google.co.jp/gwt/n?u=http://$url\" accesskey=8>8.google</a><BR>";
}else{#普通のページの場合
	if ( preg_match ( "/"."ja\.wikipedia\.org\/wiki\/(.*)"."/i", $url, $match ) ) {#wikipediaの場合
		$name = $match[1];
		echo "<a href=\"http://mobile.seisyun.net/cgi/wgate/$name/a\">暇つぶしwikipedia</a><BR><a href=\"http://wpedia.mobile.goo.ne.jp/wiki/$name\" accesskey=#>#.ﾓﾊﾞｲﾙgoo Wikipedia検索</a><BR>";
	}
	echo "<a href=\"http://mac.io/i.php?-_u=$url\" accesskey=1>1.macブラウザ</a><BR>
<a href=\"http://lupo.jp/gl7.php?g_url=http://$url\" accesskey=2>2.ぐるっぽ</a><BR>
<a href=\"http://p01.fileseek.net/cgi-bin/p.cgi?uR=http://$url\" accesskey=3>3.ファイルシーク</a><BR>
<a href=\"http://poke.u.la/pcview.cgi/g/$url\" accesskey=4>4.ポケブラ</a><BR>
<a href=\"http://www.sjk.co.jp/c/w.exe?y=http://$url\" accesskey=5>5.通勤ブラウザ</a><BR>
<a href=\"http://froute.jp/bbgate/index.cgi?MoN=g&UoN=http://$url\" accesskey=6>6.携帯BBブラウザ</a><BR>
<a href=\"http://i.ringoya.nu/?u0=$url\" accesskey=7>7.ringoya</a><BR>
<a href=\"http://202.229.22.194/cgi-bin/i.pl?u=http://$url&m=0&m=0903\" accesskey=8>8.i-コンバート</a><BR>
<a href=\"http://mobazilla.ax-m.jp/?http://$url\" accesskey=9>9.mobazilla</a><BR>
<a href=\"http://feed.moo.jp/p/result.php?fdm_l=1&fdm_u=http://$url\" accesskey=>フィードモ</a><BR>
<a href=\"http://google.co.jp/gwt/n?u=http://$url\">google</a><BR>
<a href=\"http://mgw.hatena.ne.jp/?http://$url\">はてな</a><BR>";
}
echo "<form action=i>*.<input name=\"copy\" type=\"text\" accesskey=\"*\" value=\"http://$url\"></form>";
echo "<hr>Ver.$ver count.$count";
?>
</body>
</html>
