import {Component, Inject, CORE_DIRECTIVES, FORM_DIRECTIVES, FormBuilder, ControlGroup} from 'angular2/angular2';
import {Router, RouteParams} from 'angular2/router'

import * as Constraint from "../../../api/constraint";
import * as Result from "../../../api/result";
import {ConnectWebsiteSession} from "../../../service/ConnectWebsiteSession";
import {Api} from "../../../service/Api";

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
        <div class="ui very relaxed stackable grid" [ng-class]="loginFormFound ? ['two', 'column'] : ''">
            <!-- @todo Duplicate code until https://github.com/angular/angular/issues/4805 gets resolved! -->
            <div class="row" *ng-if="!loginFormFound">
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
                    <div *ng-if="errors.stillDisabled" class="ui negative message">
                        <p>The Oxygen module still appears to be disabled.</p>
                    </div>
                    <button class="ui primary labeled icon submit button" [class.loading]="connectWebsiteLoading" [disabled]="connectWebsiteActive" (click)="click()">
                        <i class="linkify icon"></i>
                        Connect Website
                    </button>
                </div>
            </div>
            <div class="row" *ng-if="loginFormFound">
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
                    <div *ng-if="errors.stillDisabled" class="ui negative message">
                        <p>The Oxygen module still appears to be disabled.</p>
                    </div>
                    <button class="ui primary labeled icon submit button" [class.loading]="connectWebsiteLoading" [disabled]="connectWebsiteActive" (click)="click()">
                        <i class="linkify icon"></i>
                        Connect Website
                    </button>
                </div>
                <div class="ui vertical divider">
                    Or
                </div>
                <div class="column">
                    <form class="ui form warning" (submit)="submit(form.value)" [ng-form-model]="form">
                        <div *ng-if="!ftpFormFound" class="field">
                            <p>... we can do that for you if you provide us with <strong>{{ url }}</strong> administrator credentials:</p>
                            <div class="field">
                                <label>Username</label>
                                <div class="ui left icon input">
                                    <input type="text" placeholder="Your Drupal username" [ng-form-control]="form.controls['admin'].controls['username']" required>
                                    <i class="user icon"></i>
                                </div>
                            </div>
                            <div class="field">
                                <label>Password</label>
                                <div class="ui left icon input">
                                    <input placeholder="Your Drupal password" type="password" [ng-form-control]="form.controls['admin'].controls['password']" required>
                                    <i class="lock icon"></i>
                                </div>
                            </div>
                            <div *ng-if="errors.invalidCredentials" class="ui negative message">
                                <p>Invalid credentials provided.</p>
                            </div>
                        </div>
                        <div *ng-if="ftpFormFound" class="field">
                            <div class="ui warning message">
                                <p>Drupal has detected that your filesystem is not writable by the web user. Please fill in your FTP connection details
                                    if you want to continue. Since ManageDrupal handles your site's updates, these credentials <strong>are</strong> safely
                                    kept on our system.</p>
                            </div>
                            <div class="inline fields">
                                <label>Transfer method</label>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input id="new-ftp-method-ftp" type="radio" [ng-form-control]="form.controls['ftp'].controls['method']" value="ftp">
                                        <label for="new-ftp-method-ftp">FTP</label>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="ui radio checkbox">
                                        <input id="new-ftp-method-ssh" type="radio" [ng-form-control]="form.controls['ftp'].controls['method']" value="ssh">
                                        <label for="new-ftp-method-ssh">SSH</label>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Username</label>
                                <div class="ui input">
                                    <input type="text" [ng-form-control]="form.controls['ftp'].controls['username']" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Password</label>
                                <div class="ui input">
                                    <input type="password" [ng-form-control]="form.controls['ftp'].controls['password']" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Host</label>
                                <div class="ui input">
                                    <input type="text" [ng-form-control]="form.controls['ftp'].controls['host']" placeholder="localhost">
                                </div>
                            </div>
                            <div class="field">
                                <label for="">FTP Port</label>
                                <div class="ui input">
                                    <input type="text" id="" [ng-form-control]="form.controls['ftp'].controls['port']" [attr.placeholder]="form.value.ftp.method === 'ftp' ? 21 : 22">
                                </div>
                            </div>
                            <div *ng-if="errors.ftpError" class="ui negative message">
                                <p>Failed to use the provided FTP credentials.</p>
                                <p *ng-if="errors.ftpErrorMessage">The FTP server returned the following error:
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
                    <div class="ui info message" *ng-if="!ftpFormFound">
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
    private form: ControlGroup;

    constructor(private session: ConnectWebsiteSession, private router: Router, params: RouteParams, private api: Api, fb: FormBuilder, @Inject('OXYGEN_ZIP_URL') oxygenZipUrl) {
        this.errors = new Errors();
        this.url = decodeURIComponent(params.get('url'));
        this.updatesUrl = this.url.replace(/\/?$/, '/?q=admin/modules/install');
        this.oxygenZipUrl = oxygenZipUrl;
        this.lookedForLoginForm = params.get('lookedForLoginForm') === 'true';
        this.loginFormFound = params.get('loginFormFound') === 'true';
        this.form = fb.group({
            admin: fb.group({
                username: [''],
                password: ['']
            }),
            ftp: fb.group({
                method: ['ftp'],
                username: [''],
                password: [''],
                host: [''],
                port: ['']
            })
        });
    }

    public submit(formData: IFormData): void {
        this.errors.reset();
        this.connectWebsiteActive = true;
        this.autoConnectWebsiteLoading = true;
        let _finally = (): void => {
            this.connectWebsiteActive = false;
            this.autoConnectWebsiteLoading = false;
        };
        let response = this.api.siteConnect(this.url, true, this.session.httpUsername, this.session.httpPassword, formData.admin.username, formData.admin.password, formData.ftp.method, formData.ftp.username, formData.ftp.password, formData.ftp.host, parseInt(formData.ftp.port, 10));
        response.result.subscribe(
            (result: Result.ISiteConnect): void => {
                _finally();
                this.session.clearAll();
                this.router.navigate(['/SiteDashboard', {uid: result.site.uid}]);
            },
            (constraint): void => {
                _finally();
                if (constraint instanceof Constraint.SiteInvalidCredentials) {
                    this.errors.invalidCredentials = true;
                    return;
                } else if (constraint instanceof Constraint.SiteFtpCredentialsRequired) {
                    this.ftpFormFound = true;
                    return;
                } else if (constraint instanceof Constraint.SiteFtpCredentialsError) {
                    this.errors.ftpError = true;
                    this.errors.ftpErrorMessage = constraint.ftpError;
                    return;
                }
            }
        );
    };

    public click(): void {
        this.errors.reset();
        this.connectWebsiteActive = true;
        this.connectWebsiteLoading = true;
        let _finally = (): void => {
            this.connectWebsiteActive = false;
            this.connectWebsiteLoading = false;
        };
        let response = this.api.siteConnect(this.url, false, this.session.httpUsername, this.session.httpPassword);
        response.result.subscribe(
            (result: Result.ISiteConnect): void => {
                _finally();
                this.session.clearAll();
                this.router.navigate(['/SiteDashboard', {uid: result.site.uid}]);
            },
            (constraint): void => {
                _finally();
                if (constraint instanceof Constraint.SiteOxygenNotEnabled) {
                    this.errors.stillDisabled = true;
                    return;
                } else if (constraint instanceof Constraint.SiteAlreadyConnected) {
                    // ISite got connected to another account in the meantime? It's possible...
                    this.router.navigate(['../ConnectSiteReconnect', {
                        url: encodeURIComponent(this.url),
                        lookedForLoginForm: this.lookedForLoginForm.toString(),
                        loginFormFound: this.loginFormFound.toString()
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
