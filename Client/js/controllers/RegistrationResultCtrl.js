ordersHandlerControllers.controller('RegistrationResultCtrl', ['$scope', '$location',
    function ($scope, $location) {
        var error = $location.search('errorMessage');
        if(error !== null && error != ''){
            $scope.error = error;
            $scope.hasError = true;
        }
    }
]);