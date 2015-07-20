ordersHandlerApp.factory('Orders', ['$rootScope', '$resource', function($rootScope, $resource){
    return $resource($rootScope.serverPath + "/orders", null,
        {
            'query':  {
                method:'GET',
                transformResponse: function (data) {return angular.fromJson(data).list},
                isArray:true
            }
        });
}]);