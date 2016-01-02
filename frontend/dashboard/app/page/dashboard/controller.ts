import {Component, View} from 'angular2/core';
import {RouterLink} from 'angular2/router';

@Component({
    selector: 'dashboard-controller'
})
@View({
    directives: [RouterLink],
    template: 'Foo is bar!'
})
export class DashboardController {
    constructor() {
    }
}
