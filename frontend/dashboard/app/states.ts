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
        .state('siteDashboard', {
        url: '/site/{uid}/dashboard',
        controller: 'SiteDashboardController',
        templateUrl: '/page/site/dashboard.html',
        resolve: {
            Site: function (Dashboard:Dashboard, $stateParams:ng.ui.IStateParamsService) {
                return _.find(Dashboard.sites, {uid: $stateParams['uid']});
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
        .state('connectWebsite', {
        url: '/connect-website',
        templateUrl: '/page/connect-website/layout.html',
        ncyBreadcrumb: {
            label: 'Connect Website'
        },
        abstract: true
    })
        .state('connectWebsite.new', {
        // Empty URL will make the state take the URL of the parent.
        url: '',
        controller: 'ConnectWebsiteNewController',
        templateUrl: '/page/connect-website/new.html',
    })
        .state('connectWebsite.instructions', {
        url: '/instructions?url',
        controller: 'ConnectWebsiteInstructionsController',
        templateUrl: '/page/connect-website/instructions.html',
        resolve: {
            url: function ($state:ng.ui.IStateService, $stateParams:ng.ui.IStateParamsService) {
                return decodeURIComponent($stateParams['url']);
            }
        }
    })
        .state('connectWebsite.httpCredentials', {
        url: '/http-credentials',
        templateUrl: '/page/connect-website/http-credentials.html',
    })
        .state('connectWebsite.userCredentials', {
        url: '/user-credentials',
        templateUrl: '/page/connect-website/user-credentials.html',
    })
        .state('connectWebsite.finish', {
        url: '/connected',
        templateUrl: '/page/connect-website/connected.html',
    })
    ;
});
