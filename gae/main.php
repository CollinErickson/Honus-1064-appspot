<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Honus</title>
    <!-- Load jQuery from Google's CDN -->
    <!-- Load jQuery UI CSS  -->
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    
    <!-- Load jQuery JS -->
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <!-- Load jQuery UI Main JS  -->
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    
    <!-- Load SCRIPT.JS which will create datepicker for input field  -->
    <script type="text/javascript"  src="/scripts/script.js"></script>
    
    <link rel="stylesheet" type="text/css"  href="/stylesheets/stylesheet.css" />
</head>

<?php  // FUNCTIONS put at top of file

//echo date('h');echo date('M');date("F j, Y, g:i a");echo "\n".date("    D M j G:i:s T Y");    echo "abc\r\ndef";        
	function foo($arg_1, $arg_2, /* ..., */ $arg_n)
	{
			$away_code = $datescoreboardpagexml -> game[0] -> attributes() -> away_code;
		$home_code = $datescoreboardpagexml -> game[0] -> attributes() -> home_code;
		$selectedgamemasterscoreboard = $datescoreboardpagexml -> game[0];
		$filename = "http://gd2.mlb.com/components/game/mlb/year_{$year}/month_{$month}/day_{$day}/gid_{$year}_{$month}_{$day}_{$away_code}mlb_{$home_code}mlb_1/media/highlights.xml";
		$headlines = array();
		$blurbs = array();
		$urls = array();
		$homepage = file_get_contents($filename);
		if ( substr($homepage,0,23) == "GameDay - 404 Not Found") {
			echo "\nNo highlights yet";
		} else {
			$xml = simplexml_load_string($homepage);
			echo $xml;
			foreach ($xml as $media) {
				array_push($headlines,$media->headline);
				array_push($blurbs,$media->blurb);
				array_push($urls,$media->url);
			}
		}
	
	}
	
	function createGameOnclickURLForJS ( $gototeam , $gotoyear , $gotomonth , $gotoday) {
		$gotodata = array (
			'team' => $gototeam,
			'date' => $gotoyear . $gotomonth . $gotoday
			//'year' => $gotoyear,
			//'month' => $gotomonth,
			//'day' => $gotoday			
		);
		return "window.location.href = '/?" . http_build_query($gotodata) . "'";
	}
	function createGameOnclickURLForJSRelative($gototeam , $gotoyear , $gotomonth , $gotoday, $offset) {
		$gotodatestring = date('Ymd', strtotime($offset .' day', strtotime( $gotoyear.'/'.$gotomonth . '/' . $gotoday )));
		$gotodata = array (
			'team' => $gototeam,
			'date' => $gotodatestring
		);
		return "window.location.href = '/?" . http_build_query($gotodata) . "'";
	}
//echo createGameOnclickURLForJS("CIN","2013","08",'08');
?>


<?php // GET data from URL
    if (isset($_GET['team'])){
        //echo 'team is set<br />';
		//echo $_GET['team'];
		$team = $_GET['team'];
	} else {
        //echo 'team is not set<br />';
	}
    //if (isset($_GET['date']))
        //echo 'date is set<br />';
	//else
        //echo 'date is not set<br />';
?>
<?php // GET data from URL

//$GETdata = array();

//if( isset($_GET['date']) ){
//    $GETdata['date'] = $_GET['date'];
//}
//if( isset($_GET['team']) ){
 //   $GETdata['team'] = $_GET['team'];
//}

$teamslist = array('ARI','ATL','BAL','BOS','CHA','CHN','CIN','CLE','COL','DET','HOU','KCA','LAN','MIA','MIL','MIN','NYA','NYN','OAK','PHI','PIT','SDN','SEA','SFN','SLN','TEX','TOR','WAS');

?>


<?php
	// set date today
	#$gdate =  getdate();
	#echo $gdate['mday'];
	// Use West coast time, in future will just use previous day if before 11 AM maybe, or if no games have started
	date_default_timezone_set('America/Los_Angeles');
	#echo date('d');
	$year = date('Y');
	$month = date('m');
	$day = date('d');
	$yeartoday = date('Y');
	$monthtoday = date('m');
	$daytoday = date('d');
	$team = "MIN";
	// get incoming data if available
	$GETdata = array();
	if( isset($_GET['date']) ){
		$GETdata['date'] = $_GET['date'];
		$date = $_GET['date'];
		$date_parsed = date_parse($date);
		$year = $date_parsed['year'];
		$month = strval($date_parsed['month']); if (strlen($month) == 1) {$month = "0" . $month;};
		$day = strval($date_parsed['day']); if (strlen($day) == 1) {$day = "0" . $day;};
		//echo "The year is ".$year . $month . $day. " got it??"; echo gettype($month);
	}
	if( isset($_GET['team']) ){
		$GETdata['team'] = $_GET['team'];
		$team =  $_GET['team'];
	}
	// moved this chunk above select team box to set default
//echo date('Ymd', strtotime('1 day', strtotime("08/01/2015"))); 
//echo createGameOnclickURLForJSRelative('COL',2015,8,1,-1);
?>

