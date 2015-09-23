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
                    case 'site.invalid_credentials':
                        response.data = new InvalidCredentialsConstraint(response.data);
                        break;
                    case 'site.oxygen_not_enabled':
                        response.data = new OxygenNotEnabledConstraint(response.data);
                        break;
                    case 'site.ftp_credentials_required':
                        response.data = new FtpCredentialsRequiredConstraint(response.data);
                        break;
                    case 'site.ftp_credentials_error':
                        response.data = new FtpCredentialsErrorConstraint(response.data);
                        break;
                    case 'site.can_not_install_oxygen':
                        response.data = new CanNotInstallOxygenConstraint(response.data);
                        break;
                    case 'site.http_authentication_required':
                        response.data = new HttpAuthenticationRequiredConstraint(response.data);
                        break;
                    case 'site.http_authentication_failed':
                        response.data = new HttpAuthenticationFailedConstraint(response.data);
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
