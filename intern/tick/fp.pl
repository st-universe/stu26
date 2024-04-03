#!/usr/bin/perl
use LWP::Simple;
$data = get('finishprocesses.php');
print $data;