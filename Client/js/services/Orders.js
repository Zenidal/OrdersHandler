ordersHandlerApp.factory('Orders', ['$rootScope', '$resource', function($rootScope, $resource){
    return $resource($rootScope.serverPath + "/orders");
}]);