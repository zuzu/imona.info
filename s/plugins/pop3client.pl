
package pop3client;

###################################################################################################

#
# pop3client.pl
#
#		このプラグインはPOP3クライアントです。
#		option欄にサーバ、ユーザ名、パスワードを指定します。デリミタは[,]です。
#

###################################################################################################

BEGIN {	#初回起動時のみ
	## 設定 ###########################################################################################
	do 'setting.pl';
	$ua = 'Monazilla/1.00 (iMona/1.0)';	#USER-AGENT
	###################################################################################################

	if(exists $ENV{MOD_PERL}){	#mod_perlで動作しているとき
		require '2c.pl';
	}
}

sub read {
	$/ = "\x0A";	#改行コードを\x0A(LF)にする。

	$rbbs = $_[0], $rth = $_[1], $rst = $_[2], $rto = $_[3], $rls = $_[4], $rop = $_[5], $form = $_[6];
	@buf = ();
	@data = ();
	
	$resnum = 0;
	$outst = -1, $outto = 0;

	if($rth == 1){$rop = "";}
	if($rop ne ""){
		@rop = split(/,/ , $rop);
	} else {
		$form =~ s/^0=//;
		@rop = split(/=/ , $form);

	}

	# POP3サーバ名、ユーザ名、パスワード
	$host = $rop[0];
	$user = $rop[1];
	$pass = $rop[2];

	if($rop ne ""){
	} else {	# フォームデータの処理
		if($rth == 0){	#スレ一覧(エントリ一覧)の読み込み
			# エラー
			return &puterror(404);
		} else {
			# 埋まっていない項目がある場合
			if($host eq "" || $user eq "" || $pass eq "") {
				@data = &printform($host, $user, $pass);
			} else {
				@data = &printlink($host, $user, $pass);
			}
			return \@data;
		}
	}

	use Socket;     # Socket モジュールを使う

	# 接続
	if(&connect() == 0){
		return &puterror(404);
	}

	if($rth == 0){	#スレ一覧(エントリ一覧)の読み込み
		# メールのリストを取得
		if(&getlist() == 0){
			$outmode = ' LIST';
			$outst = 1;	$outto = 1;

			push(@data, "1.dat<>メールはありません (1)");
		} else {
			# 各メールについてヘッダを取得
			for($i = 0; $i < @mailnum; $i++){
				if(&getheader($mailnum[$i]) == 1){
					$size = $mailsize[$i] % 100000;
					$time = $size * 10000 + $mailnum[$i];

					foreach $str (@buf){
						$str =~ s/[\r\n]//g;
						if($str =~ m/^Subject: ([^\r\n]+)$/i){
							$resnum++;
							if($rls == 0 && $resnum < $rst){next;}
							if($rls == 0 && $resnum > $rto){last;}
							if($rls != 0 && $rls < $resnum){last;}
							push(@data, "$time.dat<>$1 (1)");
							if($outst == -1){$outst = $resnum;}	$outto = $resnum;
						}
					}
					$outmode = ' LIST';
				}
			}
		}
	} else {
		# メールのリストを取得
		if(&getlist() == 0){
			return &puterror(404);
		}
		
		# 目的のメールを取得
		for($i = 0; $i < @mailnum; $i++){
			$size = $mailsize[$i] % 100000;
			# 目的のメールがあった時
			if($size == int($rth / 10000) || $mailnum[$i] == $rth % 10000){
				if(&getmail($mailnum[$i]) == 1){
					$tmp = "";
					if($rst == 1 || $rls > 0){
						# 本文
						$outst = 1;	$outto = 1;
						$outmode = ' WEB(1)';	# 自動しおり機能を使用しない
						
						$header = 1;
						foreach $str (@buf){
							$str =~ s/[\r\n]//g;
							if($header == 1){
								if($str =~ m/^Subject: ([^\r\n]+)$/i){
									$title = $1;
								}
								if($str =~ m/^(From: [^\r\n]+)$/i){
									$tmp2 = $1;
									#$tmp2 =~ s/</&lt;/;	$tmp2 =~ s/>/&gt;/;
									$tmp .= "$tmp2<br><br>";
								}
								if($str eq ""){$header = 0;}
							} else {
								$tmp .= "$str<br>";
							}
						}
						$tmp .= "<>$title";
						push(@data, $tmp);
					}
					
					$tmp = "";
					if($rst != 1){
						# ヘッダ
						if($rls > 1){
							$outst = 1;	$outto = 2;
						} else {
							$outst = 2;	$outto = 2;
						}
						$outmode = ' WEB(1)';	# 自動しおり機能を使用しない

						foreach $str (@buf){
							$str =~ s/[\r\n]//g;
							if($str =~ m/^Subject: ([^\r\n]+)$/i){
								$title = $1;
							}
							if($str eq ""){last;}
							$tmp .= "$str<br>";
						}
						if($rls <= 1){
							$tmp .= "<>$title";
						}
						push(@data, $tmp);
					}
				}
			}
		}
	}

	&quit();
	
	if(@data == 0){
		return &puterror(404);
	} else {
		#ヘッダの出力
		unshift(@data, "Res:$outst-$outto/$outto$outmode");
	}
	return \@data;
}

