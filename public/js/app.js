(function() {
	var app = angular.module('TournamentManager', ['ngRoute']);

	var base = '/TournamentManager/public';

	function isEmpty(obj) {
	    for(var prop in obj) {
	        if(obj.hasOwnProperty(prop))
	            return false;
	        console.log(prop);
	    }

	    return true;
	}

	app.config(function($routeProvider) {
		$routeProvider
			.when('/', {
				templateUrl: 'views/home.html'
			})
			.when('/tournament/:id', {
				templateUrl: 'views/tournament.html'
			})
			.when('/create', {
				templateUrl: 'views/create.html'
			})
			.otherwise({
				redirectTo: '/'
			});
	});

	app.filter('asDate', function() {
		return function(input) {
			return new Date(input);
		};
	});

	app.filter('asGroupsOrBracket', function() {
		return function(input) {
			if(input === "RoundRobin")
				return "Groups";
			else if(input === "SingleElimination")
				return "Bracket";
		};
	});

	app.controller('HomeController', ['$http', function($http) {
		var vm = this;
		vm.tournaments = [];

		$http.get(base+'/tournaments').success(function(data) {
			vm.tournaments = data;
		});

		return vm;
	}]);

	app.controller('TournamentController', ['$http', '$routeParams', function($http, $routeParams) {
		var vm = this;

		$http.get(base+'/tournaments/'+$routeParams.id+'?stages=1&competitors=1').success(function(data) {
			vm.tournament = data;

			var firstStageId = vm.tournament.stages[0].id;

			$http.get(base+'/stages/'+firstStageId+'?matches=1').success(function(data) {
				vm.tournament.stages[0].pools = data.pools;
			});
		});

		vm.hasGroups = function() {
			var stages = vm.tournament.stages;
			
			return stages instanceof Array && stages[0] 
				&& vm.tournament.stages[0].type === "RoundRobin";
		}

		vm.hasBrackets = function() {
			var stages = vm.tournament.stages;

			return stages instanceof Array && stages[0]
				&& (stages[0].type === "SingleElimination"
					|| stages[1].type === "SingleElimination");
		}

		return vm;
	}]);

	app.controller('StagesController', ['$http', '$routeParams', function($http, $routeParams) {
		var vm = this;
		vm.stages = [];

		$http.get(base+'/tournaments'+$routeParams.id+'/stages').success(function(data) {
			vm.stages = data;
		});

		return vm;
	}]);

	app.controller('StageTabsController', ['$http', function($http) {
		var vm = this;
		vm.tab = 0;

	    vm.isSet = function(checkTab) {
      		return vm.tab === checkTab;
	    };

	    vm.setTab = function(setTab, stage) {
			vm.tab = setTab;

			if(stage.id && !stage.pools) {
				$http.get(base+'/stages/'+stage.id+'?matches=1').success(function(data) {
					stage.pools = data.pools;
				});
			}
	    };

	    return vm;
	}]);

	app.controller('CreateController', function() {

	});

	app.directive('groups', function() {
		return {
			restrict: 'EA',
			templateUrl: 'views/groups.html'
		}
	});

	app.directive('bracket', function() {
		return {
			restrict: 'EA',
			templateUrl: 'views/bracket.html'
		}
	});

	//debug functions
	var countWatchers = function() {
		var root = angular.element(document.getElementsByTagName('body'));

	    var watchers = [];

	    var f = function (element) {
	        angular.forEach(['$scope', '$isolateScope'], function (scopeProperty) { 
	            if (element.data() && element.data().hasOwnProperty(scopeProperty)) {
	                angular.forEach(element.data()[scopeProperty].$$watchers, function (watcher) {
	                    watchers.push(watcher);
	                });
	            }
	        });

	        angular.forEach(element.children(), function (childElement) {
	            f(angular.element(childElement));
	        });
	    };

	    f(root);

	    // Remove duplicate watchers
	    var watchersWithoutDuplicates = [];
	    angular.forEach(watchers, function(item) {
	        if(watchersWithoutDuplicates.indexOf(item) < 0) {
	             watchersWithoutDuplicates.push(item);
	        }
	    });

	    return watchersWithoutDuplicates.length;
	};
})();