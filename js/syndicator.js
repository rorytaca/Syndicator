angular.module('syndicatorApp', ['ngRoute', 'firebase'])
    //firebase first
    .value('fbURL', 'https://amber-inferno-7558.firebaseio.com/')
    .factory('Products', function($firebase, fbURL) {
        return $firebase(new Firebase(fbURL)).$asArray();
    })
    .config(function($routeProvider) {
        $routeProvider
            .when('/', {
              controller:'ListCtrl',
              templateUrl:'views/list.html'
            })
            .when('/newProduct', {
              controller:'CreateCtrl',
              templateUrl:'views/create.html'
            })
            .when('/newSite', {
              controller:'addSiteCtrl',
              templateUrl:'views/addSite.html'
            })
            .otherwise({
              redirectTo:'/'
            });
    })
    .controller('ListCtrl', function($scope, Products) {
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
    
    .controller('')