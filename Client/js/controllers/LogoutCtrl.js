ordersHandlerControllers.controller('LogoutCtrl', ['$rootScope', '$http', '$location',
    function ($rootScope, $http, $location) {
        $rootScope.hasAuthorizedUser = false;
        $rootScope.currentUser = null;
        $location.path('/');
    }
]);