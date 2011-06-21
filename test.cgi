 #!/usr/bin/perperl

### ここにあなたのスクリプト
print "Content-type: text/html\n\nHello World!\n";

##
## オプションとして、いくつかの目的のため PersistentPerlモジュールを利用
##

# PersistentPerl オブジェクトの作成
use PersistentPerl;
my $pp = PersistentPerl->new;

# PersistentPerlの下で実行されているかどうかを調べる
print "Running under perperl=", $pp->i_am_perperl ? 'yes' : 'no', "\n";

# shutdownハンドラの登録
$pp->add_shutdown_handler(sub { do something here });

# クリーンアップ・ハンドラの登録
$pp->register_cleanup(sub { do something here });

# いくつかのPersistentPerlオプションの設定／取得
$pp->setopt('timeout', 30);
print "maxruns=", $pp->getopt('maxruns'), "\n";