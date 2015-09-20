angular.module('undine.dashboard')
    .run(function ($rootScope, $window) {
        $rootScope.currentUser = $window.appData.currentUser;
    })
    .run(function (Dashboard:Dashboard, $state:ng.ui.IStateService, $rootScope:ng.IRootScopeService) {
        // Listen for state change events.
        // If the state depends on site picker being visible and there are no sites added, redirect to add website state.
        $rootScope.$on('$stateChangeStart', function ($event:ng.IAngularEvent, toState:ng.ui.IState) {
            // temporarily disabled
            return;

            if (Dashboard.sites.length || toState.name === 'connectWebsite') {
                return;
            }

            if (_.isUndefined(toState.data) ||
                _.isUndefined(toState.data.sitePicker) ||
                _.isUndefined(toState.data.sitePicker.visible) || !toState.data.sitePicker.visible) {
                return;
            }

            $event.preventDefault();
            $state.go('connectWebsite.new');
        });
    });