<?php  //moved this higher to get teams earlier
	// get scoreboard as XML
	$dateurl = "http://gd2.mlb.com/components/game/mlb/year_".$year."/month_".$month."/day_".$day."/";
	$datepage = file_get_contents($dateurl);
	$datescoreboardurl = $dateurl."master_scoreboard.xml";
	$datescoreboardpage = file_get_contents($datescoreboardurl);
	$datescoreboardpagexml = simplexml_load_string($datescoreboardpage);

	$selectedgamenumber = 0;
	$iii = 0;
	foreach($datescoreboardpagexml as $a) {
		//if (  (strtoupper($a->attributes()-> home_code) == strtoupper($team)) ) {echo "FOUND IThome!";echo $iii;}
		//if (  (strtoupper($a->attributes()-> away_code) == strtoupper($team)) ) {echo "FOUND ITaway!";echo $iii;}
		if (  (strtoupper($a->attributes()-> away_code) == strtoupper($team)) || (strtoupper($a->attributes()-> home_code) == strtoupper($team)) ) {
			//echo "FOUND ITaway!";
			//echo $iii;
			$selectedgamenumber = $iii;
			$hometeam = $a->attributes()-> home_code;
			$awayteam = $a->attributes()-> away_code;
			$hometeamname = $a->attributes()-> home_team_name;
			$awayteamname = $a->attributes()-> away_team_name;
		}
		//echo  strtoupper($a->attributes()-> home_code); echo $team;
		$iii = $iii + 1;
	}
	
?>

<body>

<table id="toptable"><tr>
	<td>
		<h3 align='center' onclick="<?php echo createGameOnclickURLForJS(3,2013,08,12);?>" >Honus<h1>
	</td>
	<td>
		Team:
	</td>
	<td>
		<select name="team" id="selectteam">
		<?php foreach ($teamslist as $tt) {
			echo '<option value="',$tt,'" '; 
			if (strtoupper($team) == strtoupper($tt)){ echo 'selected="selected"';};
			echo '>',$tt,'</option>';
			} 
		?>
		</select>
	</td>
	<td>
		<table><tr>
			<td>Date:</td> 
			<td><input type="text" id="datepicker"> </td>
			<!--<td>&#8647; </td>
			<td>&#8592;</td>  -->
			<td onclick="<?php echo createGameOnclickURLForJSRelative($team,$year,$month,$day,-2);?>" >&#10096;</td>
			<td onclick="<?php echo createGameOnclickURLForJSRelative($team,$year,$month,$day,-1);?>" >&#10092;</td>
			<td onclick="<?php echo createGameOnclickURLForJS($team,$yeartoday,$monthtoday,$daytoday);?>" >&#10072;</td>
			<td onclick="<?php echo createGameOnclickURLForJSRelative($team,$year,$month,$day,+1);?>" >&#10093;</td>
			<td onclick="<?php echo createGameOnclickURLForJSRelative($team,$year,$month,$day,+2);?>" >&#10097;</td>
			<td><button onclick='goToDatePicked()'> Go! </button></td>
		</tr></table>
	</td>
	<td width="20px"></td>
	<td><h4>
		<?php  $awayteamname . " vs " . $hometeamname ?> 
		<?php echo "<a href='" . $datescoreboardurl . "' style='text-decoration:none;'>" . $awayteamname . " vs " . $hometeamname . "</a>"?>
		
	</h4></td>
</tr></table>
 


<table id="midtable"><tr>
<td>


<?php

	// create table of scores, each row is a game
	echo "<table id='scorestable'>";
	foreach($datescoreboardpagexml as $a) {
		//echo substr($a->attributes()-> away_code , 0,3);
		echo '<tr class="scorestablegame" onclick="' . createGameOnclickURLForJS(  substr($a->attributes()-> away_code   , 0, 3   )     ,$year,$month,$day) .'"><td class="scorestablegametd">';
			echo "<table><tr><td>",$a->attributes()->away_team_name,"</td></tr><tr><td>",$a->attributes()->home_team_name,"</td></tr></table>";
			if (($a->status->attributes()->status)=="Final") {
				echo "</td><td>";
				echo "<table><tr><td>",$a->linescore->r->attributes()->away,"</td></tr><tr><td>",$a->linescore->r->attributes()->home,"</td></tr></table>";
				echo "</td><td>";
				echo "F";
			} elseif (($a->status->attributes()->status)=="In Progress" || ($a->status->attributes()->status)=="Review") {
				echo "</td><td>";
				echo "<table><tr><td>",$a->linescore->r->attributes()->away,"</td></tr><tr><td>",$a->linescore->r->attributes()->home,"</td></tr></table>";
				echo "</td><td>";
				if (($a->status->attributes()->top_inning)=="Y"){echo "&#8593;";} else {echo "&#8595;";}
				echo $a->status->attributes()->inning;
			} elseif (($a->status->attributes()->status)=="Preview") {
				echo "</td><td>";
				echo "<table><tr><td>",$a->away_probable_pitcher->attributes()->last_name,"</td></tr><tr><td>",$a->home_probable_pitcher->attributes()->last_name,"</td></tr></table>";
				echo "</td><td>";
				echo $a->attributes()->time," ET";
			} elseif (($a->status->attributes()->status)=="Postponed") {
				echo "</td><td>";
				//echo "<table><tr><td>",$a->linescore->r->attributes()->away,"</td></tr><tr><td>",$a->linescore->r->attributes()->home,"</td></tr></table>";
				echo "PP";
				echo "</td><td>";
				//echo "PP";
				echo $a->status->attributes()->reason;
			} elseif (($a->status->attributes()->status)=="Pre-Game") {
				echo "</td><td>";
				echo "<table><tr><td>",$a->away_probable_pitcher->attributes()->last_name,"</td></tr><tr><td>",$a->home_probable_pitcher->attributes()->last_name,"</td></tr></table>";
				echo "</td><td>";
				echo $a->attributes()->time," ET";
			} else {
				echo "Game status unknown";
			}
		echo "</td></tr>";
	}
	echo "</table>";
