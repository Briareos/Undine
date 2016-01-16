import {AbstractError} from "./abstract_error";

export class ResponseOxygenNotFound extends AbstractError {
}

export class ResponseUnauthorized extends AbstractError {
    get realm(): string {
        return this.data.realm;
    }

    get hasCredentials(): boolean {
        return this.data.hasCredentials;
    }
}
