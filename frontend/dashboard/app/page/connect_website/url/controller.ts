import {Component} from 'angular2/core';
import {ControlGroup, Control, CORE_DIRECTIVES, FORM_DIRECTIVES} from 'angular2/common';
import {Router} from 'angular2/router';

import * as ApiError from '../../../api/errors';
import * as Result from '../../../api/result';
import * as Progress from '../../../api/progress';
import {Api} from '../../../service/Api';
import {ConnectWebsiteSession} from '../../../service/ConnectWebsiteSession';
import {State} from "../../../dashboard/state";

@Component({
    selector: 'connect-website-url-controller',
    directives: [CORE_DIRECTIVES, FORM_DIRECTIVES],
    template: `
<div class="ui two column grid">
    <div class="column">
        <div class="ui basic segment">
            <div class="ui inverted dimmer" [ngClass]="{active:loading}">
                <div class="ui medium text loader">{{ progress }}</div>
            </div>
            <form class="ui form" [ngFormModel]="form" (submit)="submit(form.value)">
                <div class="field" *ngIf="!httpAuthenticationRequired">
                    <p>Enter the URL of the website you would like to connect to the dashboard.</p>
                    <div class="field">
                        <label for="site-url">Website URL</label>
                        <input required type="text" id="site-url" placeholder="http://example.com" [ngFormControl]="form.controls['url']" autofocus>
                    </div>
                    <div class="ui negative message" *ngIf="errors.canNotResolveHost">
                        <p>Could not resolve the website host. Are you sure the site exists?</p>
                    </div>
                    <div class="ui negative message" *ngIf="errors.invalidUrl">
                        <p>The provided URL does not seem to be valid.</p>
                    </div>
                </div>
                <div class="field" *ngIf="httpAuthenticationRequired">
                    <p>The site specified uses HTTP authentication. Please enter the HTTP credentials below:</p>
                    <div class="field">
                        <label for="url-http-username">HTTP Username</label>
                        <input type="text" id="url-http-username" autofocus [ngFormControl]="form.controls['httpUsername']">
                    </div>
                    <div class="field">
                        <label for="url-http-password">HTTP Password</label>
                        <input type="password" id="url-http-password" [ngFormControl]="form.controls['httpPassword']">
                    </div>
                    <div class="ui negative message" *ngIf="errors.httpAuthenticationFailed">
                        <p>The HTTP credentials you have entered were not accepted.</p>
                    </div>
                </div>
                <button class="ui primary labeled icon submit button" [class.loading]="loading" [disabled]="loading">
                    <i class="linkify icon"></i>
                    Connect Website
                </button>
            </form>
        </div>
    </div>
</div>
        `
})
export class ConnectWebsiteUrlController {
    private form: ControlGroup = new ControlGroup({
        url: new Control(''),
        httpUsername: new Control(''),
        httpPassword: new Control('')
    });
    private errors: Errors = new Errors();
    private loading: boolean = false;
    private progress: string = '';
    private httpAuthenticationRequired: boolean = false;

    constructor(private api: Api, private session: ConnectWebsiteSession, private router: Router, private state: State) {
    }

    public submit(formData: IFormData): boolean {
        if (!this.form.valid) {
            return;
        }
        this.session.httpUsername = formData.httpUsername;
        this.session.httpPassword = formData.httpPassword;
        this.errors.reset();
        this.progress = 'Loading';

        let siteUrl: string = formData.url;
        if (!siteUrl.match(/^https?:\/\//)) {
            // Make sure the URL starts with a scheme.
            siteUrl = 'http://' + siteUrl.replace(/^:?\/+/, '');
        }
        this.loading = true;
        let response = this.api.siteConnect(siteUrl, true, formData.httpUsername, formData.httpPassword);
        response.progress.subscribe((progress: Progress.ISiteConnect): void => {
            this.progress = progress.message;
        });
        response.result
            .finally((): void => {
                this.loading = false;
            })
            .subscribe((result: Result.ISiteConnect): void => {
                    this.session.clearAll();
                    this.state.addSite(result.site);
                    this.router.navigate(['/SiteDashboard', {id: result.site.id}]);
                },
                (constraint: ApiError.IError): void => {
                    if (constraint instanceof ApiError.ResponseUnauthorized) {
                        if (constraint.hasCredentials) {
                            this.session.clearHttp();
                            this.errors.httpAuthenticationFailed = true;
                        } else {
                            this.httpAuthenticationRequired = true;
                        }
                        return;
                    } else if (constraint instanceof ApiError.SiteUrlInvalid) {
                        this.errors.invalidUrl = true;
                        return;
                    } else if (constraint instanceof ApiError.SiteConnectAlreadyConnected) {
                        this.router.navigate(['../ConnectSiteReconnect', {
                            url: encodeURIComponent(siteUrl),
                            lookedForLoginForm: constraint.lookedForLoginForm ? 'yes' : 'no',
                            loginFormFound: constraint.loginFormFound ? 'yes' : 'no'
                        }]);
                        return;
                    } else if (constraint instanceof ApiError.SiteConnectOxygenNotFound) {
                        this.router.navigate(['../ConnectSiteNew', {
                            url: encodeURIComponent(siteUrl),
                            lookedForLoginForm: constraint.lookedForLoginForm ? 'yes' : 'no',
                            loginFormFound: constraint.loginFormFound ? 'yes' : 'no'
                        }]);
                        return;
                    } else if (constraint instanceof ApiError.NetworkCanNotResolveHost) {
                        this.errors.canNotResolveHost = true;
                        return;
                    }
                }
            );
    }
}

interface IFormData {
    url: string;
    httpUsername: string;
    httpPassword: string;
}

class Errors {
    public httpAuthenticationFailed: boolean = false;
    public canNotResolveHost: boolean = false;
    public invalidUrl: boolean = false;

    public reset(): void {
        this.httpAuthenticationFailed = false;
        this.canNotResolveHost = false;
        this.invalidUrl = false;
    }
}
