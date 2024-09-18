var app = angular.module("ClassificationApp", [
  "ngRoute",
  "LoginController",
  "TasksController",
  "UsersController",
  "TypesController",
  "TasksController",
  "ngAnimate",
]);

app.config([
  "$routeProvider",
  "$locationProvider",
  function ($routeProvider) {
    $routeProvider
      .when("/login", {
        templateUrl: "Views/Login/login.html",
        controller: "loginController",
      })
      .when("/tasks", {
        templateUrl: "Views/TasksPage/tasks.html",
        controller: "tasksController",
      })
      .when("/task/:id", {
        templateUrl: "Views/TaskPage/task.html",
        controller: "taskController",
      })
      .when("/createNewTask", {
        templateUrl: "Views/TasksPage/createNewTask.html",
        controller: "tasksController",
      })
      .when("/users", {
        templateUrl: "Views/Users/users.html",
        controller: "usersController",
      })
      .when("/createNewUser", {
        templateUrl: "Views/Users/createNewUser.html",
        controller: "usersController",
      })
      .when("/listTypes", {
        templateUrl: "Views/Types/listTypes.html",
        controller: "typesController",
      })
      .when("/createNewType", {
        templateUrl: "Views/Types/createNewType.html",
        controller: "typesController",
      })
      .otherwise({
        redirectTo: "/login",
      });
  },
]);
