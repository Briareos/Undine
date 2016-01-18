import {AbstractError} from "./abstract_error";

export enum DrupalClientCanNotInstallOxygenStep {
    STEP_LIST_MODULES,
    STEP_SEARCH_UPDATE_MODULE,
    STEP_SEARCH_OXYGEN_MODULE,
}

export class DrupalClientCanNotInstallOxygen extends AbstractError {
    get step(): DrupalClientCanNotInstallOxygenStep {
        return <any>DrupalClientCanNotInstallOxygenStep[this.data.step.toUpperCase()];
    }
}

export class DrupalClientInvalidCredentials extends AbstractError {
}

export class DrupalClientOxygenPageNotFound extends AbstractError {
}
