<html>
<head>
<meta charset="utf-8">
<title><?php echo $pageTitle;?></title>
</head>
<style type="text/css">
.container {
	width: 80%;
	min-height: 40px;
	margin: auto;
	position: relative;
	top: 20px;
	padding: 10px 20px 10px 20px;
	-moz-box-shadow: 3px 3px 10px #888;
	-webkit-box-shadow: 3px 3px 10px #888;
	box-shadow: 3px 3px 10px #888;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	overflow: hidden;
}

.container:hover {
	box-shadow: 3px 3px 15px #888;
}

.container p {
	font-size: 16px;
	font-family: sans-serif;
	text-align: center;
}
</style>
<body>
	<div class="container">
		<p>
			<?php echo $pageInfo;?>
		 </p>
	</div>
</body>
</html>