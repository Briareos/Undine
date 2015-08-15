angular.module('undine.dashboard', ['undine.dashboard.template', 'ui.router'])
    .config(function ($httpProvider) {
        $httpProvider.interceptors.push('AuthenticationInterceptor');
    });
