angular.module('undine.dashboard', ['undine.dashboard.template', 'ui.router', 'ncy-angular-breadcrumb'])
    .config(function ($httpProvider:ng.IHttpProvider) {
        $httpProvider.interceptors.push('AuthenticationInterceptor');
    })
    .config(function ($breadcrumbProvider) {
        $breadcrumbProvider.setOptions({
            templateUrl: '/component/breadcrumb/breadcrumb.html'
        });
    });
