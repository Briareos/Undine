angular.module('undine.dashboard')
    .directive('sitePicker', function (SitePicker: SitePicker, Dashboard: Dashboard) {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sitePicker/sitePicker.html',
            link: function (scope: ng.IScope, $element: ng.IAugmentedJQuery) {
                /* tslint:disable:no-string-literal */
                scope['sites'] = Dashboard.sites;
                /* tslint:enable */
                scope.$watch(
                    function (): boolean {
                        return SitePicker.visible;
                    },
                    function (): void {
                        if (SitePicker.visible) {
                            $element.show();
                        } else {
                            $element.hide();
                        }
                    }
                );
            }
        };
    });
