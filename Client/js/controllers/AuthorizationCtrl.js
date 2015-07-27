ordersHandlerControllers.controller('AuthorizationCtrl', ['$scope', '$http', '$rootScope', '$location',
    function ($scope, $http, $rootScope, $location) {
        $scope.login = login;

        function login() {
            var req = {
                method: 'POST',
                url: $rootScope.serverPath + '/authorize',
                data: {
                    username: $scope.user.username,
                    password: $scope.user.password
                }
            };
            $http(req)
                .success(function (data, status, headers) {
                    if (data.errorMessage !== undefined) {
                        $scope.hasError = true;
                        $scope.error = data.errorMessage;
                        $scope.errorMessage = data.error;
                        $scope.lastUsername = data.lastUsername;
                    } else {
                        $rootScope.hasAuthorizedUser = true;
                        $rootScope.currentUser = {
                            apiKey: data.apiKey,
                            id: data.id,
                            username: data.username,
                            roleName: data.roleName,
                            companies: data.companies,
                            isManager: data.roleName === 'ROLE_MANAGER'
                        };
                        $location.path('/orders');
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
    }
]);