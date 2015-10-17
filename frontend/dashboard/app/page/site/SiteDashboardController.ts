angular.module('undine.dashboard')
.controller('SiteDashboardController', function($scope:ng.IScope, Site:ISite, SiteDashboardFactory:ISiteDashboardFactory){
    $scope['siteDashboard'] = SiteDashboardFactory.create(Site);
});
