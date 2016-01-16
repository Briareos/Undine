import {AbstractError} from "./abstract_error";

export class FtpCredentialsError extends AbstractError {
    get ftpError(): string {
        return this.data.ftpError;
    }
}

export class FtpCredentialsRequired extends AbstractError {
}
