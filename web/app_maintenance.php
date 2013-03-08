<?php
$assetPrefix = substr_replace($_SERVER['PHP_SELF'], '', strpos($_SERVER['PHP_SELF'], basename(__FILE__)));
$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
header($protocol.' 503 Service Unavailable');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>KindleNewsletter: Google Reader for Kindle</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<link rel="icon" type="image/x-icon" href="<?= $assetPrefix ?>favicon.ico" />
		<style type="text/css">
			body {
				text-align: center;
				background-color: #DDD;
				color: black;
				font-family: sans-serif;
				font-size: 1em;
			}
			
			#terminator {
				max-width: 90%;
				border: 2px solid black;
				border-radius: 20px;
			}
		</style>
	</head>
	<body>
		<div id="container">
			<h1>KindleNewsletter.com is under maintenance!</h1>
			<p><img id="terminator" src="<?= $assetPrefix ?>maintenance.jpg" /></p>
			<hr />
			<div>
				<p><a href="https://github.com/eagleoneraptor/kindlenewsletter">KindleNewsletter.com on GitHub</a></p>
				<p>Copyright © <?= date('Y') ?> by Damián Nohales</p>
			</div>
		</div>
	</body>
</html>
