#!/bin/sh

find /virtual/imona/public_html/dat/ -type f -atime +0 -exec rm {} \;

/usr/local/bin/perl /virtual/imona/makebrdflex.pl

exit