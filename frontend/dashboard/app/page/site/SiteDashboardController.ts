import {Component} from 'angular2/core';
import {CORE_DIRECTIVES} from 'angular2/common';
import {RouteParams} from 'angular2/router';
import {ISite} from "../../api/model/site";
import {State} from "../../dashboard/state";
import {Api} from "../../service/Api";
import {Router} from "angular2/router";

@Component({
    selector: 'site-dashboard-controller',
    directives: [CORE_DIRECTIVES],
    template: `
<h2>Site <em>{{ site.url }}</em></h2>

<button class="ui negative button" [class.loading]="removing" [class.disabled]="removing" type="button" (click)="remove()">Remove</button>

<h4>Core</h4>
<strong>Core v{{ site.state.drupalVersion }}</strong>

<h4>Modules</h4>
<ul>
    <li *ngFor="#module of site.state.modules">
        <strong>{{ module.name }} <i [class]="module.enabled ? 'toggle off icon' : 'toggle on icon'"></i></strong>
        <blockquote>{{ module.description }}</blockquote>
    </li>
</ul>

<h4>Themes</h4>
<ul>
    <li *ngFor="#theme of site.state.themes">
        <strong>{{ theme.name }}</strong>
        <blockquote>{{ theme.description }}</blockquote>
    </li>
</ul>

<h3>Updates</h3>
<h4>Core ({{ site.state.coreUpdates.length }})</h4>
<ul>
    <li *ngFor="#update of site.state.coreUpdates">
        <strong>{{ update.existingVersion }}</strong>
        ->
        <strong>{{ update.recommendedVersion }}</strong>
    </li>
</ul>

<h4>Module ({{ site.state.moduleUpdates.length }})</h4>
<ul>
    <li *ngFor="#update of site.state.moduleUpdates">
        <strong>{{ update.existingVersion }}</strong>
        ->
        <strong>{{ update.recommendedVersion }}</strong>
    </li>
</ul>

<h4>Theme ({{ site.state.themeUpdates.length }})</h4>
<ul>
    <li *ngFor="#update of site.state.themeUpdates">
        <strong>{{ update.existingVersion }}</strong>
        ->
        <strong>{{ update.recommendedVersion }}</strong>
    </li>
</ul>

`
})
export class SiteDashboardController {
    private site: ISite;
    private removing: boolean = false;

    constructor(routeParams: RouteParams, private state: State, private api: Api, private router: Router) {
        let id: string = routeParams.get('id');
        this.site = state.user.sites.find(site => site.id === id);
    }

    private remove() {
        this.removing = true;
        this.state.removeSite(this.site);
        this.router.navigate(['/Dashboard']);
        this.api.siteDisconnect(this.site.id)
            .result.subscribe(
            null,
            ()=> {
                // The removal failed; return it to the list.
                this.state.addSite(this.site);
            })
            .finally(()=>this.removing = false);
    }
}
