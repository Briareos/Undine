import {Component, Inject} from 'angular2/core';
import {CORE_DIRECTIVES, FORM_DIRECTIVES, ControlGroup, Control} from 'angular2/common';
import {Router, RouteParams} from 'angular2/router'

import * as ApiError from "../../../api/errors";
import * as Result from "../../../api/result";
import {ConnectWebsiteSession} from "../../../service/ConnectWebsiteSession";
import {Api} from "../../../service/Api";
import {State} from "../../../dashboard/state";

@Component({
    selector: 'connect-website-new-controller',
    directives: [CORE_DIRECTIVES, FORM_DIRECTIVES],
    template: `
        <div class="ui grid">
            <div class="row">
                <div class="column">
                    <p>
                        So you want to connect <strong>{{ url }}</strong> to ManageDrupal!
                        <br>
                        Here's how to do it:</p>
                </div>
            </div>
        </div>
        <div class="ui very relaxed stackable grid" [ngClass]="loginFormFound ? ['two', 'column'] : ''">
            <div class="row">
                <div class="column">
                    <p>Install and enable our client plugin, <strong>Oxygen</strong>, that allows you to manage the website remotely:</p>

                    <div class="ui fluid action input">
                        <input type="text" readonly [value]="oxygenZipUrl" onclick="this.select()">
                        <a [attr.href]="oxygenZipUrl" class="ui right labeled icon button">
                            <i class="download icon"></i>
                            Download
                        </a>
                    </div>
                    <div class="ui info message">
                        If you have the Drupal's core module <strong>Updates</strong> enabled, go to the <a [attr.href]="updatesUrl" target="_blank">install module page</a> of your website, paste in the URL above and enable the "Oxygen" module.
                    </div>
                    <div *ngIf="errors.stillDisabled" class="ui negative message">
                        <p>The Oxygen module still appears to be disabled.</p>
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
                    <form class="ui form warning" (submit)="submit(form.value)" [ngFormModel]="form">
                        <div *ngIf="!ftpFormFound" class="field">
                            <p>... we can do that for you if you provide us with <strong>{{ url }}</strong> administrator credentials:</p>
                            <div class="field">
                                <label>Username</label>
                                <div class="ui left icon input">
                                    <input type="text" placeholder="Your Drupal username" [ngFormControl]="form.controls['admin'].controls['username']" required>
                                    <i class="user icon"></i>
                                </div>
                            </div>
                            <div class="field">
                                <label>Password</label>
                                <div class="ui left icon input">
                                    <input placeholder="Your Drupal password" type="password" [ngFormControl]="form.controls['admin'].controls['password']" required>
                                    <i class="lock icon"></i>
                                </div>
                            </div>
                            <div *ngIf="errors.invalidCredentials" class="ui negative message">
                                <p>Invalid credentials provided.</p>
                            </div>
                        </div>
                        <div *ngIf="ftpFormFound" class="field">
                            <div class="ui warning message">
                                <p>Drupal has detected that your filesystem is not writable by the web user. Please fill in your FTP connection details
                                    if you want to continue. Since ManageDrupal handles your site's updates, these credentials <strong>are</strong> safely
                                    kept on our system.</p>
                            </div>
                            <div class="inline fields">
                                <label form="new-ftp-method">Transfer method</label>
                                <div class="field">
                                    <select [ngFormControl]="form.controls['ftp'].controls['method']" name="new-ftp-method">
                                        <option value="ftp">FTP</option>
                                        <option value="ssh">SSH</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Username</label>
                                <div class="ui input">
                                    <input type="text" [ngFormControl]="form.controls['ftp'].controls['username']" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Password</label>
                                <div class="ui input">
                                    <input type="password" [ngFormControl]="form.controls['ftp'].controls['password']" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Host</label>
                                <div class="ui input">
                                    <input type="text" [ngFormControl]="form.controls['ftp'].controls['host']" placeholder="localhost">
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Port</label>
                                <div class="ui input">
                                    <input type="text" id="" [ngFormControl]="form.controls['ftp'].controls['port']" [attr.placeholder]="form.value.ftp.method === 'ftp' ? 21 : 22">
                                </div>
                            </div>
                            <div *ngIf="errors.ftpError" class="ui negative message">
                                <p>Failed to use the provided FTP credentials.</p>
                                <p *ngIf="errors.ftpErrorMessage">The FTP server returned the following error:
                                    <br>
                                    <code>{{ errors.ftpErrorMessage }}</code>
                                </p>
                            </div>
                        </div>
                        <button class="ui primary labeled icon submit button" [class.loading]="autoConnectWebsiteLoading" [disabled]="connectWebsiteActive">
                            <i class="linkify icon"></i>
                            Automatically Connect Website
                        </button>
                    </form>
                    <div class="ui info message" *ngIf="!ftpFormFound">
                        These credentials are only used now and are <strong>not</strong> saved anywhere on our system.
                    </div>
                </div>
            </div>
        </div>
        `
})
export class ConnectWebsiteNewController {
    private url: string;
    private updatesUrl: string;
    private oxygenZipUrl: string;
    private lookedForLoginForm: boolean;
    private loginFormFound: boolean;
    private connectWebsiteLoading: boolean;
    private autoConnectWebsiteLoading: boolean = false;
    private connectWebsiteActive: boolean = false;
    private ftpFormFound: boolean = false;
    private errors: Errors = new Errors();
    private form: ControlGroup = new ControlGroup({
        admin: new ControlGroup({
            username: new Control(''),
            password: new Control('')
        }),
        ftp: new ControlGroup({
            method: new Control('ftp'),
            username: new Control(''),
            password: new Control(''),
            host: new Control(''),
            port: new Control(''),
        })
    });

