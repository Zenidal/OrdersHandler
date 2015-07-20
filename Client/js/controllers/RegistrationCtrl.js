ordersHandlerControllers.controller('RegistrationCtrl', ['$scope', '$http', '$location', 'Companies', 'Users', "uuid",
    function ($scope, $http, $location, Companies, Users, uuid) {
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
                User.confirmationLink = $location.protocol() + '://' + location.host + '/#/registrationResult/' + uuid.v4();
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