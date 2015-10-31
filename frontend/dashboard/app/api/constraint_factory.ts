import * as Constraint from '../api/constraint';

export default class ConstraintFactory {
    public static createConstraint(name: string, data: any): Constraint.IConstraint {
        switch (name) {

            // Site constraints.
            case 'site.already_connected':
                return new Constraint.SiteAlreadyConnected(data);
            case 'site.invalid_credentials':
                return new Constraint.SiteInvalidCredentials(data);
            case 'site.oxygen_not_enabled':
                return new Constraint.SiteOxygenNotEnabled(data);
            case 'site.ftp_credentials_required':
                return new Constraint.SiteFtpCredentialsRequired(data);
            case 'site.ftp_credentials_error':
                return new Constraint.SiteFtpCredentialsError(data);
            case 'site.can_not_install_oxygen':
                return new Constraint.SiteCanNotInstallOxygen(data);
            case 'site.http_authentication_required':
                return new Constraint.SiteHttpAuthenticationRequired(data);
            case 'site.http_authentication_failed':
                return new Constraint.SiteHttpAuthenticationFailed(data);
            case 'site.can_not_resolve_host':
                return new Constraint.SiteCanNotResolveHost(data);
            case 'site.url_invalid':
                return new Constraint.SiteUrlInvalid(data);

            // Security constraints.
            case 'security.not_authenticated':
                return new Constraint.SecurityNotAuthenticated(data);
            case 'security.not_authorized':
                return new Constraint.SecurityNotAuthorized(data);
            default:
                return new Constraint.Constraint(data);
        }
    }
}
