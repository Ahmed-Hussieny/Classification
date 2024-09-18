var LoginController = angular.module("LoginController", []);

app.controller("loginController", [
  "$scope",
  "$location",
  function ($scope, $location) {
    $scope.loginData = {
      name: "",
      password: "",
    };
    $scope.errorMessage = "";

    $scope.submitForm = function (form) {
      if (form.$valid) {
        axios
          .post("http://localhost:8080/index.php/user/login", {
            name: $scope.loginData.name,
            password: $scope.loginData.password,
          })
          .then(function (res) {
            localStorage.setItem("userId", res.data.Data.id);
            $location.path("tasks");
            $scope.$apply();
          })
          .catch(function (error) {
            $scope.errorMessage = error.response.data.message;
            $scope.$apply();
          });
      }
    };
  },
]);
