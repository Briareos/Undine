angular.module('undine.dashboard')
.controller('SiteDashboardController', function($scope:ng.IScope, Site:Site, SiteDashboardFactory:SiteDashboardFactory){
    $scope['siteDashboard'] = SiteDashboardFactory.create(Site);
});
