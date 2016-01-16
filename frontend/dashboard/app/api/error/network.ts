import {AbstractError} from "./abstract_error";

export class NetworkCanNotConnect extends AbstractError {
}

export class NetworkCanNotResolveHost extends AbstractError {
}

export class ReceiveError extends AbstractError {
}

export class SendError extends AbstractError {
}

export class NetworkTimedOut extends AbstractError {
    get timeout(): number {
        return this.data.timeout;
    }
}
