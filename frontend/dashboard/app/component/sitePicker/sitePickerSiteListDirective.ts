angular.module('undine.dashboard')
    .directive('sitePickerSiteList', function (SitePicker:SitePicker) {
        return {
            scope: true,
            replace: true,
            templateUrl: '/component/sitePicker/sitePickerSiteList.html',
            link: function (scope:ng.IScope) {
                SitePicker.subscribe(() => {
                    scope['sites'] = SitePicker.filteredSites;
                })
            }
        };
    });
