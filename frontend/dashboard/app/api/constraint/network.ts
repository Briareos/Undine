import {Constraint} from "./constraint";

export class NetworkCanNotResolveHost extends Constraint {
}

export class NetworkError extends Constraint {
    get code(): number {
        return this.data.code;
    }

    get message(): string {
        return this.data.message;
    }
}

export class NetworkTimeout extends Constraint {
    get totalTime(): number {
        return this.data.totalTime;
    }
}
