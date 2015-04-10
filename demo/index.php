<html>
	<head>
		<title>U.R.F. Games Search</title>
		<link rel="stylesheet" href="css/styles.css">
		<script type="text/javascript" src="js/modernizr.js"></script>
		<script>
Modernizr.load({
    test : Modernizr.inputtypes.date,
    nope : ['css/jquery-ui.min.css',
          'css/jquery-ui.structure.min.css',
          'css/jquery-ui.theme.min.css',
          'js/jquery-2.1.3.min.js',
          'js/jquery-ui.min.js'],
    complete: function () {
        $('input[type=datetime-local]').datepicker({
            dateFormat: "dd-mm-yy"
        });
    }
});
		</script>
	</head>
	<body>
		<h1 class="logo">
			<a href=""><img src="img/logo.jpg" /></a>
		</h1>
		<div class="search">
			<form method="post" action="/search/">
				<input name="summonerName" placeholder="Summoner name" required></input>
				<select class="regionselect" name="region">
					<option value="na">NA</option>
					<option value="euw">EUW</option>
					<option value="eune">EUNE</option>
					<option value="lan">LAN</option>
					<option value="las">LAS</option>
					<option value="br">BR</option>
					<option value="ru">RU</option>
					<option value="tr">TR</option>
					<option value="oce">OCE</option>
					<option value="kr">KR</option>
				</select>
				<button type="submit" value="Search">Search</button>
			</form>
		</div>
	</body>
</html>