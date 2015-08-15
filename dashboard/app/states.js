angular.module('undine.dashboard')
    .config(function ($locationProvider, $stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise('/');
        $locationProvider.html5Mode(true);
        $stateProvider
            .state('endorse', {
                abstract: true,
                url: '/',
                templateUrl: 'layout/endorse.html'
            })
            .state('endorse.feed', {
                url: '',
                controller: 'FeedController',
                templateUrl: 'page/feed/feed.html'
            })
            .state('endorse.statistic', {
                url: 'statistic',
                controller: 'StatisticController',
                templateUrl: 'page/statistic/statistic.html'
            })
            .state('endorse.profileView', {
                url: 'profile/:uid',
                controller: 'ProfileController',
                templateUrl: 'page/profile/profile.html',
                resolve: {
                    User: function (UserRepository, $stateParams) {
                        return UserRepository.getByUid($stateParams.uid);
                    }
                }
            })
            .state('endorse.profile', {
                url: 'profile',
                controller: 'ProfileController',
                templateUrl: 'page/profile/profile.html',
                resolve: {
                    User: function (Session) {
                        return Session.user;
                    }
                }
            })
        ;
    });
