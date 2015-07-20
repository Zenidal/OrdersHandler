ordersHandlerControllers.controller('OrdersCtrl', ['$scope', '$http', '$location', 'Orders',
    function ($scope, $http, $location, Orders) {
        $scope.orders = Orders.query();
        $scope.show = show;

        function show(id) {
            $location.path('/orders/' + id);
        }
    }
]);

var OrderReviewCtrl = ordersHandlerControllers.controller('OrderReviewCtrl', ['$scope', '$routeParams', 'Orders',
    function ($scope, $routeParams, Orders) {
        $scope.order = Orders.get({id: $routeParams.id});
    }
]);

ordersHandlerControllers.controller('OrderDeleteCtrl', ['$scope', '$routeParams', 'Orders',
    function ($scope, $routeParams, Orders) {
        $scope.order = Orders.get({id: $routeParams.id});
    }
]);

ordersHandlerControllers.controller('OrderEditCtrl', ['$scope', '$routeParams', 'Orders',
    function ($scope, $routeParams, Orders) {
        $scope.order = Orders.get({id: $routeParams.id});
    }
]);