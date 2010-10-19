

package typepad;

use Time::Local;

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

	#$rhost = "http://$options[0].cocolog-nifty.com";
	$rhost = $options[0];

	if($rhost =~ /^http:/){
		$rurl = $rhost;
	} else {
		$rurl = "http://$rhost";
	}

	#後ろに何もついていない時はindex.rdfをつける
	if($rurl !~ /rss\.php/ && $rurl !~ /\.rdf$/ && $rurl !~ /(\.html?|[0-9]+)$/){
		if($rurl !~ /\/$/){
			$rurl .= '/';
		}
		$rurl .= 'index.rdf';
	}

	if(!exists $ENV{MOD_PERL}){	#!mod_perl
		require 'http.pl';
	}

	$http'ua = $ua;
	$http'range = 0;
	$http'other = '';

	if($rurl =~ /(\.html?|[0-9]+)$/){
		$str = &http'get("$rurl");		#ダウンロード
		&tosjis($str);
		&getcontents($str);
		$outmode = ' WEB';
	} else {
		if($rth == 0){	#スレ一覧(エントリ一覧)の読み込み
			
			$str = &http'get("$rurl");		#ダウンロード
			&tosjis($str);

			while($str =~ s/(<item rdf:about="?(.+?)"?>.+?<\/item>)//s){
				$resnum++;
				if($rls == 0 && $resnum < $rst){next;}
				if($rls == 0 && $resnum > $rto){last;}
				if($rls != 0 && $rls < $resnum){last;}
				
				$buffer = $1;
				$link = $2;

				if($link =~ /rss\.php/){
					$buffer =~ m/<link>(.+?)<\/link>/;
					$link = $1;
				}

				if($link =~ s|^http://||){
					$link =~ s/$rhost/\./;
				}

				$buffer =~ m/<dc:date>(.+?)<\/dc:date>/;
				#$time = gettime($1);

				$buffer =~ m/<title>(.+?)<\/title>/;
				$title = $1;

				#push(@data, "$time.dat<>$1 (0)");
				push(@data, "10.dat<>$title (0)<>$link");
				if($outst == -1){$outst = $resnum;}	$outto = $resnum;
			}
			$outmode = ' LIST';
		} else {		#レス(本文)の読み込み
			$str = &http'get("$rurl");		#ダウンロード
			&tosjis($str);

			while($str =~ s/(<item rdf:.+?<\/item>)//s){
				$buffer = $1;

				$buffer =~ m/<dc:date>(.+?)<\/dc:date>/;
				$time = gettime($1);

				if($time == $rth){
					$buffer2 = '';
					
					$buffer =~ m/<title>(.+?)<\/title>/;
					$title = $1;
					$buffer2 .= "$title<br>";

					$buffer =~ m/<content:encoded>(.+?)<\/content:encoded>/s;
					$tmp = $1;
					&filter($tmp);
					$buffer2 .= "$tmp<br><br>";
					
					$buffer =~ m/<dc:creator>(.+?)<\/dc:creator>/;
					$buffer2 .= "$1<br>";

					$buffer =~ m/<dc:date>(.+?)<\/dc:date>/;
					$buffer2 .= "$1<>$title";

					push(@data, $buffer2);
					if($outst == -1){$outst = $resnum;}	$outto = $resnum;
				}
			}
			$outmode = ' WEB';
		}
	}

	#ヘッダの出力
	#$logs = @data;
	unshift(@data, "Res:$outst-$outto/$outto$outmode");
	#print @data;

	return \@data;
}

sub getcontents {
	my $tmp = $_[0], $tmp2;
	my $title;
	my $out;

	if($tmp =~ m%<h3[^>]*>(.+?)</h3>(.+?)<(p|span) class="posted">%s){	# 汎用(typepad)
		$out = "";
		
		$title = $1;
		$tmp2 = $2;

		$resnum++;

		if($rls > 0 || $resnum >= $rst){
			&filter($tmp2);
			&filter($title);
			$out = "$title<br><br>" . $tmp2 . "<>$title";
			push(@data, $out);
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
		}

		while($tmp =~ s%<a id="[a-zA-Z]\d+"></a>(.+?<(\w+)\s.*?\s?class="posted">.+?</\2>)%%s || $tmp =~ s|<div class="comments-body">(.+?)(<div class="comments-body">)|$2|s){
			if($1 =~ /<form method="post"/){next;}

			$tmp2 = $1;

			$resnum++;
			if($rls <= 0 && $resnum < $rst){next;}
			if($rls <= 0 && $resnum > $rto){last;}

			&filter($tmp2);
			#print "$tmp2";
			push(@data, $tmp2);
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
		}
	} elsif($tmp =~ m%entry_title%s){	# 汎用(blosxom)
		while(($resnum == 0 && $tmp =~ s%<[^>]+"entry_title"[^>]*>(.+?)</[^>]+>(.+?)<\w+\s[^>]*?\s?class="posted"[^>]*>%%s) || $tmp =~ s%<a id="([a-zA-Z]\d+)"></a>(.+?<(\w+)\s[^>]*?\s?class="posted">.+?</\3>)%%s){
			if($2 =~ /<form method="post"/){next;}

			$title = $1;
			$tmp2 = $2;

			$resnum++;
			if($rls <= 0 && $resnum < $rst){next;}
			if($rls <= 0 && $resnum > $rto){last;}

			&filter($tmp2);
			if($resnum == 1){
				&filter($title);
				$tmp2 = "$title<br><br>" . $tmp2 . "<>$title";
			}

			push(@data, $tmp2);
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
		}
	} elsif($tmp =~ m%<!--article-->%s){	#ameblo
		while(($resnum == 0 && $tmp =~ s%<!--article-->(.+?)<!--[\r\n]+<rdf:RDF.+?-->%%s) || $tmp =~ s%<a id="[a-z0-9]{5,100}"></a>(.+?<(\w+)\s[^>]*?\s?class="comment_date">.+?</\2>)%%s){
			if($2 =~ /<form method="post"/){next;}

			$tmp2 = $1;
			if($tmp2 =~ m%"((article_)?title|comment_title)">(.+?)</%){
				$title = $3;
			}

			$resnum++;
			if($rls <= 0 && $resnum < $rst){next;}
			if($rls <= 0 && $resnum > $rto){last;}

			&filter($tmp2);
			if($resnum == 1){
				&filter($title);
				$tmp2 = "$title<br><br>" . $tmp2 . "<>$title";
			}

			push(@data, $tmp2);
			if($outst == -1){$outst = $resnum;}	$outto = $resnum;
		}
	}
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

sub gettime {
	$_[0] =~ m/(\d+)-(\d+)-(\d+)T(\d+):(\d+):(\d+)\+(\d+):(\d+)/;
	return timelocal($6,$5,$4,$3,$2 - 1,$1);	#とりあえず日本時間と仮定する。
}

1;