# 接続
sub connect {
	# プロトコルは POP3 を使う
	$port = getservbyname('pop3', 'tcp') || 110;

	# ホスト名を、IPアドレスの構造体に変換
	$iaddr = inet_aton($host) || return 0;

	# port と IP アドレスをまとめて構造体に変換
	$sock_addr = pack_sockaddr_in($port, $iaddr);

	# ソケット生成
	socket(SOCKET, PF_INET, SOCK_STREAM, 0) || return 0;

	connect(SOCKET, $sock_addr) || return 0;

	# ファイルハンドルSOCKETをバッファリングしない
	select(SOCKET); $|=1; select(STDOUT);


	# POP3サーバにユーザ名とパスワードを送る
	print SOCKET "USER $user\r\n";
	if(&checkerror() == 0){return 0;}
	print SOCKET "PASS $pass\r\n";
	if(&checkerror() == 0){return 0;}

	return 1;
}

# メールリストを取得
sub getlist {

	@mailnum = ();
	@mailsize = ();
	
	print SOCKET "LIST\r\n";

	while (<SOCKET>){
		# 「.」のみの行が送られてきたらループを抜ける
		m/^\.\r?\n?$/ && last;

		# 「メール番号 バイト数」という行なら
		if ( m/^(\d+) (\d+)/ ){
			$num = $1;
			$size = $2;
			# メール番号を push
			push(@mailnum, $num);
			push(@mailsize, $size);
		}
	}

	if(@mailnum > 0) {return 1;} else {return 0;}
}

# ヘッダの取得
sub getheader {
	# メールの内容を送信するようリクエストを送る
	print SOCKET "TOP $_[0] 0\r\n";

	@buf = ();
	while (<SOCKET>){
		# 「.」のみの行が送られてきたらメールの終り。
		m/^\.\r?\n?$/ && last;

		push(@buf, $_);
	}

	if(@buf > 0) {return 1;} else {return 0;}
}

# メールを取得
sub getmail {
	# メールの内容を送信するようリクエストを送る
	print SOCKET "RETR $_[0]\r\n";

	@buf = ();
	while (<SOCKET>){
		# 「.」のみの行が送られてきたらメールの終り。
		m/^\.\r\n$/ && last;

		push(@buf, $_);
	}

	if(@buf > 0) {return 1;} else {return 0;}
}

# 終了＆切断
sub quit {
	# サーバとの接続を切る
	print SOCKET "QUIT\r\n";
	&checkerror();
	close (SOCKET);
}

# 受信メッセージをチェック
sub checkerror {
	my $error;

	$error = 0;
	while (<SOCKET>){
		if(m/^\+OK/){$error = 1; last;}
	}
	return $error;
}

sub printform {
	my @data;
	my $host;
	my $user;
	my $pass;

	($host, $user, $pass) = @_;

	@data = ();
	
	#ヘッダの出力
	push(@data, "Res:1-1/1 WEB(1)");# 自動しおり機能を使用しない

	if($host eq ""){$host = "imona.net";}
	if($user eq ""){$user = "user";}
	if($pass eq ""){$pass = "password";}

	push(@data, "  == POP メーラ ==<br> Host:<br> <input value=$host size=15><br> ID:<br> <input value=$user size=12><br> パスワード:<br> <input value=$pass size=12><br><br>   <input type=submit value=%20送信%20><br><>POP メーラ");

	return @data;
}

sub printlink {
	my @data;
	my $host;
	my $user;
	my $pass;

	($host, $user, $pass) = @_;

	@data = ();
	
	#ヘッダの出力
	push(@data, "Res:1-1/1 WEB(1)");# 自動しおり機能を使用しない

	push(@data, "  == POP メーラ ==<br>接続の準備ができました。以下のリンクをクリックして下さい。<br>クリックした後にブックマークに登録することもできます。<br> <a href=##0#1#$host,$user,$pass>[Mail]$user</a><>POP メーラ");

	return @data;
}


1;
