ordersHandlerControllers.controller('OrdersCtrl', ['$scope', '$http', '$location', 'Orders',
    function ($scope, $http, $location, Orders) {
        $scope.orders = Orders.query();
        $scope.show = show;

        function show(id){
            $location.path('/orders/' + id);
        }
    }
]);