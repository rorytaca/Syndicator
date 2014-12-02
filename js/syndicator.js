angular.module('syndicatorApp', ['ngRoute', 'firebase'])
    //firebase first
    .value('fbURL', 'https://buc1ya1k9sw.firebaseio-demo.com/')
    .factory('Product', function($firebase, fbURL) {
        return $firebase(new Firebase(fbURL)).$asArray();
    })
    .config(function($routeProvider) {
        $routeProvider
            .when('/', {
              controller:'ListCtrl',
              templateUrl:'views/list.html'
            })
            .when('/new', {
              controller:'CreateCtrl',
              templateUrl:'detail.html'
            })
            .otherwise({
              redirectTo:'/'
            });
    })
    .controller('ListCtrl', function($scope, Product) {
        $scope.products = Product;
    })
    .controller('CreateCtrl', function($scope, $location, Product) {
      $scope.save = function() {
          Products.$add($scope.product).then(function(data) {
              $location.path('/');
          });
      };
    })    
    
    .controller('')