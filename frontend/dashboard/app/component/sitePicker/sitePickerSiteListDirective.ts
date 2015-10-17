angular.module('undine.dashboard')
    .directive('sitePickerSiteList', function (SitePicker: SitePicker): ng.IDirective {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sitePicker/sitePickerSiteList.html',
            link: function (scope: ng.IScope): void {
                SitePicker.subscribe(() => {
                    /* tslint:disable:no-string-literal */
                    scope['sites'] = SitePicker.filteredSites;
                    /* tslint:enable */
                });
            }
        };
    });
