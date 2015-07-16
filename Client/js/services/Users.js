ordersHandlerApp.factory('Users', ['$rootScope', '$resource', function($rootScope, $resource){
    return $resource($rootScope.serverPath + "/users");
}]);