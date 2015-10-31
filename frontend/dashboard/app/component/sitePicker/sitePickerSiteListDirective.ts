import {Component, Injectable, CORE_DIRECTIVES} from 'angular2/angular2';
import {RouterLink} from 'angular2/router';
import {ISite} from '../../api/model/site';
import {SitePicker} from '../../dashboard/SitePicker';
import {StripUrlPipe} from "../../filters/strip_url";

@Component({
    selector: 'site-picker-site-list',
    directives: [CORE_DIRECTIVES, RouterLink],
    pipes: [StripUrlPipe],
    template: `
<section class="site-picker-site-list">
    <div class="ui site list">
        <div class="item site-item" *ng-for="#site of sites">
            <a class="site-name" [router-link]="['/SiteDashboard', {uid: site.uid}]">
                <span class="status">
                    <span class="site status connected"></span>
                </span>

                <span class="name">
                    {{ site.url | stripUrl }}
                </span>
            </a>
        </div>
    </div>
</section>
`
})
@Injectable()
export class SitePickerSiteList {
    private sites: ISite[];

    constructor(sitePicker: SitePicker) {
        this.sites = sitePicker.filteredSites;
    }
}
