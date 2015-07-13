ordersHandlerControllers.controller('RegistrationCtrl', ['$scope', '$http', 'Companies',
    function ($scope, $http, Companies) {
        $scope.registrationCompanies = Companies.query();
    }
]);