angular.module('syndicatorApp', ['ngRoute', 'firebase'])
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
              controller:'CreateCtrl',
              templateUrl:'views/create.html'
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
    .controller('CreateCtrl', function($scope, $location, Products) {
        
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