import {Constraint} from "./constraint";

export class SiteCanNotInstallOxygen extends Constraint {
    public static STEP_LIST_MODULES: string = 'list_modules';
    public static STEP_SEARCH_UPDATE_MODULE: string = 'search_update_module';
    public static STEP_SEARCH_OXYGEN_MODULE: string = 'search_oxygen_module';

    get step(): string {
        return this.data.step;
    }
}

export class SiteAlreadyConnected extends Constraint {
    get lookedForLoginForm(): boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound(): boolean {
        return this.data.loginFormFound;
    }
}

export class SiteInvalidCredentials extends Constraint {
}

export class SiteUrlInvalid extends Constraint {
}

export class SiteOxygenNotEnabled extends Constraint {
    get lookedForLoginForm(): boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound(): boolean {
        return this.data.loginFormFound;
    }
}

export class SiteFtpCredentialsRequired extends Constraint {
}

export class SiteFtpCredentialsError extends Constraint {
    get ftpError(): string {
        return this.data.ftpError;
    }
}

export class SiteHttpAuthenticationRequired extends Constraint {
}

export class SiteHttpAuthenticationFailed extends Constraint {
}

export class SiteCanNotResolveHost extends Constraint {
}
