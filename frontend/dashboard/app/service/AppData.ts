interface AppData {
    apiUrl: string
    logoutUrl: string
    currentUser: IUser
    oxygenZipUrl: string
}

angular.module('undine.dashboard')
    .service('AppData', function ($window) {
        return $window.appData;
    });
