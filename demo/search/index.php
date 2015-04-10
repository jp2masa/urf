<?php
	$apikey = '562765ac-d44e-41fc-9573-ef3971f07fbb';
	$summonerName = $_POST["summonerName"];
	$region = $_POST["region"];
	$url = "https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.4/summoner/by-name/" . $summonerName . "?api_key=" . $apikey;
	$hasGames = false;

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	
	$result = json_decode($result, true);
	
	$summonerId = $result[strtolower($summonerName)]["id"];
	$summonerName = $result[strtolower($summonerName)]["name"];
	
	$url = "https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.3/game/by-summoner/" . $summonerId . "/recent?api_key=" . $apikey;
	
	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);
	$result = json_decode($result, true);

	for($match = 0; $match <= 9; $match++)
	{
		if($result["games"][$match]["subType"] == "URF")
		{
				
			$url = "https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v2.2/match/" . $result["games"][$match]["gameId"] . "?api_key=" . $apikey;
			curl_setopt($ch, CURLOPT_URL, $url);
			$matchData = curl_exec($ch);
			$matchData = json_decode($matchData, true);
			$matchDuration = gmdate("i:s", $matchData["matchDuration"]);
			
			foreach($matchData["participants"] as $participant)
			{
				$kills[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["kills"];
				$deaths[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["deaths"];
				$assists[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["assists"];
				$item0[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item0"];
				$item1[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item1"];
				$item2[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item2"];
				$item3[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item3"];
				$item4[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item4"];
				$item5[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item5"];
				$item6[$participant["teamId"]][$participant["championId"]] = $participant["stats"]["item6"];
				$spell1[$participant["teamId"]][$participant["championId"]] = $participant["spell1Id"];
				$spell2[$participant["teamId"]][$participant["championId"]] = $participant["spell2Id"];
			}
			
			for($i = 1; $i <= 2; $i++)
			{
				$t = $i * 100;
				
				foreach($spell1[$t] as &$spellId)
				{
					$url = "https://global.api.pvp.net/api/lol/static-data/" . $region . "/v1.2/summoner-spell/" . $spellId . "?api_key=" . $apikey;
					curl_setopt($ch, CURLOPT_URL, $url);
					$spelldata = curl_exec($ch);
					$spelldata = json_decode($spelldata, true);
					$spellId = $spelldata["key"];
				}
				
				foreach($spell2[$t] as &$spellId)
				{
					$url = "https://global.api.pvp.net/api/lol/static-data/" . $region . "/v1.2/summoner-spell/" . $spellId . "?api_key=" . $apikey;
					curl_setopt($ch, CURLOPT_URL, $url);
					$spelldata = curl_exec($ch);
					$spelldata = json_decode($spelldata, true);
					$spellId = $spelldata["key"];
				}
			}
			
			$hasGames = true;
			
			$won = $result["games"][$match]["stats"]["win"];
			
			if($won)
				$class = "victory";
			else
				$class = "defeat";
			
			if($result["games"][$match]["teamId"] == 100)
			{
				$isBlue = true;
				
				if($won)
				{
					$blueTeamVictoryDefeat = "Victory";
					$redTeamVictoryDefeat = "Defeat";
				} else {
					$blueTeamVictoryDefeat = "Defeat";
					$redTeamVictoryDefeat = "Victory";
				}
			} else {
				$isBlue = false;
				
				if($won)
				{
					$blueTeamVictoryDefeat = "Defeat";
					$redTeamVictoryDefeat = "Victory";
				} else {
					$blueTeamVictoryDefeat = "Victory";
					$redTeamVictoryDefeat = "Defeat";
				}
			}
			
			$bn = 0;
			$rn = 0;
			
			for($summonerList = 0; $summonerList <= 8; $summonerList++)
			{
				if($isBlue)
				{
					$blueplayers = 3;
					$redplayers = 4;
					if($result["games"][$match]["fellowPlayers"][$summonerList]["teamId"] == 100)
					{
						$blueSummoners[$bn + 1] = $result["games"][$match]["fellowPlayers"][$summonerList]["summonerId"];
						$blueChampions[$bn + 1] = $result["games"][$match]["fellowPlayers"][$summonerList]["championId"];
						$bn++;
					} else {
						$redSummoners[$rn] = $result["games"][$match]["fellowPlayers"][$summonerList]["summonerId"];
						$redChampions[$rn] = $result["games"][$match]["fellowPlayers"][$summonerList]["championId"];
						$rn++;
					}
				} else {
					$blueplayers = 4;
					$redplayers = 3;
					
					if($result["games"][$match]["fellowPlayers"][$summonerList]["teamId"] == 100)
					{
						$blueSummoners[$bn] = $result["games"][$match]["fellowPlayers"][$summonerList]["summonerId"];
						$blueChampions[$bn] = $result["games"][$match]["fellowPlayers"][$summonerList]["championId"];
						$bn++;
					} else {
						$redSummoners[$rn + 1] = $result["games"][$match]["fellowPlayers"][$summonerList]["summonerId"];
						$redChampions[$rn + 1] = $result["games"][$match]["fellowPlayers"][$summonerList]["championId"];
						$rn++;
					}
				}
			}
?>
<div class="matchHolder <?php echo $class?>">
	<div class="blueteam">
		<div class="title"><span><?php echo $blueTeamVictoryDefeat?></span></div>
		<table>
			<colgroup>
				<col width="8%"></col>
				<col width="20%"></col>
				<col width="12%"></col>
				<col width="36%"></col>
				<col width="24%"></col>
			</colgroup>
			<thead>
				<tr>
					<th></th>
					<th>Summoner</th>
					<th>Spells</th>
					<th>Item Build</th>
					<th>KDA</th>
				</tr>
			</thead>
			<tbody>
<?php
			foreach($blueSummoners as &$bluesumm)
			{
				$summString = $summString . $bluesumm . ",";
			}
			
			foreach($redSummoners as &$redsumm)
			{
				$summString = $summString . $redsumm . ",";
			}
			
			$summString = rtrim($summString, ",");
			
			$url = "https://" . $region . ".api.pvp.net/api/lol/" . $region . "/v1.4/summoner/" . $summString . "/name/?api_key=" . $apikey;
			curl_setopt($ch, CURLOPT_URL, $url);
			$summList = curl_exec($ch);
			
			$summList = json_decode($summList, true);
			foreach($blueSummoners as &$bluesumm)
			{
				$bluesumm = $summList[$bluesumm];
			}
			
			foreach($redSummoners as &$redsumm)
			{
				$redsumm = $summList[$redsumm];
			}
			
			for($summonerBlue = 0; $summonerBlue <= 4; $summonerBlue++)
			{
				if($isBlue && $summonerBlue == 0)
				{
					$championId = $result["games"][$match]["championId"];
					$blueSummoners[0] = $summonerName;
				} else {
					$championId = $blueChampions[$summonerBlue];
				}
			
				$url = "https://global.api.pvp.net/api/lol/static-data/" . $region . "/v1.2/champion/" . $championId . "?api_key=" . $apikey;
				curl_setopt($ch, CURLOPT_URL, $url);
				$championData = curl_exec($ch);
				$championData = json_decode($championData, true);
				$championKey = $championData["key"];
				$championName = $championData["name"];
?>
				<tr class="player">
					<td><img src="../img/champions/<?php echo $championKey?>.png" title="<?php echo $championName?>"></img></td>
					<td <?php if($summonerBlue == 0 && $isBlue) echo("style=\"font-weight: bold\"")?>><span><?php echo htmlentities($blueSummoners[$summonerBlue], 0, 'UTF-8');?></span></td>
					<td><div class="spells">
						<img src="../img/spells/<?php echo $spell1[100][$championId]?>.png"></img>
						<img src="../img/spells/<?php echo $spell2[100][$championId]?>.png"></img>
					</div></td>
					<td><div class="build">
						<img src="../img/items/<?php echo $item0[100][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item1[100][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item2[100][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item3[100][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item4[100][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item5[100][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item6[100][$championId]?>.png"></img>
					</div></td>
					<td><span><?php echo $kills[100][$championId] . "/" . $deaths[100][$championId] . "/" . $assists[100][$championId]?></span></td>
				</tr>
<?php

			}
?>

			</tbody>
		</table>
	</div>
	<div class="middle">
		<span><?php echo $matchDuration?></span>
		<div><h2>VS</h2></div>
	</div>
	<div class="redteam">
		<div class="title"><span><?php echo $redTeamVictoryDefeat?></span></div>
		<table>
			<colgroup>
				<col width="8%"></col>
				<col width="20%"></col>
				<col width="12%"></col>
				<col width="36%"></col>
				<col width="24%"></col>
			</colgroup>
			<thead>
				<tr>
					<th></th>
					<th>Summoner</th>
					<th>Spells</th>
					<th>Item Build</th>
					<th>KDA</th>
				</tr>
			</thead>
			<tbody>
<?php
for($summonerRed = 0; $summonerRed <= 4; $summonerRed++)
			{
				if(!$isBlue && $summonerRed == 0)
				{
					$championId = $result["games"][$match]["championId"];
					$redSummoners[0] = $summonerName;
				} else {
					$championId = $redChampions[$summonerRed];
				}
			
				$url = "https://global.api.pvp.net/api/lol/static-data/" . $region . "/v1.2/champion/" . $championId . "?api_key=" . $apikey;
				curl_setopt($ch, CURLOPT_URL, $url);
				$championData = curl_exec($ch);
				$championData = json_decode($championData, true);
				$championKey = $championData["key"];
				$championName = $championData["name"];
?>
				<tr class="player">
					<td><img src="../img/champions/<?php echo $championKey?>.png" title="<?php echo $championName?>"></img></td>
					<td <?php if($summonerRed == 0 && !$isBlue) echo("style=\"font-weight: bold\"")?>><span><?php echo htmlentities($redSummoners[$summonerRed], 0, 'UTF-8')?></span></td>
					<td><div class="spells">
						<img src="../img/spells/<?php echo $spell1[200][$championId]?>.png"></img>
						<img src="../img/spells/<?php echo $spell2[200][$championId]?>.png"></img>
					</div></td>
					<td><div class="build">
						<img src="../img/items/<?php echo $item0[200][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item1[200][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item2[200][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item3[200][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item4[200][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item5[200][$championId]?>.png"></img>
						<img src="../img/items/<?php echo $item6[200][$championId]?>.png"></img>
					</div></td>
					<td><span><?php echo $kills[200][$championId] . "/" . $deaths[200][$championId] . "/" . $assists[200][$championId]?></span></td>
				</tr>
<?php

			}
?>
			</tbody>
		</table>
	</div>
</div>

<?php
			unset($blueSummoners);
			unset($redSummoners);
			unset($summList);
			unset($summString);
		}
	}
	
	if(!$hasGames)
		print("<div class=\"nomatches\">No U.R.F. Matches Found!</div>");
	
	curl_close($ch);
?>

<html>
	<head>
		<title><?php echo $summonerName?> U.R.F. Games Search</title>
		<link rel="stylesheet" href="../css/styles.css">
	</head>
	<body>
		
	</body>
</html>