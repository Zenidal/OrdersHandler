ordersHandlerControllers.controller('RegistrationCtrl', ['$scope', '$http', 'Companies', 'Users',
    function ($scope, $http, Companies, Users) {
        $scope.registrationCompanies = Companies.query();
        $scope.register = register;

        function register() {
            if ($scope.user.password != $scope.user.passwordConfirmation) {
                $scope.passwordMismatch = true;
            } else {
                var User = new Users;
                User.username = $scope.user.username;
                User.password = $scope.user.password;
                User.passwordConfirmation = $scope.user.passwordConfirmation;
                User.firstName = $scope.user.firstName;
                User.surname = $scope.user.surname;
                User.email = $scope.user.email;
                User.companies = $scope.user.companies;
                User.$save(
                    function (data) {
                        if(data.errorMessage !== null){
                            $scope.hasError = true;
                            $scope.error = data.errorMessage;
                        }
                        if(data.message !== null){
                            $scope.hasMessage = true;
                            $scope.message = data.message;
                        }
                    },
                    function (error) {
                        $scope.hasError = true;
                        $scope.error = error;
                    }
                );
            }
        }
    }
]);