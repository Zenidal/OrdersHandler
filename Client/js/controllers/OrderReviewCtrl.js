ordersHandlerControllers.controller('OrderReviewCtrl', ['$scope', '$routeParams', 'Orders',
    function ($scope, $routeParams, Orders) {
        $scope.order = Orders.get({ id: $routeParams.id }, function(data) {
            $scope.post = data;
        });
    }
]);