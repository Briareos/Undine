import {AbstractError} from "./abstract_error";

export class ApiUnexpectedError extends AbstractError {
}

export class ApiBadRequest extends AbstractError {
    get message():string {
        return this.data.message;
    }

    get parameter(): string|void {
        return this.data.parameter;
    }
}
