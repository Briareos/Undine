angular.module('undine.dashboard')
    .config(function ($locationProvider: ng.ILocationProvider, $stateProvider: ng.ui.IStateProvider, $urlRouterProvider: ng.ui.IUrlRouterProvider): void {
        $urlRouterProvider.otherwise('/');
        $locationProvider.html5Mode(true);
        /* tslint:disable:no-string-literal */
        $stateProvider
            .state('dashboard', {
                url: '/',
                controller: 'DashboardController',
                templateUrl: '/page/dashboard/dashboard.html',
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
                    Site: function (Dashboard: Dashboard, $stateParams: ng.ui.IStateParamsService): ISite {
                        return _.find(Dashboard.sites, {uid: $stateParams['uid']});
                    }
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
                data: {
                    sitePicker: {
                        visible: true
                    }
                }
            })
            .state('account', {
                url: '/account',
                controller: 'ProfileController',
                templateUrl: '/page/profile/profile.html'
            })
            .state('connectWebsite', {
                url: '/connect-website',
                templateUrl: '/page/connect-website/layout.html',
                abstract: true
            })
            .state('connectWebsite.url', {
                // Empty URL will make the state take the URL of the parent.
                url: '',
                controller: 'ConnectWebsiteUrlController',
                templateUrl: '/page/connect-website/url.html'
            })
            .state('connectWebsite.reconnect', {
                url: '/reconnect?url&lookedForLoginForm&loginFormFound',
                controller: 'ReconnectWebsiteController',
                templateUrl: '/page/connect-website/reconnect.html',
                resolve: {
                    url: function ($stateParams: ng.ui.IStateParamsService): string {
                        return decodeURIComponent($stateParams['url']);
                    },
                    lookedForLoginForm: function ($stateParams: ng.ui.IStateParamsService): boolean {
                        return $stateParams['lookedForLoginForm'] === 'true';
                    },
                    loginFormFound: function ($stateParams: ng.ui.IStateParamsService): boolean {
                        return $stateParams['loginFormFound'] === 'true';
                    }
                }
            })
            .state('connectWebsite.new', {
                url: '/new?url&lookedForLoginForm&loginFormFound',
                controller: 'ConnectWebsiteNewController',
                templateUrl: '/page/connect-website/new.html',
                resolve: {
                    url: function ($stateParams: ng.ui.IStateParamsService): string {
                        return decodeURIComponent($stateParams['url']);
                    },
                    lookedForLoginForm: function ($stateParams: ng.ui.IStateParamsService): boolean {
                        return $stateParams['lookedForLoginForm'] === 'true';
                    },
                    loginFormFound: function ($stateParams: ng.ui.IStateParamsService): boolean {
                        return $stateParams['loginFormFound'] === 'true';
                    }
                }
            })
            .state('connectWebsite.httpCredentials', {
                url: '/http-credentials',
                templateUrl: '/page/connect-website/http-credentials.html'
            })
            .state('connectWebsite.adminCredentials', {
                url: '/admin-credentials',
                controller: 'ConnectWebsiteAdminCredentialsController',
                templateUrl: '/page/connect-website/admin-credentials.html'
            })
            .state('connectWebsite.finish', {
                url: '/connected',
                templateUrl: '/page/connect-website/connected.html'
            })
        ;
        /* tslint:enable */
    });
