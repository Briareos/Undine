import {Constraint} from "./constraint";

export enum DrupalClientCanNotInstallOxygenStep {
    STEP_LIST_MODULES,
    STEP_SEARCH_UPDATE_MODULE,
    STEP_SEARCH_OXYGEN_MODULE,
}

export class DrupalClientCanNotInstallOxygen extends Constraint {
    get step(): DrupalClientCanNotInstallOxygen {
        return DrupalClientCanNotInstallOxygen[this.data.step.toUpperCase()];
    }
}

export class DrupalClientInvalidCredentials extends Constraint {
}

export class DrupalClientOxygenPageNotFound extends Constraint {
}
