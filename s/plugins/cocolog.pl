

package cocolog;

use Time::Local;
#use Encode qw/from_to/; 
use Jcode;

BEGIN {	#初回起動時のみ
	## 設定 ###########################################################################################
	do 'setting.pl';
	$ua = 'Monazilla/1.00 (iMona/1.0)';	#USER-AGENT
	###################################################################################################

	if(exists $ENV{MOD_PERL}){	#mod_perlで動作しているとき
		require 'http.pl';
	}
}

sub read {
	$/ = "\x0A";	#改行コードを\x0A(LF)にする。

	$rbbs = $_[0], $rth = $_[1], $rst = $_[2], $rto = $_[3], $rls = $_[4], $rop = $_[5];
	$buffer ='';
	$buffer2 ='';
	$tmp ='';
	@data = ();
	@options = ();

	if($rst > $rto){	#startの方がtoよりも大きい場合
		$rto = 1024;	#最後まで読む
	}

	@options = split( /,/, $rop);

	$rhost = "http://$options[0].cocolog-nifty.com";

	if(!exists $ENV{MOD_PERL}){	#!mod_perl
		require 'http.pl';
	}

	$http'ua = $ua;
	$http'range = 0;
	$http'other = '';

	if($rth == 0){	#スレ一覧(エントリ一覧)の読み込み
		$str = &http'get("$rhost/blog/index.rdf");		#ダウンロード
		#from_to($str , 'utf8', 'shiftjis');		#utf-8 to shiftjis
		Jcode::convert(\$str , 'sjis', 'utf8');
		#print "[[$rhost/blog/index.rdf $str]]";

		while($str =~ s/(<item rdf:.+?<\/item>)//s){
			$buffer = $1;

			$buffer =~ m/<dc:date>(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)\+(\d+):(\d+)<\/dc:date>/;
			$time = timelocal($6,$5,$4,$3,$2 - 1,$1);	#とりあえず日本時間と仮定する。

			$buffer =~ m/<title>(.+?)<\/title>/;

			push(@data, "$time.dat<>$1 (1)");
		}
	} else {		#レス(本文)の読み込み
		$str = &http'get("$rhost/blog/index.rdf");		#ダウンロード
		#from_to($str , 'utf8', 'shiftjis');		#utf-8 to shiftjis
		Jcode::convert(\$str , 'sjis', 'utf8');

		while($str =~ s/(<item rdf:.+?<\/item>)//s){
			$buffer = $1;

			$buffer =~ m/<dc:date>(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)\+(\d+):(\d+)<\/dc:date>/;
			$time = timelocal($6,$5,$4,$3,$2 - 1,$1);	#とりあえず日本時間と仮定する。

			if($time == $rth){
				$buffer2 = '';
				
				$buffer =~ m/<title>(.+?)<\/title>/;
				$title = $1;
				$buffer2 .= "$title<br>";

				$buffer =~ m/<content:encoded>(.+?)<\/content:encoded>/s;
				$tmp = $1;
				$tmp =~ s/<\!\[CDATA\[//g;
				$tmp =~ s/]]>//g;
				$tmp =~ s/<br \/>/<br>/g;
				$tmp =~ s/<\/?p>//g;
				$tmp =~ s/[\r\n]//g;
				$buffer2 .= "$tmp<br><br>";
				
				$buffer =~ m/<dc:creator>(.+?)<\/dc:creator>/;
				$buffer2 .= "$1<br>";

				$buffer =~ m/<dc:date>(.+?)<\/dc:date>/;
				$buffer2 .= "$1<>$title";

				push(@data, $buffer2);
			}
		}
	}

	#ヘッダの出力
	$logs = @data;
	unshift(@data, "Res:1-$logs/$logs");
	
	return join("\n", @data);
}

1;