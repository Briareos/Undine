import {Component} from 'angular2/core';
import {FormBuilder, ControlGroup, CORE_DIRECTIVES, FORM_DIRECTIVES} from 'angular2/common';
import {Router, RouteParams} from 'angular2/router';

import * as Result from "../../../api/result";
import * as Constraint from "../../../api/constraint";
import {ConnectWebsiteSession} from "../../../service/ConnectWebsiteSession";
import {Api} from "../../../service/Api";

@Component({
    selector: 'connect-website-reconnect-controller',
    directives: [CORE_DIRECTIVES, FORM_DIRECTIVES],
    template: `
        <div class="ui grid">
            <div class="row">
                <div class="column">
                    <div class="ui warning message">
                        <div class="header">
                            The website at {{ url }} is already connected
                        </div>
                        This website is already connected to another account, and for security reasons we can't disclose that account's details. You can connect the website with this account, but then it won't be manageable from the other account.
                    </div>
                </div>
            </div>
        </div>
        <div class="ui middle aligned very relaxed stackable grid" [ngClass]="loginFormFound ? ['two', 'column'] : ''">
            <div class="row">
                <div class="center aligned middle aligned column">
                    <p>
                        You can go to <a [href]="disconnectUrl" target="_blank">this page</a> and disconnect the website from any dashboard that it's connected to. Then click:
                    </p>
                    <div *ngIf="errors.stillConnected" class="ui negative message">
                        <p>The site still appears to be connected to the other account.</p>
                    </div>
                    <button class="ui primary labeled icon submit button" [class.loading]="connectWebsiteLoading" [disabled]="connectWebsiteActive" (click)="click()">
                        <i class="linkify icon"></i>
                        Connect Website
                    </button>
                </div>
                <!-- The elements below will create a column -->
                <div class="ui vertical divider" *ngIf="loginFormFound">
                    Or
                </div>
                <div class="column" *ngIf="loginFormFound">
                    <p>... we can do that for you if you provide us with <strong>{{ url }}</strong> administrator credentials:</p>
                    <form class="ui form" (submit)="submit(form.value)" [ngFormModel]="form">
                        <div class="field">
                            <label>Username</label>
                            <div class="ui left icon input">
                                <input type="text" placeholder="Your Drupal username" [ngFormControl]="form.controls['username']" required>
                                <i class="user icon"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Password</label>
                            <div class="ui left icon input">
                                <input placeholder="Your Drupal password" type="password" [ngFormControl]="form.controls['password']" required>
                                <i class="lock icon"></i>
                            </div>
                        </div>
                        <div *ngIf="errors.invalidCredentials" class="ui negative message">
                            <p>Invalid credentials provided.</p>
                        </div>
                        <button class="ui primary labeled icon submit button" [class.loading]="autoConnectWebsiteLoading" [disabled]="connectWebsiteActive">
                            <i class="linkify icon"></i>
                            Automatically Connect Website
                        </button>
                    </form>
                    <div class="ui info message">
                        These credentials are only used now and are <strong>not</strong> saved anywhere on our system.
                    </div>
                </div>
            </div>
        </div>
        `
})
export class ConnectWebsiteReconnectController {
    private url: string;
    private disconnectUrl: string;
    private lookedForLoginForm: boolean;
    private loginFormFound: boolean;
    private connectWebsiteLoading: boolean = false;
    private autoConnectWebsiteLoading: boolean = false;
    private connectWebsiteActive: boolean = false;
    private errors: Errors = new Errors();
    private form: ControlGroup;

    constructor(private session: ConnectWebsiteSession, private api: Api, private router: Router, params: RouteParams, fb: FormBuilder) {
        this.url = decodeURIComponent(params.get('url'));
        this.disconnectUrl = this.url.replace(/\/?$/, '/?q=admin/config/oxygen/disconnect');
        this.lookedForLoginForm = params.get('lookedForLoginForm') === 'yes';
        this.loginFormFound = params.get('loginFormFound') === 'yes';
        this.form = fb.group({
            username: [''],
            password: [''],
        });
    }

    public click(): void {
        this.errors.reset();
        this.connectWebsiteActive = true;
        this.connectWebsiteLoading = true;
        let _finally = () => {
            this.connectWebsiteActive = false;
            this.connectWebsiteLoading = false;
        };
        let response = this.api.siteConnect(this.url, false, this.session.httpUsername, this.session.httpPassword);
        response.result.subscribe(
            (result): void => {
                _finally();
                this.session.clearAll();
                this.router.navigate(['/SiteDashboard', {id: result.site.id}]);
            },
            (constraint: Constraint.IConstraint): void => {
                _finally();
                if (constraint instanceof Constraint.SiteConnectAlreadyConnected) {
                    this.errors.stillConnected = true;
                    return;
                }
            }
        );
    }

    public submit(formData: IFormData): void {
        this.errors.reset();
        this.connectWebsiteActive = true;
        this.autoConnectWebsiteLoading = true;
        let _finally = (): void => {
            this.connectWebsiteActive = false;
            this.autoConnectWebsiteLoading = false;
        };
        let response = this.api.siteConnect(this.url, true, this.session.httpUsername, this.session.httpPassword, formData.username, formData.password)
        response.result.subscribe(
            (result: Result.ISiteConnect): void => {
                _finally();
                this.session.clearAll();
                this.router.navigate(['/SiteDashboard', {id: result.site.id}]);
            },
            (constraint: Constraint.IConstraint): void => {
                _finally();
                if (constraint instanceof Constraint.SiteInvalidCredentials) {
                    this.errors.invalidCredentials = true;
                    return;
                }
            }
        );
    }
}

interface IFormData {
    username: string;
    password: string;
}

class Errors {
    stillConnected: boolean = false;
    invalidCredentials: boolean = false;

    public reset(): void {
        this.stillConnected = false;
        this.invalidCredentials = false;
    }
}
