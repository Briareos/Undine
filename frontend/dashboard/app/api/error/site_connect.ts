import {AbstractError} from "./abstract_error";

export class SiteConnectAlreadyConnected extends AbstractError {
    get lookedForLoginForm(): boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound(): boolean {
        return this.data.loginFormFound;
    }
}

export class SiteConnectOxygenNotFound extends AbstractError {
    get lookedForLoginForm(): boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound(): boolean {
        return this.data.loginFormFound;
    }
}
