/**
 * If the user ever gets a "not authenticated" error, it means his session has expired.
 * That case should be handled here. For now, reload the page and let the backend handle it.
 */
angular.module('undine.dashboard')
    .service('ConstraintInterceptor', function ($q:ng.IQService) {
    // https://docs.angularjs.org/api/ng/service/$http#interceptors
    return {
        response: function (response) {
            if (response.data.ok === false) {
                switch (response.data.error) {
                    case 'site.already_connected':
                        response.data = new AlreadyConnectedConstraint(response.data);
                        break;
                    default:
                        response.data = new Constraint(response.data);
                }
                return $q.reject(response);
            }
            return response;
        }
    };
});
