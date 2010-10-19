

package tenki;

use Time::Local;

BEGIN {	#初回起動時のみ
	## 設定 ###########################################################################################
	do 'setting.pl';
	$ua = 'Monazilla/1.00 (iMona/1.0)';	#USER-AGENT
	$encode = 1;	#0:Encode 1:Jcode
	$dir = "$dat/public_html/tenki";
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

	$rurl = "http://web1.aaacafe.ne.jp/~tenki/tenki.xml";

	if(!exists $ENV{MOD_PERL}){	#!mod_perl
		require 'http.pl';
	}

	$http'ua = $ua;
	$http'range = 0;
	$http'other = '';

	$str = &loadcache(60 * 60 * 24);

	if($str eq ""){
		$str = &http'get("$rurl");		#ダウンロード
		&tosjis($str);

		&savecache($str);
	}

	if($rth == 0){	#スレ一覧(エントリ一覧)の読み込み

		while($str =~ s/(<item>.+?<\/item>)//s){
			$resnum++;
			if($rls == 0 && $resnum < $rst){next;}
			if($rls == 0 && $resnum > $rto){last;}
			if($rls != 0 && $rls < $resnum){last;}

			$buffer = $1;

			$buffer =~ m/<title>(.+?)<\/title>/;
			$title = $1;

			push(@data, (10+$resnum) . ".dat<>$titleの天気 (0)");
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
			#$tmp .= "<a href=\"##$resnum#1\">$resnum</a>:$title<br>";
		}
		$outmode = ' LIST';
	} else {		#レス(本文)の読み込み

		while($str =~ s/(<item>.+?<\/item>)//s){
			$resnum++;

			$buffer = $1;

			if($resnum == ($rth - 10)){
				$buffer =~ m/<description>(.+?)<\/description>/;
				$tmp = $1;
				&filter($tmp);
				last;
			}
		}

		$outst = 1;	$outto = 1;
		push(@data, $tmp);
		$outmode = ' WEB(1)';	# 自動しおり機能を使用しない
	}

	#ヘッダの出力
	unshift(@data, "Res:$outst-$outto/$outto$outmode");

	return \@data;
}

sub filter {
	$_[0] =~ s/&lt;/</g;			#<
	$_[0] =~ s/&gt;/>/g;			#>
	$_[0] =~ s/&raquo;/≫/g;		#>>
	$_[0] =~ s%&#24180;%年%;
	$_[0] =~ s%&#26376;%月%;
	$_[0] =~ s%&#26085;%日%;
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

sub loadcache {
	my $time, $data;
	if(open(DATA, "$dir/cache.txt")){
		binmode(DATA);

		$time = <DATA>;
		chomp($time);
		if(time - $time > $_[0]){
			close(DATA);
		} else {
			while(<DATA>){$data .= $_;}
			close(DATA);
			return $data;
		}
	}
	return "";
}

sub savecache {
	if(!-e "$dir"){mkdir("$dir", $dirpermission);}	#ディレクトリがなければ作成する

	if(open(DATA, "> $dir/cache.txt")){
		binmode(DATA);

		print DATA time . "\n";
		print DATA $_[0];
		close(DATA);
	}
}


1;