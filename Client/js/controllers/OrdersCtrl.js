ordersHandlerControllers.controller('OrdersCtrl', ['$scope', '$http', 'Orders',
    function ($scope, $http, Orders) {
        $scope.orders = Orders.query();
    }
]);