ordersHandlerControllers.controller('RegistrationResultCtrl', ['$scope', '$routeParams', '$http', '$location', '$rootScope',
    function ($scope, $routeParams, $http, $location, $rootScope) {
        $scope.params = $routeParams;
        if($routeParams.confirmationLink !== null){
            var req = {
                method: 'POST',
                url: $rootScope.serverPath + '/email_confirmation',
                data: { confirmationLink: $location.absUrl() }
            };
            $http(req)
                .success(function(data){
                    if(data.errorMessage !== null){
                        $scope.hasError = true;
                        $scope.error = data.errorMessage;
                    }
                    if(data.message !== null){
                        $scope.hasMessage = true;
                        $scope.message = data.message;
                    }
                })
                .error(function(error){
                    $scope.hasError = true;
                    $scope.error = error;
                });
        }
    }
]);