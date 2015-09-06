angular.module('undine.dashboard')
    .run(function ($rootScope, $window) {
        $rootScope.currentUser = $window.appData.currentUser;
    });
