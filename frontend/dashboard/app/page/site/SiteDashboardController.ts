import {Component} from 'angular2/core';
import {CORE_DIRECTIVES} from 'angular2/common';
import {RouteParams} from 'angular2/router';
import {ISite} from "../../api/model/site";
import {State} from "../../dashboard/state";

@Component({
    selector: 'site-dashboard-controller',
    directives: [CORE_DIRECTIVES],
    template: `
<h2>Site <em>{{ site.url }}</em></h2>

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

    constructor(routeParams: RouteParams, state: State) {
        let id: string = routeParams.get('id');
        this.site = state.user.sites.find(site => site.id === id);
    }
}
