ordersHandlerApp.factory('Places', ['$rootScope', '$resource', function($rootScope, $resource){
    return $resource($rootScope.serverPath + "/places");
}]);