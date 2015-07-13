var ordersHandlerApp = angular.module('ordersHandlerApp', ['ngRoute', 'ngResource', 'ordersHandlerControllers']);

var ordersHandlerControllers = angular.module('ordersHandlerControllers', []);

ordersHandlerApp.config(['$routeProvider', '$provide', '$resourceProvider', function ($routeProvider, $provide, $resourceProvider) {
    $resourceProvider.defaults.stripTrailingSlashes = false;
    $routeProvider
        .when('/',
        {
            redirectTo: '/home'
        })
        .when('',
        {
            redirectTo: '/home'
        })
        .when('/home',
        {
            templateUrl: 'html/views/home.html',
            controller: 'HomeCtrl'
        })
        .when('/register', {
            templateUrl: 'html/views/registration.html',
            controller: 'RegistrationCtrl'
        })
        .otherwise(
        {
            redirectTo: '/'
        });
}]);

ordersHandlerApp.run(['$rootScope', function($rootScope){
    $rootScope.serverPath = 'http://127.0.0.1:8000';
}]);