# RSS Reader for iMona

package rss;

BEGIN {	#初回起動時のみ
	## 設定 ###########################################################################################
	do 'setting.pl';
	$ua = 'Monazilla/1.00 (iMona/1.0)';	#USER-AGENT
	$encode = 1;	#0:Encode 1:Jcode
	###################################################################################################

	if(exists $ENV{MOD_PERL}){	#mod_perlで動作しているとき
		require 'http.pl';
	}
}

sub read {

	if($encode == 0){
		require Encode;# qw/ from_to /;
		require Encode::Guess;# qw/ utf8 euc-jp shiftjis 7bit-jis /;
	} else {
		require Jcode;
	}
	#require "jcode.pl";
	
	$/ = "\x0A";	#改行コードを\x0A(LF)にする。

	$rbbs = $_[0], $rth = $_[1], $rst = $_[2], $rto = $_[3], $rls = $_[4], $rop = $_[5];
	$buffer ='', $buffer2 ='', $outmode = '';
	$rurl = '';
	$tmp ='';
	$resnum = 0;
	$outst = -1, $outto = 0;
	@data = ();
	@options = ();

	if($rst > $rto){	#startの方がtoよりも大きい場合
		$rto = 1024;	#最後まで読む
	}

	@options = split( /,/, $rop);

	$rhost = $options[0];

	if($rhost =~ /^http:/){
		$rurl = $rhost;
	} else {
		$rurl = "http://$rhost";
	}

	#最後が/の時はindex.rdfをつける
	if($rurl =~ /\/$/){
		$rurl .= 'index.rdf';
	}

	if(!exists $ENV{MOD_PERL}){	#!mod_perl
		require 'http.pl';
	}

	$http'ua = $ua;
	$http'range = 0;
	$http'other = '';

	if($rth == 1){	#板情報の読み込み
		push(@data, "[RSS Reader For iMona]<br>このプラグインはRSSをiMonaで閲覧するための物です。<br>使い方はブックマークのその他のデータにRSSのURLを入れて下さい。<br>スレ番号を2にすると、RSSの全てのデータを一度に読むことができます。<>RSSREADER");

		$outst = 1;
		$outto = @data;
		$outmode = ' WEB(1)';	# 自動しおり機能を使用しない
	} elsif($rth == 0){	#スレ一覧(エントリ一覧)の読み込み
		
		$str = &http'get("$rurl");		#ダウンロード
		&tosjis($str);

		while($str =~ s/(<item( rdf:about="?(.+?)"?)?>.+?<\/item>)//s){
			$resnum++;
			if($rls == 0 && $resnum < $rst){next;}
			if($rls == 0 && $resnum > $rto){last;}
			if($rls != 0 && $rls < $resnum){last;}
			
			$buffer = $1;

			if($buffer =~ m/<title>(.+?)<\/title>/){
				$title = $1;
			} else {
				$title = "no titile";
			}

			push(@data, "10.dat<>$title (0)");
			
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
		}
		$outmode = ' LIST';
	} elsif($rth == 2){	#レス(本文)の読み込み(全て)
		$str = &http'get("$rurl");		#ダウンロード
		&tosjis($str);

		while($str =~ s/(<item( rdf:about="?(.+?)"?)?>.+?<\/item>)//s){
			$resnum++;
			if($rls == 0 && $resnum < $rst){next;}
			if($rls == 0 && $resnum > $rto){last;}
			if($rls != 0 && $rls < $resnum){last;}

			$buffer = $1;

			if($resnum == 1){
				push(@data, &createcontents($buffer, 1));
			} else {
				push(@data, &createcontents($buffer));
			}
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
		}
		$outmode = ' WEB(1)';	# 自動しおり機能を使用しない
	} else {		#レス(本文)の読み込み
		$str = &http'get("$rurl");		#ダウンロード
		&tosjis($str);

		while($str =~ s/(<item( rdf:about="?(.+?)"?)?>.+?<\/item>)//s){
			$resnum++;

			$buffer = $1;

			if($resnum == ($rth - 10)){
				push(@data, &createcontents($buffer, 1));
				if($outst == -1){$outst = $resnum;}	$outto = $resnum;
				last;
			}
		}
		$outmode = ' WEB(1)';	# 自動しおり機能を使用しない
	}

	if(@data == 0){
		return &puterror(404);
	} else {
		#ヘッダの出力
		unshift(@data, "Res:$outst-$outto/$outto$outmode");
	}

	return \@data;
}

#引数: buffer, printtitle
sub createcontents {
	my $tmp, $tmp2,$title;

	$tmp = "";
	
	if($_[0] =~ m/<title>(.+?)<\/title>/){
		$title = $1;
	} else {
		$title = "no titile";
	}
	$tmp .= "$title<br>";

	if($_[0] =~ m/<content:encoded>(.+?)<\/content:encoded>/s){
		$tmp2 = $1;
		&filter($tmp2);
		$tmp .= "$tmp2<br><br>";
	} elsif($_[0] =~ m/<description>(.+?)<\/description>/){
		$tmp2 = $1;
		&filter($tmp2);
		$tmp .= "$tmp2<br><br>";
	} else {
		$tmp .= "no contents<br><br>";
	}
	
	if($_[0] =~ m/<dc:creator>(.+?)<\/dc:creator>/){
		$tmp .= "$1<br>";
	}

	if($_[0] =~ m/<dc:date>(.+?)<\/dc:date>/){
		$tmp2 = $1;
		&filter($tmp2);
	} else {
		$tmp2 = "";
	}

	if($_[1] == 0){	#タイトルの出力
		$tmp .= "$tmp2";
	} else {
		$tmp .= "$tmp2<>$title";
	}

	return $tmp;
}

sub filter {
	$_[0] =~ s/<\!\[CDATA\[//g;		#<![CDATA[
	$_[0] =~ s/]]>//g;				#]]>
	$_[0] =~ s/<br \/>/<br>/g;		#<br />
	$_[0] =~ s/<\/?p(|\s+[^>]*)>//g;			#<p>asdf</p>
	$_[0] =~ s/<>//g;				#<>
	$_[0] =~ s/<\/?div>//g;			#<div> </div>
	$_[0] =~ s/<\/br>/<br>/g;		#</br>
	$_[0] =~ s/<a [^>]+?><\/a>//g;	#more
	$_[0] =~ s%<(span)[^>]*?>(.+?)</\1>%$2<br>%gs;	#tag
	$_[0] =~ s%<(strong)[^>]*?>(.+?)</\1>%$2%gs;	#tag
	$_[0] =~ s%&#24180;%年%;
	$_[0] =~ s%&#26376;%月%;
	$_[0] =~ s%&#26085;%日%;
	$_[0] =~ s/&raquo;/≫/g;		#>>
	$_[0] =~ s/[\r\n]//g;
	$_[0] =~ s/<a name=".+?">(.+?)<\/a>/$1 /g;	#name
}

sub tosjis {
	if($encode == 0){
		my $enc = Encode::Guess::guess_encoding($_[0]);
		Encode::from_to($_[0] , $enc->name, 'shiftjis');		#utf-8 to shiftjis
	} else {
		Jcode::convert(\$_[0] , 'sjis');
	}
}

sub puterror {	#エラーの出力
	# エラーコード
	#400 何らかのエラー
	#401
	#402
	#403 dat落ち
	#404 nodata
	#405 読み込みスタートの位置よりもデータが少ない
	#406 新レスが無い

	my @tmp;
	$tmp[0] = "ERR - $_[0]";
	return \@tmp;
}


1;