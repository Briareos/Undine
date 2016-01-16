import {AbstractError} from "./abstract_error";

export enum DrupalClientCanNotInstallOxygenStep {
    STEP_LIST_MODULES,
    STEP_SEARCH_UPDATE_MODULE,
    STEP_SEARCH_OXYGEN_MODULE,
}

export class DrupalClientCanNotInstallOxygen extends AbstractError {
    get step(): number {
        return DrupalClientCanNotInstallOxygenStep[this.data.step.toUpperCase()];
    }
}

let a = new DrupalClientCanNotInstallOxygen({step: 'nema_ga'});

if (a.step === DrupalClientCanNotInstallOxygenStep.STEP_LIST_MODULES) {
    alert('majko moja');
}

export class DrupalClientInvalidCredentials extends AbstractError {
}

export class DrupalClientOxygenPageNotFound extends AbstractError {
}
