var ordersHandlerApp = angular.module('ordersHandlerApp', ['ngRoute', 'ngResource', 'ordersHandlerControllers', "angular-uuid"]);

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
        .when('/orders', {
            templateUrl: 'html/views/orders.html',
            controller: 'OrdersCtrl'
        })
        .when('/register', {
            templateUrl: 'html/views/registration.html',
            controller: 'RegistrationCtrl'
        })
        .when('/registrationResult/:id', {
            templateUrl: 'html/views/registrationResult.html',
            controller: 'RegistrationResultCtrl'
        })
        .otherwise(
        {
            redirectTo: '/'
        });
}]);

ordersHandlerApp.run(['$rootScope', function ($rootScope) {
    $rootScope.serverPath = 'http://horder.server.loc:666';
}]);