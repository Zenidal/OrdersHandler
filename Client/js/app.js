var ordersHandlerApp = angular.module('ordersHandlerApp', ['ngRoute', 'ngResource', 'ordersHandlerControllers', 'angular-uuid', 'blockUI']);

var ordersHandlerControllers = angular.module('ordersHandlerControllers', []);

angular.module('ordersHandlerControllers').factory('httpRequestInterceptor', ['$rootScope', function ($rootScope) {
    return {
        request: function ($config) {
            if ($rootScope.hasAuthorizedUser) {
                $config.headers['Token'] = $rootScope.apiKey;
            }
            return $config;
        }
    };
}]);

ordersHandlerApp.run(['$rootScope', function ($rootScope) {
    $rootScope.serverPath = 'http://horder.server.loc:666';
}]);