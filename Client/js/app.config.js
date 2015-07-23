ordersHandlerApp.config(['$routeProvider', '$provide', '$resourceProvider', '$httpProvider', function ($routeProvider, $provide, $resourceProvider, $httpProvider) {
    $httpProvider.interceptors.push('httpRequestInterceptor');
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
        .when('/orders/:id', {
            templateUrl: 'html/views/order.html',
            controller: 'OrderReviewCtrl'
        })
        .when('/orders/order/new', {
            templateUrl: 'html/views/orderNew.html',
            controller: 'OrderCreationCtrl'
        })
        .when('/orders/:id/edit', {
            templateUrl: 'html/views/orderEdit.html',
            controller: 'OrderAlterationCtrl'
        })
        .when('/register', {
            templateUrl: 'html/views/registration.html',
            controller: 'RegistrationCtrl'
        })
        .when('/registrationResult/:id', {
            templateUrl: 'html/views/registrationResult.html',
            controller: 'RegistrationResultCtrl'
        })
        .when('/login', {
            templateUrl: 'html/views/authorization.html',
            controller: 'AuthorizationCtrl'
        })
        .otherwise(
        {
            redirectTo: '/'
        });
}]);