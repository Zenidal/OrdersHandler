ordersHandlerControllers.controller('AuthorizationCtrl', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {
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
                        $rootScope.apiKey = data.apiKey;
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