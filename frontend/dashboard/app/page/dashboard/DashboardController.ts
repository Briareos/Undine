angular.module('undine.dashboard')
    .controller('DashboardController', function (Api:Api, $scope) {
        //Api.siteConnect('http://alpha.drupal.localhost')
        //    .then(function (data:SiteConnectResult) {
        //        console.log(data);
        //    });
        $scope.foo = 'bar';
    });
