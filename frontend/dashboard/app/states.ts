angular.module('undine.dashboard')
    .config(function ($locationProvider:ng.ILocationProvider, $stateProvider:ng.ui.IStateProvider, $urlRouterProvider:ng.ui.IUrlRouterProvider) {
        $urlRouterProvider.otherwise('/');
        $locationProvider.html5Mode(true);
        $stateProvider
            .state('dashboard', {
                url: '/',
                controller: 'DashboardController',
                templateUrl: '/page/dashboard/dashboard.html',
                ncyBreadcrumb: {
                    label: 'Dashboard'
                },
                data: {
                    sitePicker: {
                        visible: true
                    }
                }
            })
            .state('module', {
                url: '/module',
                controller: 'ModuleController',
                templateUrl: '/page/module/module.html',
                ncyBreadcrumb: {
                    label: 'Module'
                },
                data: {
                    sitePicker: {
                        visible: true
                    }
                }
            })
            .state('backup', {
                url: '/backup',
                controller: 'BackupController',
                templateUrl: '/page/backup/backup.html',
                ncyBreadcrumb: {
                    label: 'Backup'
                },
                data: {
                    sitePicker: {
                        visible: true
                    }
                }
            })
            .state('account', {
                url: '/account',
                controller: 'ProfileController',
                templateUrl: '/page/profile/profile.html',
                ncyBreadcrumb: {
                    label: 'Account'
                }
            })
            .state('add-website', {
                url: '/add-website',
                templateUrl: '/page/add-website/add-website.html',
                ncyBreadcrumb: {
                    label: 'Add Website'
                }
            });
    });
