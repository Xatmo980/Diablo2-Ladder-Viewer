<?php
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}
/////////////////////////////////////////////////
//          Options
$ladderType = "Expansion"; // Possible Types: Standard, Hardcore, Expansion, Expansion HC
$GatewayADD = "URL";
$RegFileLink = "URL";
//
/////////////////////////////////////////////////
//          FilePaths
$xmlfile = "Path/var/ladders/d2ladder.xml";
$accountsdir = "Path/var/charinfo";
$xmlfile2 = "Path/var/status/server.xml";
//
/////////////////////////////////////////////////

if(file_exists($xmlfile2)) {
   $xml2 = simplexml_load_file($xmlfile2);
   $days = $xml2->Uptime->Days;
   $hours = $xml2->Uptime->Hours;
   $mins = $xml2->Uptime->Minutes;
   $users = $xml2->Users->Number;
   print "<center><div class=\"Top\"><center>(How to Connect)<br>Gateway: $GatewayADD<br><br>Download and run Reg File <a href=\"$RegFileLink\">Download</a><br></center></div></center><br><br>";
   print "<center><div class=\"Top2\"><center>(Server Uptime)<br> $days(Days) $hours(Hours) $mins(Minutes)<br><br> (Users Online)<br> $users</center></div></center><br><br>";
}

$handle = opendir($accountsdir);

$accounts = array();
while(($file = readdir($handle)) != false) {
	if($file != "." && $file != "..") {
		$handlezwei = opendir("$accountsdir/$file");

		while(($filezwei = readdir($handlezwei)) != false) {
			if($filezwei != "." && $filezwei != "..") {
				$accounts["$filezwei"] = "$file";
			}
		}
	}
}

$anzahl_tote = $anzahl_lebende = 0;

$xml = simplexml_load_file($xmlfile);
print "<center style=\"padding: 20px;\"><table cellpadding=\"2\" cellspacing=\"2\" style=\"width:70%;border:1px solid black;border-radius:10px;background-color:#2C2F33\"";
foreach ($xml->ladder as $ladder) {
         if($ladder->mode  == $ladderType) {
            if($ladder->class == "OverAll")  {

	       print " <tr><colspan=\"7\"><font size=\"6\">$ladderType Ladder</font></th></tr>\n";
	       print " <tr><th>#</th><th>Name</th><th>Level</th><th>Exp</th><th>Class</th><th>Title</th><th>Status</th><th>Logged</th></tr>\n";

           foreach($ladder->char as $char) {
		   $rank = $char->rank;
		   $name = $char->name;
		   $level = $char->level;
		   $exp = $char->experience;
		   $class = $char->class;
		   $prefix = $char->prefix;
		   $status = $char->status;
		   $acc = $accounts[strtolower($name)];
                                 
			
                  if($prefix = " ") {
                        $prefix = "None";
                  }		
		  if($status == "alive") {
		        $status = "<font color=\"green\">Alive</font>";
			$anzahl_lebende++;
		   } else {
			$status = "<font color=\"red\">Dead</font>";
			$anzahl_tote++;
		   }

		   print " <tr><td>$rank</td><td><img src=\"icons/$class.gif\" alt=\"$class\">";
		   if ($acc) {
			print "$name (<small>$acc</small>)";
		   } else {
			print "<s>$name</s> (<small>deleted</small>)";
		   }
                   $online = "<font color=\"red\">Offline</font>";
                   foreach($xml2->Users->user as $uuser) {
                           $uname = $uuser->name;
			   if ($uname = $name) {
                               $online = "<font color=\"green\">Online</font>";
                            }
                   }
		   print "</td><td>$level</td><td>$exp</td><td>$class</td><td>$prefix</td><td>$status</td><td>$online</td></tr>\n";
	   }
      print "</table>\n";
    }
  }
}
  print "<p>Total of <b>" . ($anzahl_tote + $anzahl_lebende) . "</b> characters in the ladder, <b>$anzahl_lebende</b> alive and <b>$anzahl_tote</b> dead.</p></center>";
?>
