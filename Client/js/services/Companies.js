ordersHandlerApp.factory('Companies', ['$rootScope', '$resource', function($rootScope, $resource){
    return $resource($rootScope.serverPath + "/companies");
}]);