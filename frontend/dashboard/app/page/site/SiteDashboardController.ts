import {Component, CORE_DIRECTIVES} from 'angular2/angular2';
import {RouteParams} from 'angular2/router';
import {ISite} from "../../api/model/site";
import {Dashboard} from "../../dashboard/Dashboard";

@Component({
    selector: 'site-dashboard-controller',
    directives: [CORE_DIRECTIVES],
    template: `
<h2>Site <em>{{ site.url }}</em></h2>

<h4>Core</h4>
<strong>Core v{{ site.state.drupalVersion }}</strong>

<h4>Modules</h4>
<ul>
    <li *ng-for="#module of site.modules">
        <strong>{{ module.name }}</strong>
        <blockquote>{{ module.description }}</blockquote>
    </li>
</ul>

<h4>Themes</h4>
<ul>
    <li *ng-for="#theme of site.themes">
        <strong>{{ theme.name }}</strong>
        <blockquote>{{ theme.description }}</blockquote>
    </li>
</ul>

<h3>Updates</h3>
<h4>Core ({{ site.coreUpdates.length }})</h4>
<ul>
    <li *ng-for="#update of site.coreUpdates">
        <strong>{{ update.existingVersion }}</strong>
        ->
        <strong>{{ update.recommendedVersion }}</strong>
    </li>
</ul>

<h4>Module ({{ site.moduleUpdates.length }})</h4>
<ul>
    <li *ng-for="#update of site.moduleUpdates">
        <strong>{{ update.existingVersion }}</strong>
        ->
        <strong>{{ update.recommendedVersion }}</strong>
    </li>
</ul>

<h4>Theme ({{ site.themeUpdates.length }})</h4>
<ul>
    <li *ng-for="#update of site.themeUpdates">
        <strong>{{ update.existingVersion }}</strong>
        ->
        <strong>{{ update.recommendedVersion }}</strong>
    </li>
</ul>

`
})
export class SiteDashboardController {
    private site: ISite;
    constructor(routeParams: RouteParams, dashboard: Dashboard) {
        this.site = _.find(dashboard.sites, {uid: routeParams.get('uid')});
    }
}
