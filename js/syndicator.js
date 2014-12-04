angular.module('syndicatorApp', ['ngRoute','firebase'])
    //firebase first
    .value('fbURL1', 'https://amber-inferno-7558.firebaseio.com/')
    .factory('Products', function($firebase, fbURL1) {
        return $firebase(new Firebase(fbURL1)).$asArray();
    })
    .value('fbURL2', 'https://amber-inferno-7600.firebaseio.com/')
    .factory('Websites', function($firebase, fbURL2) {
        return $firebase(new Firebase(fbURL2)).$asArray();
    })
    .config(function($routeProvider) {
        $routeProvider
            .when('/', {
              controller:'ListProductsCtrl',
              templateUrl:'views/listProducts.html'
            })
            .when('/newProduct', {
              controller:'AddProductCtrl',
              templateUrl:'views/addProduct.html'
            })
            .when('/newSite', {
              controller:'AddSiteCtrl',
              templateUrl:'views/addSite.html'
            })
            .when('/listSites', {
              controller:'ListSitesCtrl',
              templateUrl:'views/listSites.html'
            })
            .otherwise({
              redirectTo:'/'
            });
    })
    .controller('ListProductsCtrl', function($scope, Products) {
        $scope.products = Products;
    })
    .controller('AddProductCtrl', function($scope, $location, Products) {
        $scope.today = function() {
           $scope.dt = new Date();
        };
        
        $scope.today();
      
        $scope.clear = function () {
          $scope.dt = null;
        };
      
        // Disable weekend selection
        $scope.disabled = function(date, mode) {
          return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
        };
      
        $scope.toggleMin = function() {
          $scope.minDate = $scope.minDate ? null : new Date();
        };
        $scope.toggleMin();
      
        $scope.open = function($event) {
          $event.preventDefault();
          $event.stopPropagation();
      
          $scope.opened = true;
        };
        
        $scope.dateOptions = {
            formatYear: 'yy',
            startingDay: 1
        };        
         
        $scope.format =  'dd-MM-yyyy';
        $scope.save = function() {
            $scope.product.status = false;
            Products.$add($scope.product).then(function(data) {
               $location.path('/');
            });
        };
    })
    .controller('ListSitesCtrl', function($scope, Websites) {
        $scope.websites = Websites;
    })
    .controller('AddSiteCtrl', function($scope, $location, Websites) {
        $scope.save = function() {
            Websites.$add($scope.website).then(function(data) {
               $location.path('/listSites');
            });
        };
    })
    .controller('FooterCtrl', function($scope) {
        $scope.nextsync = function() {
            var d = new Date();
            var h = d.getHours();
            var nh = h + 1;
            return nh;
        };
    })
    .directive('clearTable', function() {
        return function(scope, element, attrs) {
            var clickingCallback = function() {
                var r = confirm("ALERT: Are you absolutely sure you want to clear the database table?!");
                if (r == true) {
                    var fbref = new Firebase('https://amber-inferno-7558.firebaseio.com/');
                    fbref.remove();
                } else {
                    
                }
            };
            element.bind('click', clickingCallback);
        }
    });