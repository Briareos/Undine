import {Component, ViewContainerRef} from 'angular2/angular2';
import {RouterLink} from 'angular2/router';

import {SitePickerSiteList} from './sitePickerSiteListDirective';
import {SitePicker} from '../../dashboard/SitePicker';
import {Dashboard} from '../../dashboard/Dashboard';
import {ISite} from '../../api/model/site';

@Component({
    selector: 'site-picker',
    directives: [SitePickerSiteList, RouterLink],
    template: `
<div class="site-picker">
    <div class="ui fluid dark large icon input margin-bottom-20">
        <input type="text" placeholder="Search sites">
        <i class="search icon"></i>
    </div>
    <a [router-link]="['/ConnectSite']" class="fluid large ui light gray button margin-bottom-20">
        <i class="plus icon"></i>
        Connect Website
    </a>
    <site-picker-site-list></site-picker-site-list>
</div>
`
})
export class SitePickerDirective {
    private sites: ISite[] = [];

    constructor(private _viewContainer: ViewContainerRef, dashboard: Dashboard) {
        let element = <HTMLElement>_viewContainer.element.nativeElement;
        element.style.display = 'block';
    }
}
