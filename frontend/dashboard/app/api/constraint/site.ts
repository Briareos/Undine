import {Constraint} from "./constraint";

export class SiteCanNotInstallOxygen extends Constraint {
    public static STEP_LIST_MODULES: string = 'list_modules';
    public static STEP_SEARCH_UPDATE_MODULE: string = 'search_update_module';
    public static STEP_SEARCH_OXYGEN_MODULE: string = 'search_oxygen_module';

    get step(): string {
        return this.data.step;
    }
}

export class SiteInvalidCredentials extends Constraint {
}

export class SiteUrlInvalid extends Constraint {
}
