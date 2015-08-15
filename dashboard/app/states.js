angular.module('undine.dashboard')
    .config(function ($locationProvider, $stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise('/');
        $locationProvider.html5Mode(true);
        $stateProvider
            .state('dashboard', {
                abstract: true,
                url: '/',
                templateUrl: 'app/layout/dashboard.html'
            })
            .state('dashboard.dashboard', {
                url: '',
                controller: 'DashboardController',
                templateUrl: 'app/page/dashboard/dashboard.html'
            })
            .state('dashboard.module', {
                url: 'module',
                controller: 'ModuleController',
                templateUrl: 'app/page/module/module.html'
            })
            .state('dashboard.backup', {
                url: 'backup',
                controller: 'BackupController',
                templateUrl: 'app/page/backup/backup.html'
            })
            .state('dashboard.account', {
                url: 'account',
                controller: 'ProfileController',
                templateUrl: 'app/page/profile/profile.html'
            })
        ;
    });
