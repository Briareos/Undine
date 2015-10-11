class CanNotInstallOxygenConstraint extends Constraint {
    static STEP_LIST_MODULES = 'list_modules';
    static STEP_SEARCH_UPDATE_MODULE = 'search_update_module';
    static STEP_SEARCH_OXYGEN_MODULE = 'search_oxygen_module';

    get step():string {
        return this.data.step;
    }
}

class AlreadyConnectedConstraint extends Constraint {
    get lookedForLoginForm():boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound():boolean {
        return this.data.loginFormFound;
    }
}

class InvalidCredentialsConstraint extends Constraint {
}

class OxygenNotEnabledConstraint extends Constraint {
    get lookedForLoginForm():boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound():boolean {
        return this.data.loginFormFound;
    }
}

class FtpCredentialsRequiredConstraint extends Constraint {
}

class FtpCredentialsErrorConstraint extends Constraint {
    get ftpError():string {
        return this.data.ftpError;
    }
}

class HttpAuthenticationRequiredConstraint extends Constraint {
}

class HttpAuthenticationFailedConstraint extends Constraint {
}

class CanNotResolveHost extends Constraint {
}
