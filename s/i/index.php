<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<title>iMona for i�A�v���_�E�����[�h��</title>
</head>
<body>
<p>
iMona(i����)�̃_�E�����[�h<br>
<?php include "../../ad.php"; ?>
<hr size="1">
<form action="index.php" method="post" enctype="multipart/form-data">
<?php
/*if( preg_match( "/DoCoMo\/1\.0\/([0-9a-zA-Z]+?)(\/)?/i", $_SERVER['HTTP_USER_AGENT'], $_) ){

}elseif( preg_match( "/DoCoMo/2.0[^0-9a-zA-Z]+([0-9a-zA-Z]+?)/i", $_SERVER['HTTP_USER_AGENT'], $_ ) ){
}*/
$boardurl = "http://s.imona.info/";
$board = "@��Ҕ�";
$filename = $filename."_s";
//docomo�̋@��̉�ʃT�C�Y�̃f�[�^
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
		echo "�w�肳�ꂽ�@�킪�����݂����ł��B<br>�蓮�ݒ���s���ĉ�����<br>";
		$type = ""; $board = "";
	}else{
		echo "���@�햼�F$name<br>";
	}
}else{
	$size = $_POST["size"];
	$type = $_POST["type"];
}
$filepass = ""; $filename = "iMona";
if($type == "u" and $_POST['boardurl'] = "" and $_POST['boardname'] == ""){
	echo "!�J�X�^���ݒ�ł̕K�{���������͂���Ă��܂���B";
}else{
	if($type == "" or $board == ""){
		echo "!�@�햼����ނ��I������Ă��܂���B�I�����Ă�������!";
	}else{
		if($size=="auto" or $size == ""){
			$size = "";
		}elseif($size == "cus"){
			$size = htmlspecialchars($_POST["size_cus"]);
			if (preg_match('/^[0-9]{3}x[0-9]{3}$/', $size)) {
				
			} else {
				echo "�������Ȃ��T�C�Y�w��ł�";
				exit;
			}
			#$drawarea = "\nDrawArea = ".$size."\n";
		}else{
			#$drawarea = "\nDrawArea = ".$size."\n";
			$size = htmlspecialchars($size);
			if (preg_match('/^[0-9]{3}x[0-9]{3}$/', $size)) {
				
			} else {
				echo "�������Ȃ��T�C�Y�w��ł�";
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
				$type = "iMona@����904";
				break;*/
			case "toh_905":
				/*$filepass = "./905_toh/";
				$type = "iMona@����905";
				break;*/
			case "toh_900":
				$filepass = "./900_toh/";
				$type = "iMona@����900";
				break;
		}
		

		$data = file_get_contents($filepass."iMona.jam");
		$data = str_replace("<size>",$size,$data);
		$data = str_replace("<boardurl>",$boardurl,$data);
		$data = $data.$drawarea;
		$fp = fopen($filepass.$filename."_$size.jam","w");
		fwrite($fp,$data);
		fclose($fp);
		echo "����ށF$board<br>
	����ʻ��ށF$size<br>
	���@�햼�F$type<br>";
		echo "<OBJECT declare id=\"imona\" data=\"".$filepass.$filename."_$size.jam"."\" type=\"application/x-jam\"></OBJECT><A ijam='#imona' href='index.php'>�_�E�����[�h</A><br>";
	}
}
?>
<hr size="1">
���ݒ�<br>
���(URL)�F<?php echo $board ?>


</select>
<br>
���܂����ݒ�(��FP903iTV)�F<input name="auto" autocomplete="off"><br>
<input type="checkbox" name="yoko" value="1">���\��(F904i�̂�)
<br>
���܂����ݒ���������͉��͉����ύX����K�v�͂���܂���B
<br>
��ʻ���(����Ȃ��l�͎����ݒ���w��)�F<br>
<select name="size">
<option value="auto" selected>�����ݒ�</option>
<option value="cus">�J�X�^��</option>
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
���؎�ށF<select name="type">
<option value="504" selected>iMona@504gzip</option>
<option value="toh_900">iMona@����900</option>
<option value="sk_900">iMona@���900</option>
</select>
<br>
��ʻ��ގw��(��:480x480)�F<br>
��ʻ��ނŶ��т�I���������̂ݓ��͂��ĉ������B<br>
<input name="size_cus" autocomplete="off">
<br>
<input type="submit" value="���M">
</form>
<a href="../">�g�b�v�֖߂�</a>
</body>
</html>