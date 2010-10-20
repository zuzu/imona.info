<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title>iMona for iアプリダウンロード所</title>
</head>
<body>
<p>
iMona(iｱﾌﾟﾘ)のダウンロード<br>
<?php include "../../ad.php"; ?>
<hr size="1">
<form action="index.php" method="post" enctype="multipart/form-data">
<?php
/*if( preg_match( "/DoCoMo\/1\.0\/([0-9a-zA-Z]+?)(\/)?/i", $_SERVER['HTTP_USER_AGENT'], $_) ){

}elseif( preg_match( "/DoCoMo/2.0[^0-9a-zA-Z]+([0-9a-zA-Z]+?)/i", $_SERVER['HTTP_USER_AGENT'], $_ ) ){
}*/
$boardurl = "http://o.imona.info/";
$board = "@独自版";
$filename = $filename."_o";
//docomoの機種の画面サイズのデータ
$datafile = "docomo.data";
$auto = $_POST["auto"];
$type = "";
if($auto != ""){
	$docomo = file($datafile);
	//$auto = strtoupper($auto);
	$fp = fopen("docomo.data","r");
	while( ! feof( $fp ) ){
  	$_ = fgets( $fp, 30 );
		if(preg_match("/$auto/i",$_)){
			$auto = explode(",", $_);
			$name = $auto[0];
			if($auto[0] == "F904i"){
				if($_POST["yoko"] == 1){
					$auto[1] = $auto[3];
				}
			}
			$size = $auto[1];
			$auto[0] = ereg_replace("[^0-9]+", "", $auto[0]);
			if($auto[0] <= 600){
				$type = "504";
			}else{
				$type = "toh_900";
			}/*elseif($auto[0] <= 903){
				$type = "sk_903";
			}elseif($auto[0] <= 904){
				$type = "toh_904";
			}elseif($auto[0] <= 905){
				$type = "toh_905";
			}*/
			break;
		}
	}
	fclose($fp);
	if($type == ""){
		echo "指定された機種が無いみたいです。<br>手動設定を行って下さい<br>";
		$type = ""; $board = "";
	}else{
		echo "■機種名：$name<br>";
	}
}else{
	$size = $_POST["size"];
	$type = $_POST["type"];
}
$filepass = ""; $filename = "iMona";
if($type == "u" and $_POST['boardurl'] = "" and $_POST['boardname'] == ""){
	echo "!カスタム設定での必須事項が入力されていません。";
}else{
	if($type == "" or $board == ""){
		echo "!機種名か板種類が選択されていません。選択してください!";
	}else{
		if($size=="auto" or $size == ""){
			$size = "";
		}elseif($size == "cus"){
			$size = htmlspecialchars($_POST["size_cus"]);
			if (preg_match('/^[0-9]{3}x[0-9]{3}$/', $size)) {
				
			} else {
				echo "正しくないサイズ指定です";
				exit;
			}
			#$drawarea = "\nDrawArea = ".$size."\n";
		}else{
			#$drawarea = "\nDrawArea = ".$size."\n";
			$size = htmlspecialchars($size);
			if (preg_match('/^[0-9]{3}x[0-9]{3}$/', $size)) {
				
			} else {
				echo "正しくないサイズ指定です";
				exit;
			}
		}
		switch ($type) {
			case "504":
				$filepass = "./504/";
				$type = "iMona@504gzip";
				break;
			case "sk_900":
				$filepass = "./903_sk/";
				$type = "iMona@900";
				break;
			case "toh_904":
				/*$filepass = "./904_toh/";
				$type = "iMona@東鳩904";
				break;*/
			case "toh_905":
				/*$filepass = "./905_toh/";
				$type = "iMona@東鳩905";
				break;*/
			case "toh_900":
				$filepass = "./900_toh/";
				$type = "iMona@東鳩900";
				break;
		}
		

		$data = file_get_contents($filepass."iMona.jam");
		$data = str_replace("<size>",$size,$data);
		$data = str_replace("<boardurl>",$boardurl,$data);
		$data = str_replace("<board>",$board,$data);
		$data = $data.$drawarea;
		$fp = fopen($filepass.$filename."_$size.jam","w");
		fwrite($fp,$data);
		fclose($fp);
		echo "■板種類：$board<br>
	■画面ｻｲｽﾞ：$size<br>
	■機種名：$type<br>";
		echo "<OBJECT declare id=\"imona\" data=\"".$filepass.$filename."_$size.jam"."\" type=\"application/x-jam\"></OBJECT><A ijam='#imona' href='index.php'>ダウンロード</A><br>";
	}
}
?>
<hr size="1">
■設定<br>
板種類(URL)：<?php echo $board ?>


</select>
<br>
おまかせ設定(例：P903iTV)：<input name="auto" autocomplete="off"><br>
<input type="checkbox" name="yoko" value="1">横表示(F904iのみ)
<br>
おまかせ設定をした方は下は何も変更する必要はありません。
<br>
画面ｻｲｽﾞ(判らない人は自動設定を指定)：<br>
<select name="size">
<option value="auto" selected>自動設定</option>
<option value="cus">カスタム</option>
<option value="240x240">240x240</option>
<option value="240x320">240x320</option>
<option value="240x320">240x368</option>
<option value="480x480">480x480</option>
<option value="480x640">480x640</option>
<option value="480x854">480x854</option>
<option value="480x864">480x864</option>
<option value="480x704">480x704</option>
</select>
<br>
ｱﾌﾟﾘ種類：<select name="type">
<option value="504" selected>iMona@504gzip</option>
<option value="toh_900">iMona@東鳩900</option>
<option value="sk_900">iMona@作者900</option>
</select>
<br>
画面ｻｲｽﾞ指定(例:480x480)：<br>
画面ｻｲｽﾞでｶｽﾀﾑを選択した時のみ入力して下さい。<br>
<input name="size_cus" autocomplete="off">
<br>
<input type="submit" value="送信">
</form>
<a href="../">トップへ戻る</a>
</body>
</html>