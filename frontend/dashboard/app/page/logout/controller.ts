import {Inject, Component} from 'angular2/core';

@Component({
    selector: 'logout-controller',
    template: 'Logging you out of your websites... (not really)'
})
export class LogoutController {
    constructor(@Inject('LOGOUT_URL') logoutUrl: string) {
        // @todo: Figure out the "Angular" way to do this.
        window.location.href = logoutUrl;
    }
}
