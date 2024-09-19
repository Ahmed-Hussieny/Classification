var TasksController = angular.module("TasksController", []);

app.factory("TasksService", function () {
  const baseUrl = "http://10.0.0.19:8080/index.php";

  return {
    getAllTasks: function ($scope) {
      const {
        searchName = "",
        searchUser = "",
        searchDate = "",
        itemsPerPage,
        currentPage,
      } = $scope;

      return axios.get(
        `${baseUrl}/task/getAllTasks?page=${
          currentPage + 1
        }&limit=${itemsPerPage}&searchName=${searchName || ""}&searchUser=${
          searchUser || ""
        }&searchDate=${searchDate || ""}`
      );
    },
    getAlltypes: function () {
      return axios.get(`${baseUrl}/type/getAllTypes`);
    },
    getAllLabelsForType: function (typeId) {
      return axios.get(
        `${baseUrl}/label/getAllLabelsForSpecificType?typeId=${typeId}`
      );
    },
  };
});

app.controller("tasksController", [
  "$scope",
  "TasksService",
  "$location",
  function ($scope, TasksService, $location) {
    $scope.location = "/tasks";
    $scope.currentPage = 0;
    $scope.uploadProgress = 0;
    $scope.itemsPerPage = 10;
    $scope.totalPages = 0;
    $scope.rowsPerPageOptions = [10, 50, 100, 500, 1000, 2000];
    $scope.searchName = "";
    $scope.searchUser = "";
    $scope.searchDate = "";
    $scope.tasks = [];
    $scope.taskTypes = [];
    $scope.labels = [];
    $scope.taskData = {
      name: "",
      type: "",
      userId: localStorage.getItem("userId"),
      file: null,
    };
    $scope.getTasks = function () {
      var userId = localStorage.getItem("userId");
      TasksService.getAllTasks($scope)
        .then(function (res) {
          $scope.tasks = res.data.data;
          if (res.data.data) {
            $scope.totalPages = res.data.pagination.total_pages;
          }
          $scope.$apply();
        })
        .catch(function (error) {
          console.log(error);
        });
    };

    $scope.getAlltypes = function () {
      TasksService.getAlltypes()
        .then(function (res) {
          $scope.taskTypes = res.data.data;
          $scope.$apply();
        })
        .catch(function (error) {
          console.log(error.response);
        });
    };

    $scope.getLabelsForType = function () {
      TasksService.getAllLabelsForType($scope.taskData.type)
        .then(function (res) {
          $scope.labels = res.data.data;
          $scope.$apply();
        })
        .catch(function (error) {
          console.log(error.response);
        });
    };

    $scope.setPage = function (page) {
      $scope.currentPage = page;
      $scope.getTasks();
    };

    $scope.prevPage = function () {
      if ($scope.currentPage > 0) {
        $scope.currentPage--;
        $scope.getTasks();
      }
    };
    $scope.nextPage = function () {
      if ($scope.currentPage < $scope.totalPages) {
        $scope.currentPage++;
        $scope.getTasks();
      }
    };

    $scope.$watch("itemsPerPage", function () {
      $scope.currentPage = 0;
      $scope.getTasks();
    });

    $scope.resetInput = function () {
      $scope.searchInputName = "";
      $scope.searchInputUser = "";
      $scope.searchInputDate = "";
      $scope.applySearch();
    };

    $scope.applySearch = function () {
      $scope.searchName = $scope.searchInputName;
      $scope.searchUser = $scope.searchInputUser;
      $scope.searchDate = $scope.searchInputDate;
      $scope.getTasks();
    };

    $scope.submitForm = function (form) {
      $scope.errorAddTaskMessage = "";
      $scope.successAddTaskMessage = "";

      if (!$scope.taskData.file) {
        $scope.errorAddTaskMessage = "File is required";
        return;
      }

      if (form.$valid) {
        var formData = new FormData();
        formData.append("name", $scope.taskData.name);
        formData.append("userId", $scope.taskData.userId);
        formData.append("zip", $scope.taskData.file);
        formData.append("typeId", $scope.taskData.type);

        axios
          .post(`http://10.0.0.19:8080/index.php/task/AddTask`, formData, {
            headers: { "Content-Type": "multipart/form-data" },
            onUploadProgress: function (progressEvent) {
              if (progressEvent.lengthComputable) {
                $scope.uploadProgress = Math.round(
                  (progressEvent.loaded * 100) / progressEvent.total
                );
                $scope.$apply();
              }
            },
          })
          .then(function (res) {
            if (res.data.success) {
              $scope.successAddTaskMessage = res.data.message;
            } else {
              $scope.errorAddTaskMessage = res.data.message;
            }
            $scope.uploadProgress = 0;
            $scope.$apply();
          })
          .catch(function (error) {
            $scope.errorAddTaskMessage =
              error.response?.data?.message || "An error occurred";
            $scope.uploadProgress = 0;
            $scope.$apply();
          })
          .finally(function () {
            setTimeout(() => {
              $scope.$apply(() => {
                $scope.errorAddTaskMessage = "";
                $scope.successAddTaskMessage = "";
              });
            }, 3000);
          });
      } else {
        $scope.errorAddTaskMessage =
          "Form is invalid. Please check your inputs.";
      }
    };

    $scope.goToTask = function (item) {
      $location.path(`/task/${item.id}`);
    };
  },
]);

app.directive("zoomImage", function () {
  return {
    restrict: "A",
    link: function (scope, element) {
      const zoomImage = element[0];
      const zoomContainer = element.parent()[0];

      zoomContainer.style.overflow = "hidden";

      zoomImage.style.transition = "transform 0.2s ease";
      zoomImage.style.cursor = "zoom-in";

      zoomContainer.addEventListener("mousemove", function (e) {
        const { offsetWidth: width, offsetHeight: height } = zoomContainer;
        const { offsetX: x, offsetY: y } = e;

        const xPercent = (x / width) * 100;
        const yPercent = (y / height) * 100;

        zoomImage.style.transformOrigin = `${xPercent}% ${yPercent}%`;
      });

      zoomContainer.addEventListener("mouseenter", function () {
        zoomImage.style.transform = "scale(1.5)";
        zoomImage.style.cursor = "zoom-out";
      });

      zoomContainer.addEventListener("mouseleave", function () {
        zoomImage.style.transform = "scale(1)";
        zoomImage.style.cursor = "zoom-in";
      });
    },
  };
});

app.directive("fileModel", [
  "$parse",
  function ($parse) {
    return {
      restrict: "A",
      link: function (scope, element, attrs) {
        var model = $parse(attrs.fileModel);
        var modelSetter = model.assign;

        element.bind("change", function () {
          scope.$apply(function () {
            modelSetter(scope, element[0].files[0]);
          });
        });
      },
    };
  },
]);
