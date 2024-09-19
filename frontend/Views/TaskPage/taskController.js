var TaskController = angular.module("TaskController", []);

app.controller("taskController", [
  "$scope",
  "$routeParams",
  function ($scope, $routeParams) {
    $scope.location = "/tasks";
    $scope.tasks = [];
    $scope.taskData = [];
    $scope.labels = [];
    $scope.images = [];
    $scope.currentImageIndex = 0;
    $scope.selectedLabel = null;
    $scope.ActiveLabels = [];
    $scope.taskId = $routeParams.id;
    $scope.getTask = function () {
      axios
        .get(
          `http://10.0.0.19:8080/index.php/task/getSingleTask?id=${$scope.taskId}`
        )
        .then(function (res) {
          console.log(res);
          $scope.taskData = res.data.data;
          console.log($scope.taskData);
          // $scope.downloadTaskFile($scope.taskId);
          $scope.taskData.images.forEach((el) => {
              console.log(el.name);
              el.url = `http://10.0.0.19:8080/uploads/${el.name}`;
              el.label = "";
          });

          if ($scope.taskData.images) {
            $scope.taskData.images.forEach((image, index) => {
              if (image.labelId) {
                $scope.taskData.labels.forEach((label) => {
                  if (image.labelId === label.id) {
                    $scope.taskData.images[index].label = label;
                  }
                });
              }
            });
          }
          $scope.selectedLabel = $scope.taskData.images[$scope.currentImageIndex].label;
          
          $scope.$apply();
          console.log(res.data.data);
        })
        .catch(function (error) {
          console.log(error);
        });
    };

    $scope.nextImage = function () {
      if ($scope.currentImageIndex < $scope.taskData.images.length - 1) {
        $scope.currentImageIndex++;
        $scope.selectedLabel =
          $scope.taskData.images[$scope.currentImageIndex].label;
      }
    };

    $scope.prevImage = function () {
      if ($scope.currentImageIndex > 0) {
        $scope.currentImageIndex--;
        $scope.selectedLabel =
          $scope.taskData.images[$scope.currentImageIndex].label;
      }
    };

    $scope.labelClicked = function (label) {
      $scope.taskData.images[$scope.currentImageIndex].label = label;
      $scope.selectedLabel = label;
    };

    $scope.updateImageIndex = function (newIndex) {
      if (newIndex >= $scope.taskData.images.length) {
        console.log("newIndex", newIndex);
        $scope.currentImageIndex = $scope.taskData.images.length - 1;
        return;
      } else if (newIndex < 0) {
        console.log("newIndex", newIndex);
        $scope.currentImageIndex = 0;
        return;
      } else {
        $scope.selectedLabel =
          $scope.taskData.images[$scope.currentImageIndex].label;
      }
    };

    $scope.AssignLabelToImage = function (imageId, labelId) {
      axios
        .put(
          `http://10.0.0.19:8080/index.php/image/AssignLabelToImage?imageId=${imageId}&labelId=${labelId}`
        )
        .then(function (res) {
          console.log(res);
          if (res.data.success) {
            $scope.taskCompleted = true;
            $scope.$apply();
          }
        })
        .catch(function (error) {
          console.log(error);
        });
    };

    $scope.finish = function () {
      $scope.taskData.images.forEach((el) => {
        if (el.id && el.label.id) {
          $scope.AssignLabelToImage(el.id, el.label.id);
        }
      });
      $scope.taskCompleted = true;
      setTimeout(() => {
        $scope.$apply(() => {
          $scope.taskCompleted = false;
        });
      }, 3000);
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
