var TypessController = angular.module("TypesController", []);

app.factory("TypesService", function () {
  const baseUrl = "http://localhost:8080/index.php";

  return {
    getAllTypesWithLabels: function ($scope) {
      const { searchQuery = "", itemsPerPage, currentPage } = $scope;
      return axios.get(
        `${baseUrl}/type/getAllTypesWithLabels?page=${currentPage}&limit=${itemsPerPage}&searchName=${searchQuery}`
      );
    },
    addTypeWithLabels: function (data) {
      return axios.post(`${baseUrl}/type/addTypeWithLabels`, data);
    },
    updateTypeWithLabels: function (data) {
      return axios.put(`${baseUrl}/type/updateTypeWithLabels`, data);
    },
    deleteLabel: function (id) {
      return axios.delete(`${baseUrl}/label/deleteLabel?id=${id}`);
    },
  };
});

app.controller("typesController", [
  "$scope",
  "TypesService",
  function ($scope, TypesService) {
    $scope.types = [];
    $scope.typesWithLabels = [];
    $scope.location = "/listTypes";
    $scope.errorAddTypeMessage = "";
    $scope.successAddTypeMessage = "";
    $scope.pendingSearchQuery = "";
    $scope.searchQuery = "";
    $scope.currentPage = 1;
    $scope.itemsPerPage = 10;
    $scope.selectedType = {};
    $scope.typeData = { id: "", name: "", labels: [] };
    $scope.rowsPerPageOptions = [10, 50, 100, 500, 1000, 2000];
    $scope.updateSuccessMessage = "";
    $scope.errorUpdateTypeMessage = "";

    $scope.$watch("itemsPerPage", function () {
      $scope.currentPage = 1;
      $scope.getTypes();
    });

    $scope.setPage = function (page) {
      if (page > 0 && page <= $scope.totalPages) {
        $scope.currentPage = page;
        $scope.getTypes();
      }
    };

    $scope.prevPage = function () {
      if ($scope.currentPage > 1) {
        $scope.currentPage--;
        $scope.getTypes();
      }
    };

    $scope.nextPage = function () {
      if ($scope.currentPage < $scope.totalPages) {
        $scope.currentPage++;
        $scope.getTypes();
      }
    };

    $scope.updateType = function (type) {
      $scope.selectedType = angular.copy(type);
      $scope.typeData.labels = $scope.selectedType.labels;
    };

    $scope.getTypes = function () {
      TypesService.getAllTypesWithLabels($scope)
        .then(function (res) {
          $scope.types = res.data.data;
          $scope.typesWithLabels = res.data.data;
          if (res.data.data) {
            $scope.totalPages = res.data.pagination.total_pages;
          }
          $scope.$apply();
        })
        .catch(function (error) {
          console.log(error);
        });
    };

    $scope.performSearch = function () {
      $scope.searchQuery = $scope.pendingSearchQuery;
      $scope.currentPage = 1;
      $scope.getTypes();
    };

    $scope.resetInput = function () {
      $scope.pendingSearchQuery = "";
      $scope.performSearch();
    };

    $scope.addLabel = function () {
      if ($scope.newLabel) {
        const labelExists = $scope.typeData.labels.some(
          (label) => label.title === $scope.newLabel
        );
        if (!labelExists) {
          $scope.typeData.labels.push({ title: $scope.newLabel });
        }
        $scope.newLabel = "";
      }
    };

    $scope.removeLabel = function (index, label) {
      $scope.typeData.labels.splice(index, 1);
      if (label.id) {
        $scope.deleteLabel(label.id);
      }
    };

    $scope.deleteLabel = function (id) {
      TypesService.deleteLabel(id)
        .then(function (res) {
          $scope.successDeleteLabelMessage = res.data.message;
        })
        .catch(function (error) {
          $scope.errorDeleteLabelMessage = error.response.data.message;
        });

      setTimeout(() => {
        $scope.successDeleteLabelMessage = "";
        $scope.errorDeleteLabelMessage = "";
      }, 3000);
    };

    $scope.submitTypeForm = function () {
      TypesService.addTypeWithLabels({
        name: $scope.typeData.name,
        labels: $scope.typeData.labels,
      })
        .then(function (res) {
          $scope.successAddTypeMessage = res.data.message;
          $scope.getTypes();
        })
        .catch(function (error) {
          $scope.errorAddTypeMessage = error.response.data.message;
        });

      setTimeout(() => {
        $scope.successAddTypeMessage = "";
        $scope.errorAddTypeMessage = "";
      }, 3000);
    };

    $scope.submitUpdateType = function () {
      const newLabels = $scope.typeData.labels.filter((label) => !label.id);
      TypesService.updateTypeWithLabels({
        id: $scope.selectedType.id,
        name: $scope.selectedType.name,
        labels: newLabels,
      })
        .then(function (res) {
          $scope.updateSuccessMessage = res.data.message;
          $scope.getTypes();
        })
        .catch(function (error) {
          $scope.errorUpdateTypeMessage =
            "Error updating type: " + error.message;
        });

      setTimeout(() => {
        $scope.updateSuccessMessage = "";
        $scope.errorUpdateTypeMessage = "";
      }, 3000);
    };

    $scope.getPageCount = function () {
      const filteredItems = $scope.typesWithLabels.filter((item) =>
        item.name.toLowerCase().includes($scope.searchQuery.toLowerCase())
      );
      return Math.ceil(filteredItems.length / $scope.itemsPerPage);
    };
  },
]);
