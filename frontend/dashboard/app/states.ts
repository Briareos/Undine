angular.module('undine.dashboard')
    .config(function ($locationProvider:ng.ILocationProvider, $stateProvider:ng.ui.IStateProvider, $urlRouterProvider:ng.ui.IUrlRouterProvider) {
        $urlRouterProvider.otherwise('/');
        $locationProvider.html5Mode(true);
        $stateProvider
            .state('dashboard', {
                abstract: true,
                url: '/',
                templateUrl: '/layout/dashboard.html'
            })
            .state('dashboard.dashboard', {
                url: '',
                controller: 'DashboardController',
                templateUrl: '/page/dashboard/dashboard.html'
            })
            .state('dashboard.module', {
                url: 'module',
                controller: 'ModuleController',
                templateUrl: '/page/module/module.html'
            })
            .state('dashboard.backup', {
                url: 'backup',
                controller: 'BackupController',
                templateUrl: '/page/backup/backup.html'
            })
            .state('dashboard.account', {
                url: 'account',
                controller: 'ProfileController',
                templateUrl: '/page/profile/profile.html'
            })
        ;
    });
