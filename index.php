<?php
  $GA_ACCOUNT = "UA-5269279-10";
  $GA_PIXEL = "ga.php";

  function googleAnalyticsGetImageUrl() {
    global $GA_ACCOUNT, $GA_PIXEL;
    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);

    $referer = $_SERVER["HTTP_REFERER"];
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];

    if (empty($referer)) {
      $referer = "-";
    }
    $url .= "&utmr=" . urlencode($referer);

    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }

    $url .= "&guid=ON";

    return $url;
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta name="description" content="�g�ь���2ch�u���E�UiMona��zuzu���^�c���钆�ԃT�[�o�[�ł��B">
<title>imona@zuzu�I</title>
</head>
<body>
<h1 align="center">imona@zuzu�I</h1>
<hr>
<?php include "ad.php"; ?>
���A��<br>
09/01/10/16:04�E�V�����T�[�o�[�ւƂ������z�����܂����Bi�A�v���̕���<a href="http://imona.info/iapp.php">����</a>����ă_�E�����[�h�ASoftbank�Aau�̕��̓A�h���X�̕ύX�����肢���܂�m(_ _)m<br>
<a href="http://d.hatena.ne.jp/zuzu_sion/archivemobile?word=*[iMona]">���j���[�X</a><br>
<a href="http://jbbs.livedoor.jp/internet/3900/">��BBS</a></p>
���_�E�����[�h<br>
<p><a href="device:jam?http://imona.info/au/iMonaZuzuOAP.jad">au�I�[�v���A�v�����_�E�����[�h</a></p>
<p><a href="http://imona.info/iapp.php">i�A�v�����_�E�����[�h</a></p>
<p/>Softbank<br>
�ʏ��:<a href="http://appget.com/vf/menu/lib/ap/apview/044126.html">iMona@zuzu</a><br>
���C�h�ŁF<a href="http://appget.com/vf/menu/lib/ap/apview/044431.html">iMona@zuzu@W</a><br>
�l��iMona���������č�������m�ł��B��ɊO���f���̏������݂ȂǂɑΉ����Ă��܂��B
<form>�Ǝ���<br>
�����E�Ǘ��҂̎������܂����Ă܂��B�Ⴆ�΁A��v��VIP���������Ă����肵�Ă܂��B
<br>
<textarea name="URL" rows="1">http://o.imona.info/</textarea></form>
<form>BBS��<br>
�����E2ch��BBSMENU�����̂܂܍ڂ��Ă��܂��B<br><textarea name="URL" rows="1">http://b.imona.info/</textarea></form>
<form>��Ҕ�<br>
�����E���̖��̒ʂ��ҎI�̂��̂Ɠ������Ă��܂��B<br><textarea name="URL" rows="1">http://s.imona.info/</textarea></form
</p>
<a href="spec.php">���T�[�o�[����</a><br>
���Ǘ��l<br>
zuzu <br>
��zuzuDj.USc����k4426rEFXw</p>
��<a href="&#109;&#97;ilt&#111;&#58;c&#108;&#111;&#119;&#110;&#46;b&#111;&#121;&#46;z&#117;&#122;u+i&#109;&#111;&#110;a&#64;gma&#105;&#108;&#46;&#99;o&#109;">zuzu�փ��[��</a>
<br>
��<a href="http://imona.k2y.info/saba/">�z�z�T�C�g�ꗗ</a><br>
<?php
  $googleAnalyticsImageUrl = googleAnalyticsGetImageUrl();
?>
<img src="<?= $googleAnalyticsImageUrl ?>" />
</body>
</html>