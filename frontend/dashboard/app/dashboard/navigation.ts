import {Component} from 'angular2/core';
import {RouterLink} from 'angular2/router';

@Component({
    selector: 'navigation',
    directives: [RouterLink],
    template: `
        <ul>
            <li>
            <a [routerLink]="['/Dashboard']" class="tool">
                <i class="dashboard icon"></i>
                Dashboard
            </a>
            </li>
            <li>
            <a>
                <i class="calendar outline icon"></i>
                Client Report
            </a>
            </li>
            <li>
            <a>
                <i class="cubes icon"></i>
                Modules
            </a>
            </li>
            <li>
            <a [routerLink]="['/ConnectSite']">
                <i class="plus icon"></i>
                Connect Website
            </a>
            </li>
        </ul>`
})
export class Navigation {

}
