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
            }, function () {
                if ($scope.order.id === undefined) {
                    $location.path('/orders');
                }
            });

        function deleteOrder() {
            $('#deleteOrderModal').modal('show');
        }

        function onDelete(id) {
            Orders.delete({id: id}, function () {
                $location.path('/orders');
            }, function () {
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
        $scope.create = create;
        $scope.companies = $rootScope.currentUser.companies[0];

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
                    $scope.companyPlaces = data;
                    $scope.places = $scope.companyPlaces[0];
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

        $scope.changeCompany = function(companies) {
            var req = {
                method: 'GET',
                url: $rootScope.serverPath + '/companies/' + companies.id + '/places'
            };
            $http(req)
                .success(function (data) {
                    if (data.errorMessage !== undefined) {
                        $scope.hasError = true;
                        $scope.error = data.errorMessage;
                    } else {
                        $scope.companyPlaces = data;
                        $scope.places = $scope.companyPlaces[0];
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
        };

        function create() {
            var Order = new Orders;
            Order.userId = $rootScope.currentUser.id;
            Order.description = $scope.description;
            Order.address = $scope.address;
            Order.companyId = $scope.companies.id;
            Order.placeId = $scope.places.id;
            Order.$save(function (success) {
                if (success.errorMessage !== null) {
                    $scope.hasError = true;
                    $scope.error = success.errorMessage;
                }
                if (success.message !== null) {
                    $scope.hasMessage = true;
                    $scope.message = success.message;
                }
            }, function (error) {
                $scope.hasError = true;
                $scope.error = error;
            });
        }
    }
]);