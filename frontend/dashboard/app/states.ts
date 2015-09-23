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
                },
                nycBreadcrumb: {
                    label: 'Site Dashboard'
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
            .state('connectWebsite', {
                url: '/connect-website',
                templateUrl: '/page/connect-website/layout.html',
                ncyBreadcrumb: {
                    label: 'Connect Website'
                },
                abstract: true
            })
            .state('connectWebsite.url', {
                // Empty URL will make the state take the URL of the parent.
                url: '',
                controller: 'ConnectWebsiteUrlController',
                templateUrl: '/page/connect-website/url.html',
                ncyBreadcrumb: {
                    label: 'Connect Website'
                }
            })
            .state('connectWebsite.reconnect', {
                url: '/reconnect?url&lookedForLoginForm&loginFormFound',
                controller: 'ReconnectWebsiteController',
                templateUrl: '/page/connect-website/reconnect.html',
                resolve: {
                    url: function ($stateParams:ng.ui.IStateParamsService) {
                        return decodeURIComponent($stateParams['url']);
                    },
                    lookedForLoginForm: function ($stateParams:ng.ui.IStateParamsService) {
                        return $stateParams['lookedForLoginForm'] === 'true';
                    },
                    loginFormFound: function ($stateParams:ng.ui.IStateParamsService) {
                        return $stateParams['loginFormFound'] === 'true';
                    }
                }
            })
            .state('connectWebsite.new', {
                url: '/new?url&lookedForLoginForm&loginFormFound',
                controller: 'ConnectWebsiteNewController',
                templateUrl: '/page/connect-website/new.html',
                resolve: {
                    url: function ($stateParams:ng.ui.IStateParamsService) {
                        return decodeURIComponent($stateParams['url']);
                    },
                    lookedForLoginForm: function ($stateParams:ng.ui.IStateParamsService) {
                        return $stateParams['lookedForLoginForm'] === 'true';
                    },
                    loginFormFound: function ($stateParams:ng.ui.IStateParamsService) {
                        return $stateParams['loginFormFound'] === 'true';
                    }
                },
                ncyBreadcrumb: {
                    label: 'Connect Website'
                }
            })
            .state('connectWebsite.httpCredentials', {
                url: '/http-credentials',
                templateUrl: '/page/connect-website/http-credentials.html',
                ncyBreadcrumb: {
                    label: 'Connect Website'
                }
            })
            .state('connectWebsite.adminCredentials', {
                url: '/admin-credentials',
                controller: 'ConnectWebsiteAdminCredentialsController',
                templateUrl: '/page/connect-website/admin-credentials.html',
                ncyBreadcrumb: {
                    label: 'Connect Website'
                }
            })
            .state('connectWebsite.finish', {
                url: '/connected',
                templateUrl: '/page/connect-website/connected.html',
                ncyBreadcrumb: {
                    label: 'Connect Website'
                }
            })
        ;
    });
