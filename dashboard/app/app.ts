angular.module('undine.dashboard', ['undine.dashboard.template', 'ui.router'])
    .config(function ($httpProvider:ng.IHttpProvider) {
        $httpProvider.interceptors.push('AuthenticationInterceptor');
    });
