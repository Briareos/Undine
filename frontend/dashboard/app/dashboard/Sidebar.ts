import {Component} from 'angular2/angular2';
import {RouterLink} from 'angular2/router';

@Component({
    selector: 'sidebar',
    directives: [RouterLink],
    template: `
        <div class="sidebar">
            <div class="tools">
                <a ui-sref="dashboard" ui-sref-active class="tool">
                    <i class="dashboard icon"></i>
                    Dashboard
                </a>
                <a ui-sref="client-report" ui-sref-active class="tool">
                    <i class="calendar outline icon"></i>
                    Client Report
                </a>
                <a ui-sref="modules" ui-sref-active class="tool">
                    <i class="cubes icon"></i>
                    Modules
                </a>
                <a [router-link]="['/ConnectSiteUrl']" class="tool">
                    <i class="plus icon"></i>
                    Connect Website
                </a>
            </div>
        </div>`
})
export class Sidebar {

}
