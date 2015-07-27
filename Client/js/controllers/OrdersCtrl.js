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
                if (success.errorMessage !== undefined) {
                    alert(success.errorMessage);
                }
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

        function onDelete(id) {
            Orders.delete({id: id}, function (success) {
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

ordersHandlerControllers.controller('OrderCreationCtrl', ['$scope', '$rootScope', 'Orders', '$http',
    function ($scope, $rootScope, Orders, $http) {
        $scope.companyChange = companyChange;
        $scope.create = create;

        var req = {
            method: 'GET',
            url: $rootScope.serverPath + '/companies/' + $rootScope.currentUser.companies[0].id + '/places'
        };
        $http(req)
            .success(function (data) {
                if (data.errorMessage !== undefined) {
                    $scope.hasError = true;
                    $scope.error = data.errorMessage;
                } else {
                    $scope.places = data;
                }
                if (data.message !== null) {
                    $scope.hasMessage = true;
                    $scope.message = data.message;
                }
            })
            .error(function (error) {
                $scope.hasError = true;
                $scope.error = error;
            });

        function companyChange(id) {
            var req = {
                method: 'GET',
                url: $rootScope.serverPath + '/companies/' + id + '/places'
            };
            $http(req)
                .success(function (data) {
                    if (data.errorMessage !== undefined) {
                        $scope.hasError = true;
                        $scope.error = data.errorMessage;
                    } else {
                        $scope.places = data;
                    }
                    if (data.message !== null) {
                        $scope.hasMessage = true;
                        $scope.message = data.message;
                    }
                })
                .error(function (error) {
                    $scope.hasError = true;
                    $scope.error = error;
                });
        }

        function create() {
            var Order = new Orders;
            Order.userId = $rootScope.currentUser.id;
            Order.description = $scope.description;
            Order.address = $scope.address;
            Order.companyId = $companies.id;
            Order.placeId = $company.places.id;
            Order.$save(function (success) {
                if (data.errorMessage !== null) {
                    $scope.hasError = true;
                    $scope.error = data.errorMessage;
                }
                if (data.message !== null) {
                    $scope.hasMessage = true;
                    $scope.message = data.message;
                }
            }, function (error) {
                $scope.hasError = true;
                $scope.error = error;
            });
        }
    }
]);