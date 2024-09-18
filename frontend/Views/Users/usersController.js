var app = angular.module("UsersController", []);

app.factory("UserService", function () {
  const baseUrl = "http://localhost:8080/index.php/user";

  return {
    getAllUsers: function ($scope) {
      const { searchQuery = "", itemsPerPage, currentPage } = $scope;
      return axios.get(
        `${baseUrl}/getAllUsers?page=${currentPage}&limit=${itemsPerPage}&searchName=${searchQuery}`
      );
    },
    registerUser: function (userData) {
      return axios.post(`${baseUrl}/register`, userData);
    },
    updateUser: function (user) {
      return axios.put(`${baseUrl}/updateUser`, user);
    },
  };
});

app.controller("usersController", [
  "$scope",
  "UserService",
  "$timeout",
  function ($scope, UserService, $timeout) {
    $scope.location = "/users";
    $scope.users = [];
    $scope.userData = { name: "", password: "", userType: "" };
    $scope.searchQuery = "";
    $scope.itemsPerPage = 10;
    $scope.rowsPerPageOptions = [10, 50, 100, 500, 1000, 2000];
    $scope.currentPage = 1;
    $scope.selectedUser = {};
    $scope.updateSuccessMessage = "";
    $scope.errorUpdateUserMessage = "";
    $scope.successAddUserMessage = "";
    $scope.errorAddUserMessage = "";

    $scope.getUsers = function () {
      UserService.getAllUsers($scope)
        .then(function (res) {
          $scope.users = res.data.data;
          if (res.data.data) {
            $scope.totalPages = res.data.pagination.total_pages;
          }
          $scope.$apply();
        })
        .catch(function (error) {
          console.log("Error fetching users:", error);
        });
    };

    $scope.getPageCount = function () {
      return Math.ceil($scope.totalPages / $scope.itemsPerPage);
    };

    $scope.setPage = function (page) {
      if (page > 0 && page <= $scope.totalPages) {
        $scope.currentPage = page;
      }
    };

    $scope.prevPage = function () {
      if ($scope.currentPage > 1) {
        $scope.currentPage--;
        $scope.getUsers();
      }
    };

    $scope.nextPage = function () {
      if ($scope.currentPage < $scope.totalPages) {
        $scope.currentPage++;
        $scope.getUsers();
      }
    };

    $scope.performSearch = function () {
      $scope.searchQuery = $scope.searchQueryTemp;
      $scope.getUsers();
    };

    $scope.resetInput = function () {
      $scope.searchQueryTemp = "";
      $scope.performSearch();
    };

    $scope.update = function (user) {
      $scope.selectedUser = angular.copy(user);
    };

    $scope.submitUpdateUser = function () {
      if (!$scope.selectedUser.id) return;

      UserService.updateUser($scope.selectedUser)
        .then(function (res) {
          $scope.updateSuccessMessage = res.data.message;
          $scope.getUsers();
          $timeout(clearMessages, 3000);
        })
        .catch(function (error) {
          $scope.errorUpdateUserMessage =
            "Error updating user: " + error.message;
          $timeout(clearMessages, 3000);
        });
    };

    $scope.submitUserForm = function (form) {
      if (form.$valid) {
        UserService.registerUser($scope.userData)
          .then(function (response) {
            $scope.successAddUserMessage = "User added successfully!";
            $scope.getUsers();
            // $scope.userData = { name: "", password: "", userType: "" };
          })
          .catch(function (error) {
            $scope.errorAddUserMessage = "Error adding user: " + error.message;
          })
          .finally(function () {
            $timeout(clearMessages, 3000);
          });
      }
    };

    function clearMessages() {
      $scope.errorAddUserMessage = "";
      $scope.successAddUserMessage = "";
      $scope.errorUpdateUserMessage = "";
      $scope.updateSuccessMessage = "";
    }

    $scope.$watch("itemsPerPage", function () {
      $scope.currentPage = 1;
      $scope.getUsers();
    });
  },
]);
