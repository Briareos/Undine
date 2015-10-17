/**
 * If the user ever gets a "not authenticated" error, it means his session has expired.
 * That case should be handled here. For now, reload the page and let the backend handle it.
 */
angular.module('undine.dashboard')
    .service('ConstraintInterceptor', function ($q: ng.IQService): any {
        function getConstraint(name: string, data: any): Constraint {
            switch (name) {
                case 'site.already_connected':
                    return new AlreadyConnectedConstraint(data);
                case 'site.invalid_credentials':
                    return new InvalidCredentialsConstraint(data);
                case 'site.oxygen_not_enabled':
                    return new OxygenNotEnabledConstraint(data);
                case 'site.ftp_credentials_required':
                    return new FtpCredentialsRequiredConstraint(data);
                case 'site.ftp_credentials_error':
                    return new FtpCredentialsErrorConstraint(data);
                case 'site.can_not_install_oxygen':
                    return new CanNotInstallOxygenConstraint(data);
                case 'site.http_authentication_required':
                    return new HttpAuthenticationRequiredConstraint(data);
                case 'site.http_authentication_failed':
                    return new HttpAuthenticationFailedConstraint(data);
                case 'site.can_not_resolve_host':
                    return new CanNotResolveHost(data);
                default:
                    return new Constraint(data);
            }
        }

        // https://docs.angularjs.org/api/ng/service/$http#interceptors
        return {
            response: function (response: ng.IHttpPromiseCallbackArg<any>): any {
                if (response.data.ok === false) {
                    response.data = getConstraint(response.data.error, response.data);

                    return $q.reject(response);
                }
                return response;
            }
        };
    });
