import {Component, ControlGroup, FormBuilder, CORE_DIRECTIVES, FORM_DIRECTIVES} from 'angular2/angular2';
import {Router} from 'angular2/router';

import * as Constraint from '../../../api/constraint';
import * as Result from '../../../api/result';
import {Api} from '../../../service/Api';
import {ConnectWebsiteSession} from '../../../service/ConnectWebsiteSession';

@Component({
    selector: 'connect-website-url-controller',
    directives: [CORE_DIRECTIVES, FORM_DIRECTIVES],
    template: `
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
        `
})
export class ConnectWebsiteUrlController {
    private form:ControlGroup;
    private errors:Errors;
    private loading:boolean = false;
    private httpAuthenticationRequired:boolean = false;

    constructor(private api:Api, private session:ConnectWebsiteSession, private router:Router, fb:FormBuilder) {
        this.form = fb.group({
            url: [''],
            httpUsername: [''],
            httpPassword: ['']
        });
        this.errors = new Errors();
    }

    public submit(formData:IFormData):boolean {
        if (!this.form.valid) {
            return;
        }
        this.session.httpUsername = formData.httpUsername;
        this.session.httpPassword = formData.httpPassword;
        this.errors.reset();

        let siteUrl:string = formData.url;
        if (!siteUrl.match(/^https?:\/\//)) {
            // Make sure the URL starts with a scheme.
            siteUrl = 'http://' + siteUrl.replace(/^:?\/+/, '');
        }
        this.loading = true;
        let _finally = ():void => {
            this.loading = false;
        };
        let response = this.api.siteConnect(siteUrl, true, formData.httpUsername, formData.httpPassword);
        response.progress.subscribe((progress:Object):void => {
        });
        response.result.subscribe(
            (result:Result.ISiteConnect):void => {
                _finally();
                this.session.clearAll();
                this.router.navigate(['/SiteDashboard', {id: result.site.id}]);
            },
            (constraint:Constraint.IConstraint):void => {
                _finally();
                if (constraint instanceof Constraint.ResponseUnauthorized) {
                    if (constraint.hasCredentials) {
                        this.session.clearHttp();
                        this.errors.httpAuthenticationFailed = true;
                    } else {
                        this.httpAuthenticationRequired = true;
                    }
                    return;
                } else if (constraint instanceof Constraint.SiteUrlInvalid) {
                    this.errors.invalidUrl = true;
                    return;
                } else if (constraint instanceof Constraint.SiteConnectAlreadyConnected) {
                    this.router.navigate(['../ConnectSiteReconnect', {
                        url: encodeURIComponent(siteUrl),
                        lookedForLoginForm: constraint.lookedForLoginForm ? 'yes' : 'no',
                        loginFormFound: constraint.loginFormFound ? 'yes' : 'no'
                    }]);
                    return;
                } else if (constraint instanceof Constraint.SiteConnectOxygenNotFound) {
                    this.router.navigate(['../ConnectSiteNew', {
                        url: encodeURIComponent(siteUrl),
                        lookedForLoginForm: constraint.lookedForLoginForm ? 'yes' : 'no',
                        loginFormFound: constraint.loginFormFound ? 'yes' : 'no'
                    }]);
                    return;
                } else if (constraint instanceof Constraint.NetworkCanNotResolveHost) {
                    this.errors.canNotResolveHost = true;
                    return;
                }
            });
    }
}

interface IFormData {
    url: string;
    httpUsername: string;
    httpPassword: string;
}

class Errors {
    public httpAuthenticationFailed:boolean = false;
    public canNotResolveHost:boolean = false;
    public invalidUrl:boolean = false;

    public reset():void {
        this.httpAuthenticationFailed = false;
        this.canNotResolveHost = false;
        this.invalidUrl = false;
    }
}
