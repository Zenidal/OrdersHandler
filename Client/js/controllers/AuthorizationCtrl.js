ordersHandlerControllers.controller('AuthorizationCtrl', ['$scope', '$http', '$rootScope',
    function ($scope, $http, $rootScope) {
        $scope.login = login;

        function login(){
            var req = {
                method: 'GET',
                url: $rootScope.serverPath + '/authorize',
                data: {
                    username: $scope.user.username,
                    password: $scope.user.password
                }
            };
            $http(req)
                .success(function(data){
                    console.log(data);
                    if(data.errorMessage !== null){
                        $scope.hasError = true;
                        $scope.error = data.errorMessage;
                        $scope.errorMessage = data.error;
                        $scope.lastUsername = data.lastUsername;
                    }
                    if(data.message !== null){
                        $scope.hasMessage = true;
                        $scope.message = data.message;
                    }
                })
                .error(function(error){
                    console.log(error);
                    $scope.hasError = true;
                    $scope.error = error;
                });
        }
    }
]);