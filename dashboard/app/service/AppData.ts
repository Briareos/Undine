interface AppData {
    apiUrl: string;
    logoutUrl: string;
    currentUser: User;
}

angular.module('undine.dashboard')
    .service('AppData', function ($window) {
        return $window.appData;
    });
