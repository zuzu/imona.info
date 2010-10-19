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
<meta name="description" content="携帯向け2chブラウザiMonaのzuzuが運営する中間サーバーです。">
<title>imona@zuzu鯖</title>
</head>
<body>
<h1 align="center">imona@zuzu鯖</h1>
<hr>
<?php include "ad.php"; ?>
■連絡<br>
09/01/10/16:04・新しいサーバーへとお引っ越ししました。iアプリの方は<a href="http://imona.info/iapp.php">ここ</a>から再ダウンロード、Softbank、auの方はアドレスの変更をお願いしますm(_ _)m<br>
<a href="http://d.hatena.ne.jp/zuzu_sion/archivemobile?word=*[iMona]">■ニュース</a><br>
<a href="http://jbbs.livedoor.jp/internet/3900/">■BBS</a></p>
■ダウンロード<br>
<p><a href="device:jam?http://imona.info/au/iMonaZuzuOAP.jad">auオープンアプリをダウンロード</a></p>
<p><a href="http://imona.info/iapp.php">iアプリをダウンロード</a></p>
<p/>Softbank<br>
通常版:<a href="http://appget.com/vf/menu/lib/ap/apview/044126.html">iMona@zuzu</a><br>
ワイド版：<a href="http://appget.com/vf/menu/lib/ap/apview/044431.html">iMona@zuzu@W</a><br>
僕がiMonaを改造して作ったモノです。主に外部掲示板の書き込みなどに対応しています。
<form>独自版<br>
特徴・管理者の趣味が入りまくってます。例えば、主要なVIP避難所が入っていたりしてます。
<br>
<textarea name="URL" rows="1">http://o.imona.info/</textarea></form>
<form>BBS版<br>
特徴・2chのBBSMENUをそのまま載せています。<br><textarea name="URL" rows="1">http://b.imona.info/</textarea></form>
<form>作者版<br>
特徴・その名の通り作者鯖のものと同期しています。<br><textarea name="URL" rows="1">http://s.imona.info/</textarea></form
</p>
<a href="spec.php">■サーバー情報へ</a><br>
■管理人<br>
zuzu <br>
◆zuzuDj.USc＆◆k4426rEFXw</p>
■<a href="&#109;&#97;ilt&#111;&#58;c&#108;&#111;&#119;&#110;&#46;b&#111;&#121;&#46;z&#117;&#122;u+i&#109;&#111;&#110;a&#64;gma&#105;&#108;&#46;&#99;o&#109;">zuzuへメール</a>
<br>
■<a href="http://imona.k2y.info/saba/">配布サイト一覧</a><br>
<?php
  $googleAnalyticsImageUrl = googleAnalyticsGetImageUrl();
?>
<img src="<?= $googleAnalyticsImageUrl ?>" />
</body>
</html>
