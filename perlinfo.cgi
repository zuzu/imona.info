#!/usr/local/bin/perl

my $title = "Information about $ENV{'HTTP_HOST'}";
my %mod_list;
$exec = `perl -v`;
$exec =~ s/\n\n/\<br\>\n/g;

print  "Content-type: text/html\n\n",
       "<html><meta http-equiv=Content-Type content=text/html; charset=iso-8859-1>",
       "<title>$title</title><body>\n",
       "<h1>$title</h1><hr>\n",
       "<h2>Version (perl -v):</h2>\n",
       "<p>$exec</p><hr>\n",
       "<h2>Environment values:</h2>\n",
       "<ul>";

foreach my $key( keys %ENV ){
    print "<li>$key: $ENV{$key}</li>\n";
}

print  "</ul><hr>\n",
       "<h2>Installed module list:</h2>\n",
       "<ul>";

&listup($_) for grep {$_ ne '.'} @INC;
print "<li>$_</li>\n" for sort keys %mod_list;

($user, $system, $cuser, $csystem) = times;

print  "</ul><hr>\n",
       "<p>User CPU times:$user / ",
       "System CPU times:$system / ",
       "Child process user CPU times:$cuser / ",
       "Child process system CPU times:$csystem (sec)</p>\n",
       "</body></html>\n"; 
exit;

sub listup {
       my ($base, $path) = @_;
       (my $mod = $path) =~ s!/!::!g;
       opendir DIR, "$base/$path" or return;
       my @node = grep {!/^\.\.?$/} readdir DIR;
       closedir DIR;
       foreach (@node) {
              if (/(.+)\.pm$/) { $mod_list{"$mod$1"} = 1 }
              elsif (-d "$base/$path$_") { listup($base, "$path$_/") }
       }
}
