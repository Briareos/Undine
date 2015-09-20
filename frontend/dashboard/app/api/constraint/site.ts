class CanNotInstallOxygenConstraint extends Constraint {
    static STEP_LIST_MODULES = 'list_modules';
    static STEP_SEARCH_UPDATE_MODULE = 'search_update_module';
    static STEP_SEARCH_OXYGEN_MODULE = 'search_oxygen_module';

    // One of CanNotInstallOxygenConstraintStep.
    get step():string {
        return this.data.step;
    }
}

class AlreadyConnectedConstraint extends Constraint {
    get lookedForLoginForm():boolean {
        return this.data.lookedForLoginForm;
    }

    get loginFormFound():boolean {
        return this.data.loginFormFound;
    }
}
