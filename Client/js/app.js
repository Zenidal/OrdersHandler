var ordersHandlerApp = angular.module('ordersHandlerApp', ['ngRoute', 'ngResource', 'ordersHandlerControllers', 'angular-uuid', 'blockUI']);

var ordersHandlerControllers = angular.module('ordersHandlerControllers', []);

angular.module('ordersHandlerControllers').factory('httpRequestInterceptor', ['$rootScope', function ($rootScope) {
    return {
        request: function ($config) {
            if ($rootScope.hasAuthorizedUser) {
                $config.headers['Token'] = $rootScope.currentUser.apiKey;
            }
            return $config;
        }
    };
}]);

ordersHandlerApp.run(['$rootScope', function ($rootScope) {
    $rootScope.serverPath = 'http://horder.server.loc:666';
}]);

var isCustomer = function ($location, $q, $rootScope) {
    var deferred = $q.defer();
    if ($rootScope.hasAuthorizedUser && $rootScope.currentUser.roleName === 'ROLE_CUSTOMER') {
        deferred.resolve()
    } else {
        deferred.reject();
        $location.path('/home');
    }
    return deferred.promise;
};

var isEngineer = function ($location, $q, $rootScope) {
    var deferred = $q.defer();
    if ($rootScope.hasAuthorizedUser && $rootScope.currentUser.roleName === 'ROLE_ENGINEER') {
        deferred.resolve()
    } else {
        deferred.reject();
        $location.path('/home');
    }
    return deferred.promise;
};

var isManager = function ($location, $q, $rootScope) {
    var deferred = $q.defer();
    if ($rootScope.hasAuthorizedUser && $rootScope.currentUser.roleName === 'ROLE_MANAGER') {
        deferred.resolve()
    } else {
        deferred.reject();
        $location.path('/home');
    }
    return deferred.promise;
};

var isUser = function ($location, $q, $rootScope) {
    var deferred = $q.defer();
    if ($rootScope.hasAuthorizedUser) {
        deferred.resolve()
    } else {
        deferred.reject();
        $location.path('/home');
    }
    return deferred.promise;
};