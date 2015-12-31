import {Constraint} from "./constraint";

export class FtpCredentialsError extends Constraint {
    get ftpError(): string {
        return this.data.ftpError;
    }
}

export class FtpCredentialsRequired extends Constraint {
}
