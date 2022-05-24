(function(angular, $, _) {
  var moduleName = "taskRunner";
  var moduleDependencies = ["ngRoute"];
  angular.module(moduleName, moduleDependencies);

  angular.module(moduleName).config([
    "$routeProvider",
    function($routeProvider) {
      $routeProvider.when("/sqltasks/run/:tid/:input_value?", {
        controller: "taskRunnerCtrl",
        templateUrl: "~/taskRunner/taskRunner.html",
        resolve: {
          taskId: function($route) {
            return $route.current.params.tid;
          },
          inputValue: function($route) {
            return $route.current.params.input_value;
          },
        }
      });
    }
  ]);

  angular.module(moduleName).controller("taskRunnerCtrl", function($scope, $location, taskId, inputValue) {
    $scope.taskId = taskId;
    $scope.ts = CRM.ts();
    $scope.resultLogs = [];
    $scope.isTaskReturnsEmptyLogs = false;
    $scope.isShowLogs = false;
    $scope.isTaskRunning = false;
    $scope.runButtonText = $scope.ts('Run task');
    $scope.inputValue = inputValue;

    $scope.runTask = function() {
      CRM.alert("Task execution has started", "Task execution", 'info');
      $scope.isTaskRunning = true;

      CRM.api3("Sqltask", "execute", {
        task_id: taskId,
        input_val: inputValue === undefined ? 0 : inputValue,
      }).done(function(result) {
        if (result.values && !result.is_error) {
          if (result.values.log !== undefined && Array.isArray(result.values.log)) {
            $scope.resultLogs = result.values.log;
            $scope.isTaskReturnsEmptyLogs = $scope.resultLogs.length  === 0;
          } else {
            $scope.isTaskReturnsEmptyLogs = true;
          }
          $scope.isTaskRunning = false;
          $scope.isShowLogs = true;
          CRM.alert("Task execution completed", "Task execution", 'success');
        } else {
          CRM.alert(result.error_message, ts("Error task execution"), "error");
          $scope.resultLogs = [ts('Task returns error: ') + result.error_message];
          $scope.isTaskRunning = false;
          $scope.isShowLogs = true;
        }
        $scope.runButtonText = $scope.ts('Run again');
        $scope.$apply();
      }).fail(function() {
        $scope.runButtonText = $scope.ts('Run again');
        $scope.resultLogs = ["An unknown error occurred during task execution. Please check your server logs for details before proceeding."];
        $scope.isTaskRunning = false;
        $scope.isShowLogs = true;
        $scope.$apply();
        CRM.alert("Task failed to execute", "Task execution", 'error');
      });
    };

    if (window.waitSqlTaskId === taskId) {
      window.waitSqlTaskId = null;
      $scope.runTask();
    }

  });
})(angular, CRM.$, CRM._);
