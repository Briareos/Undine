/**
 * If the user ever gets a "not authenticated" error, it means his session has expired.
 * That case should be handled here. For now, reload the page and let the backend handle it.
 */
angular.module('undine.dashboard')
    .service('AuthenticationInterceptor', function ($q, $window) {
        // https://docs.angularjs.org/api/ng/service/$http#interceptors
        return {
            response: function (response) {
                if (!response.data.ok && response.data.error === 'security.not_authenticated') {
                    // The "not logged in" handler should be placed here. For now just reload the page.
                    $window.location.reload();

                    // This promise will never be resolved. That hits close to home.
                    return $q.defer().promise;
                }
                return response;
            }
        };
    });
