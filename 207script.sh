#!/bin/sh

#PERL
#/usr/local/bin/perl /virtual/zuzu/public_html/imona/makebrdflex.cgi
#/usr/local/bin/perl /virtual/zuzu/public_html/imonabbs/makebrdflex.cgi
#
#find /virtual/zuzu/public_html/imona/ -name "sure.dat" -type f -atime +1 -exec rm {} \;
#find /virtual/zuzu/public_html/imona-zuzu.xrea.jp/dat/ -size +30000c -type f -exec rm {} \;
find /var/www/html/dat/ -type f -atime +0 -exec rm {} \;
#find /virtual/zuzu/public_html/imonabbs/imona-zuzu.xrea.jp/dat/ -type f -atime +1 -exec rm {} \;
#find /virtual/zuzu/public_html/imona/imona-zuzu.xrea.jp/dat/ -type d -atime +1 -exec rmdir {} \;

#iMona
/usr/bin/php /var/www/html/o/brd_make.php
/usr/bin/php /var/www/html/b/brd_make.php
#/usr/local/bin/perl /var/www/html/s/makebrdflex.pl

exit