    constructor(private session: ConnectWebsiteSession, private router: Router, params: RouteParams, private api: Api, @Inject('OXYGEN_ZIP_URL') oxygenZipUrl, private state: State) {
        this.url = decodeURIComponent(params.get('url'));
        this.updatesUrl = this.url.replace(/\/?$/, '/?q=admin/modules/install');
        this.oxygenZipUrl = oxygenZipUrl;
        this.lookedForLoginForm = params.get('lookedForLoginForm') === 'yes';
        this.loginFormFound = params.get('loginFormFound') === 'yes';
    }

    public submit(formData: IFormData): void {
        this.errors.reset();
        this.connectWebsiteActive = true;
        this.autoConnectWebsiteLoading = true;
        let response = this.api.siteConnect(this.url, true, this.session.httpUsername, this.session.httpPassword, formData.admin.username, formData.admin.password, formData.ftp.method, formData.ftp.username, formData.ftp.password, formData.ftp.host, parseInt(formData.ftp.port, 10));
        response.result
            .finally(
                (): void => {
                    this.connectWebsiteActive = false;
                    this.autoConnectWebsiteLoading = false;
                }
            )
            .subscribe(
                (result: Result.ISiteConnect): void => {
                    this.session.clearAll();
                    this.state.addSite(result.site);
                    this.router.navigate(['/SiteDashboard', {id: result.site.id}]);
                },
                (error): void => {
                    if (error instanceof ApiError.DrupalClientInvalidCredentials) {
                        this.errors.invalidCredentials = true;
                        return;
                    } else if (error instanceof ApiError.FtpCredentialsRequired) {
                        this.ftpFormFound = true;
                        return;
                    } else if (error instanceof ApiError.FtpCredentialsError) {
                        this.errors.ftpError = true;
                        this.errors.ftpErrorMessage = error.ftpError;
                        return;
                    }
                }
            );
    };

    public click(): void {
        this.errors.reset();
        this.connectWebsiteActive = true;
        this.connectWebsiteLoading = true;
        let response = this.api.siteConnect(this.url, false, this.session.httpUsername, this.session.httpPassword);
        response.result
            .finally(
                (): void => {
                    this.connectWebsiteActive = false;
                    this.connectWebsiteLoading = false;
                }
            )
            .subscribe(
                (result: Result.ISiteConnect): void => {
                    this.session.clearAll();
                    this.state.addSite(result.site);
                    this.router.navigate(['/SiteDashboard', {id: result.site.id}]);
                },
                (constraint): void => {
                    if (constraint instanceof ApiError.SiteConnectOxygenNotFound) {
                        this.errors.stillDisabled = true;
                        return;
                    } else if (constraint instanceof ApiError.SiteConnectAlreadyConnected) {
                        // ISite got connected to another account in the meantime? It's possible...
                        this.router.navigate(['../ConnectSiteReconnect', {
                            url: encodeURIComponent(this.url),
                            lookedForLoginForm: this.lookedForLoginForm ? 'yes' : 'no',
                            loginFormFound: this.loginFormFound ? 'yes' : 'no'
                        }]);
                        return;
                    }
                }
            );
    };
}

class Errors {
    public stillConnected: boolean = false;
    public invalidCredentials: boolean = false;
    public stillDisabled: boolean = false;
    public ftpError: boolean = false;
    public ftpErrorMessage: string = '';

    public reset() {
        this.stillConnected = false;
        this.invalidCredentials = false;
        this.stillDisabled = false;
        this.ftpError = false;
        this.ftpErrorMessage = '';
    }
}

interface IFormData {
    admin: {
        username: string;
        password: string;
    };
    ftp: {
        method: string;
        username: string;
        password: string;
        host: string;
        port: string;
    };
}
