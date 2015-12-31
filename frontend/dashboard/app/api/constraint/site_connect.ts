import {Constraint} from "./constraint";

export class SiteConnectAlreadyConnected extends Constraint {
    get lookedForLoginForm(): boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound(): boolean {
        return this.data.loginFormFound;
    }
}

export class SiteConnectOxygenNotFound extends Constraint {
    get lookedForLoginForm(): boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound(): boolean {
        return this.data.loginFormFound;
    }
}
