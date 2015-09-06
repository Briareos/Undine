angular.module('undine.dashboard')
    .directive('sitePicker', function (SitePicker:SitePicker) {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sitePicker/sitePicker.html',
            link: function (scope:ng.IScope, $element:ng.IAugmentedJQuery) {
                scope.$watch(function () {
                    return SitePicker.visible;
                }, function () {
                    if (SitePicker.visible) {
                        $element.show();
                    } else {
                        $element.hide();
                    }
                });
            }
        };
    });