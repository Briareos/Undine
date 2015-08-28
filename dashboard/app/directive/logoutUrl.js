angular.module('undine.dashboard')
    .directive('logoutUrl', function (AppData) {
        return {
            restrict: 'A',
            link: function(scope, element) {
                element[0].href = AppData.logoutUrl;
            }
        }
    });
