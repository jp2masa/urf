<!DOCTYPE html> 
<html>
	<head>
		<title>U.R.F. Matches Search</title>
		<link rel="stylesheet" href="css/select2.min.css">
		<link rel="stylesheet" href="css/styles.css">
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="js/select2.min.js"></script>
		<script>
			$(document).ready(function() {
				$(".search select").select2({
					minimumResultsForSearch: Infinity
				});
				$(".mpmbchamps select").select2({
					minimumResultsForSearch: Infinity
				});
			});
		</script>
	</head>
	<body>
<?php
  $apikey = "INSERT_API_KEY_HERE";
  isset($_POST["region"]) ? $region = $_POST["region"] : $region = "na";
  if (isset($_POST["summonerName"])) $summonerName = $_POST["summonerName"];

  $day = rand(1, 14);
  $hour = rand(0, 23);
  $minbase = rand(0, 11);
  $minute = 5 * $minbase;
  $beginDate = mktime($hour, $minute, 0, 04, $day, 2015);

  $url = "https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v4.1/game/ids?beginDate=" . $beginDate . "&api_key=" . $apikey;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $matchIds = curl_exec($ch);
  $matchIds = json_decode($matchIds, true);

  foreach($matchIds as $matchId)
  {
    $url = "https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v2.2/match/" . $matchId . "?api_key=" . $apikey;
    curl_setopt($ch, CURLOPT_URL, $url);
    $matchData = curl_exec($ch);
    $matchData = json_decode($matchData, true);

    foreach($matchData["participants"] as $p)
    {
      $champsFreq[$p["championId"]]++;
    }
	
	foreach($matchData["teams"] as $t)
    {
      foreach($t["bans"] as $b)
	  {
		$bansFreq[$b["championId"]]++;
	  }
    }
  }

  arsort($champsFreq);
  $champsFreq5 = array_slice($champsFreq, -count($champsFreq) - 5, -count($champsFreq) + 5, true);
  
  arsort($bansFreq);
  $bansFreq3 = array_slice($bansFreq, -count($bansFreq) - 3, -count($bansFreq) + 3,true);

  $bc = 0;
  $mc = 0;

  foreach($champsFreq5 as $champPickId => $cp)
  {
    $champsFreq5[$mc] = $champPickId;
    unset($champsFreq5[$champPickId]);
    
    $url = "https://global.api.pvp.net/api/lol/static-data/" . $region . "/v1.2/champion/" . $champPickId . "?api_key=" . $apikey;
	curl_setopt($ch, CURLOPT_URL, $url);
	$championPickData = curl_exec($ch);
	$championPickData = json_decode($championPickData, true);
	$champsFreq5[$mc] = $championPickData["key"];
	$champsFreqNames[$mc] = $championPickData["name"];

    $mc++;
  }
    
  foreach($bansFreq3 as $champBanId => $cb)
  {
    $bansFreq3[$bc] = $champBanId;
    unset($bansFreq3[$champBanId]);
    
    $url = "https://global.api.pvp.net/api/lol/static-data/" . $region . "/v1.2/champion/" . $champBanId . "?api_key=" . $apikey;
	curl_setopt($ch, CURLOPT_URL, $url);
	$championBanData = curl_exec($ch);
	$championBanData = json_decode($championBanData, true);
	$bansFreq3[$bc] = $championBanData["key"];
	$bansFreqNames[$bc] = $championBanData["name"];

    $bc++;
  }
  
  curl_close($ch);
?>
		<h1 class="logo">
			<a href=""><img src="img/logo.jpg" /></a>
		</h1>
		<div class="search">
			<form name="searchForm" method="post" action="/search/">
				<input name="summonerName" placeholder="Summoner name" <?php if (isset($_POST["summonerName"]))?>value="<?php echo $summonerName?>"<?php ;?> required></input>
				<select name="region" onchange="searchForm.action=''; this.form.submit()" required>
					<option value="na"<?php if($region == "na") echo " selected";?>>NA</option>
					<option value="euw"<?php if($region == "euw") echo " selected";?>>EUW</option>
					<option value="eune"<?php if($region == "eune") echo " selected";?>>EUNE</option>
					<option value="lan"<?php if($region == "lan") echo " selected";?>>LAN</option>
					<option value="las"<?php if($region == "las") echo " selected";?>>LAS</option>
					<option value="br"<?php if($region == "br") echo " selected";?>>BR</option>
					<option value="ru"<?php if($region == "ru") echo " selected";?>>RU</option>
					<option value="tr"<?php if($region == "tr") echo " selected";?>>TR</option>
					<option value="oce"<?php if($region == "oce") echo " selected";?>>OCE</option>
					<option value="kr"<?php if($region == "kr") echo " selected";?>>KR</option>
				</select>
				<button onclick="searchForm.action='/search/'" type="submit" value="Search">Search</button>
			</form>
		</div>

		<div class="mpmbchamps">
			<table>
				<caption><h4>Most Played Champions</h4></caption>
				<colgroup>
					<col width="20%"></col>
					<col width="20%"></col>
					<col width="60%"></col>
				</colgroup>
				<thead>
					<th></th>
					<th></th>
					<th></th>
				</thead>
				<tbody>
<?php
				for($i = 0; $i <= 4; $i++)
				{
?>
					<tr>
						<td><?php echo $i + 1;?></td>
						<td><img src="img/mpchampions/<?php echo $champsFreq5[$i];?>.png"></img></td>
						<td><span><?php echo $champsFreqNames[$i];?></span></td>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
			<table>
				<caption><h4>Most Banned Champions</h4></caption>
				<colgroup>
					<col width="20%"></col>
					<col width="20%"></col>
					<col width="60%"></col>
				</colgroup>
				<thead>
					<th></th>
					<th></th>
					<th></th>
				</thead>
				<tbody>
<?php
				for($i = 0; $i <= 2; $i++)
				{
?>
					<tr>
						<td><?php echo $i + 1;?></td>
						<td><img src="img/mpchampions/<?php echo $bansFreq3[$i];?>.png"></img></td>
						<td><span><?php echo $bansFreqNames[$i];?></span></td>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
		</div>
		<div class="tip">(Based on random date and time for the selected region)</div>
	</body>
</html>
