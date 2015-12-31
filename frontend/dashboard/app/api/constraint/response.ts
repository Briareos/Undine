import {Constraint} from "./constraint";

export class ResponseUnauthorized extends Constraint {
    get hasCredentials():boolean {
        return this.data.hasCredentials;
    }
}

export class ResponseOxygenNotFound extends Constraint {
}
