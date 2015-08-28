angular.module('undine.dashboard')
    .service('AppData', function ($window) {
        return $window.appData;
    });
