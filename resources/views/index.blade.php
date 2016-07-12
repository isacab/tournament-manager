<!DOCTYPE html>
<html ng-app="TournamentManager">
    <head>
        <title>TournamentManager</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
    	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.min.js"></script>
    	<script src="https://code.angularjs.org/1.2.28/angular-route.min.js"></script>
    </head>
    <body>
    	<header>
			<div class="navbar navbar-inverse navbar-static-top">
			<div class="container">
				<a href="#/" class="navbar-brand">Tournament Manager</a>
				<button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="collapse navbar-collapse navHeaderCollapse">
					<ul class="nav navbar-nav" id="main-menu">
						<li><a href="#/">Start</a></li>
						<li><a href="#/create">New Tournament</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#login" data-toggle="modal">Login</a></li>
					</ul>
				</div>
			</div>
			</div>
		</header>
		<div class="container" ng-view></div>

		<script src="js/app.js"></script>
    </body>
</html>