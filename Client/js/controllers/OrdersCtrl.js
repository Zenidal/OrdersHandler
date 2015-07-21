ordersHandlerControllers.controller('OrdersCtrl', ['$scope', '$location', 'Orders', 'blockUI',
    function ($scope, $location, Orders, blockUI) {
        blockUI.start();
        $scope.orders = Orders.query();
        blockUI.stop();
        $scope.show = show;

        function show(id) {
            $location.path('/orders/' + id);
        }
    }
]);

ordersHandlerControllers.controller('OrderReviewCtrl', ['$scope', '$routeParams', '$location', 'Orders', 'blockUI',
    function ($scope, $routeParams, $location, Orders, blockUI) {
        blockUI.start();

        $scope.deleteOrder = deleteOrder;
        $scope.onDelete = onDelete;
        $scope.order =
            Orders.get({id: $routeParams.id}, function (success) {
                if ($scope.order.id === undefined) {
                    $location.path('/orders');
                }
            }, function (error) {
                if ($scope.order.id === undefined) {
                    $location.path('/orders');
                }
            });

        function deleteOrder() {
            $('#deleteOrderModal').modal('show');
        }

        function onDelete(id){
            Orders.delete({id: id}, function(success){
                $location.path('/orders');
            }, function (error) {
                $location.path('/orders');
            });
        }

        blockUI.stop();
    }
]);

ordersHandlerControllers.controller('OrderAlterationCtrl', ['$scope', '$routeParams', 'Orders',
    function ($scope, $routeParams, Orders) {
        $scope.order = Orders.get({id: $routeParams.id});
    }
]);

ordersHandlerControllers.controller('OrderCreationCtrl', ['$scope', 'Orders',
    function ($scope, Orders) {
        alert();
    }
]);