?>

<?php	
	$away_code = $datescoreboardpagexml -> game[$selectedgamenumber] -> attributes() -> away_code;
	$home_code = $datescoreboardpagexml -> game[$selectedgamenumber] -> attributes() -> home_code;
	//echo $away_name_brev;
	//echo "before?<p>";
	//echo print_r($datescoreboardpagexml -> game[0] -> attributes()  );
	//echo "</p>sworking?";
	$selectedgamemasterscoreboard = $datescoreboardpagexml -> game[0];
	//echo $selectedgamemasterscoreboard -> attributes() -> home_team_name;
	//foreach($datescoreboardpagexml as $ii) {echo $ii;echo " ";};
	//echo "before hilites";
	//$filename = "http://gd2.mlb.com/components/game/mlb/year_2015/month_06/day_14/gid_2015_06_14_lanmlb_sdnmlb_1/media/highlights.xml";
	#$addif2016 = "";
	#if ($year >= 2016) {
	#	$addif2016 = "_es";
	#}
	#$filename = "http://gd2.mlb.com/components/game/mlb/year_{$year}/month_{$month}/day_{$day}/gid_{$year}_{$month}_{$day}_{$away_code}mlb_{$home_code}mlb_1/media/highlights{$addif2016}.xml";
	# Switching to get highlights from mobile.xml
	$filename = "http://gd2.mlb.com/components/game/mlb/year_{$year}/month_{$month}/day_{$day}/gid_{$year}_{$month}_{$day}_{$away_code}mlb_{$home_code}mlb_1/media/mobile.xml";
	//echo $filename;
	$headlines = array();
	$blurbs = array();
	$urls = array();
	$homepage = file_get_contents($filename);
	//echo $homepage;echo 12;
	//echo substr($homepage,0,23);
	if ( substr($homepage,0,23) == "GameDay - 404 Not Found") {
		echo "\nNo highlights yet";
	} else {
		$xml = simplexml_load_string($homepage);
		echo $xml;
		foreach ($xml as $media) {
			array_push($headlines,$media->headline);
			array_push($blurbs,$media->blurb);
			//array_push($urls,$media->url[3]);
			
			// The following section finds the mp4 url if possible since it is listed first in 2016 but was 4th in 2015.
			$someurladded = 0;
			foreach($media->url as $books) { // books is the url tag because I copied it from a books example
				if ($books->attributes()[0] =="FLASH_1200K_640X360") {  // If the attribute value is this, it adds it
					array_push($urls,$books);
					$someurladded = $someurladded + 1; // keep of track whether or not one has been added
				}
			} 
			if ($someurladded == 0) { // if it didn't find the mp4, just take the first one
				array_push($urls,$media->url[0]);
				echo 'Error 987235 no mp4 found';
			}
			if ($someurladded >1) {
				echo 'Error 3873259 added more than one URL';
			}
		}
	}
	

?>

</td>
 <td>

<table>
	<tr>
		<td><table id="headlinestable">
			<?php
				for ($iii=0; $iii<count($headlines); $iii++) {
					$headline = $headlines[$iii];
					$url = $urls[$iii];
					echo "<tr class='headlinestabletr'><td id='headline",$iii,"' class='headlinestabletd' 
					onclick='document.getElementById(\"videoplayer\").setAttribute(\"src\", \"",$url,"\");document.getElementById(\"videoplayer\").autoplay=true;document.getElementById(\"headline",$iii,"\").style.background = \"fuchsia\";'>",$headline,"</td></tr>";
				}
			?>
		</table></td>
		<td>
			
			<video id="videoplayer" controls  onclick="this.paused ? this.play() : this.pause();">
			<source src="<?php echo $urls[0];?>" type="video/mp4">
			Your browser does not support the video tag.
			</video>
		</td>
	</tr>
</table>


</td>
</tr></table>

<?php 
	echo "Current time: " . date("    D M j G:i:s T Y"); 
	#$dp = date_parse("20130803");
	#echo $dp['day'];
	echo '<br />';
	echo $filename;
?>

</body>
</html>