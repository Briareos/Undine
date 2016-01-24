import {Injectable, Inject} from 'angular2/core';
import {Observable} from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/finally';

import * as Result from '../api/result';
import * as Progress from '../api/progress';
import * as ApiError from '../api/errors';
import ConstraintFactory from '../api/error_factory';

function finalizeResponse(observer: Observer<any>, result: any): void {
    if (result.ok === true) {
        observer.next(result);
        observer.complete();
        return;
    }

    let constraint = ConstraintFactory.createConstraint(result.error, result);
    observer.error(constraint);
}

class ApiXhrFactory {
    public createApiXhr(method: string, url: string, data?: Object): ApiResponse<Progress.IProgress, Result.IResult> {
        method = method.toUpperCase();

        let stream: boolean = false;
        let xhr: XMLHttpRequest = new XMLHttpRequest();
        xhr.open(method, url);

        let progress: Observable<any> = new Observable<any>((progressObserver: Observer<any>): void => {
            stream = true;

            xhr.setRequestHeader('X-Stream', '1');

            let buffer: string = '', delimiterIndex: number = -1, lineObject: any, lineBuffer: string;
            let pumpResponse = (data: string): void => {
                buffer += data;
                while ((delimiterIndex = buffer.indexOf("\n")) !== -1) {
                    lineBuffer = buffer.substring(0, delimiterIndex + 1);
                    buffer = buffer.substring(delimiterIndex + 1);
                    lineObject = JSON.parse(lineBuffer);
                    progressObserver.next(lineObject);
                }
            };

            let read = 0;
            xhr.onreadystatechange = (): void => {
                if (xhr.readyState === 3) {
                    pumpResponse(xhr.response.substring(read));
                }
                read = xhr.response.length;

                if (xhr.readyState === 4) {
                    progressObserver.complete();
                }
            };
        });

        let result = new Observable<any>((resultObserver: Observer<any>): Function => {
            // Load event handler.
            let onLoad = ()=> {
                if (xhr.status !== 200) {
                    throw Error(`Unexpected status code: ${status}`);
                }

                let lastDelimiter;
                if (stream && (lastDelimiter = xhr.response.lastIndexOf("\n")) !== -1) {
                    let lastLine = xhr.response.substring(lastDelimiter + 1);
                    // Last line is empty in streaming bulk responses, so check for that case here.
                    if (lastLine.length) {
                        finalizeResponse(resultObserver, JSON.parse(lastLine));
                    } else {
                        resultObserver.complete();
                    }
                } else {
                    finalizeResponse(resultObserver, JSON.parse(xhr.response));
                }
            };

            // Error event handler.
            let onError = (err) => {
                throw Error(err);
            };

            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-XSRF-TOKEN', '1');

            xhr.addEventListener('load', onLoad);
            xhr.addEventListener('error', onError);

            if (method === 'GET' || !data) {
                xhr.send();
            } else {
                xhr.send(JSON.stringify(data));
            }

            return (): void => {
                xhr.removeEventListener('load', onLoad);
                xhr.removeEventListener('error', onError);
                xhr.abort();
            };
        });

        return new ApiResponse(progress, result);
    }
}

interface IApiResponse<T> {
    result: ErrorAwareObservable<T, ApiError.IError>;
}

interface IProgressAwareApiResponse<T, U> extends IApiResponse<T> {
    progress: Observable<U>;
}

interface ErrorAwareObservable<T, U> {
    subscribe(observerOrNext?: Observer<T> | ((value: T) => void), error?: (error: U) => void, complete?: () => void): ErrorAwareObservable<T, U>;
    finally(finallySelector: () => void): ErrorAwareObservable<T, U>;
}

/**
 * Used for every API call, but should not be accessed directly.
 * Use {@link IApiResponse} and {@link IProgressAwareApiResponse} instead.
 */
class ApiResponse<T, U> implements IProgressAwareApiResponse<T, U> {
    constructor(private _progress: any, private _result: any) {
    }

    get progress(): Observable<U> {
        return this._progress;
    }

    get result(): ErrorAwareObservable<T, ApiError.IError> {
        return this._result;
    }
}

class ApiTransaction {
    private _action: string;
    private _parameters: any[] = [];
    private _observers: ObserverContainer[] = [];
    public progress: boolean = false;

    get action(): string {
        return this._action;
    }

    get parameters(): any[] {
        return this._parameters;
    }

    get observers() {
        return this._observers;
    };

    public push(action: string, parameters: any, observerContainer: ObserverContainer): void {
        if (this._action) {
            if (this._action !== action) {
                throw Error(`Transaction of type "${this._action}" cannot accept actions of type "${action}".`);
            }
        } else {
            this._action = action;
        }
        this._parameters.push(parameters);
        this._observers.push(observerContainer);
    }
}

@Injectable()
export class Api {
    private endpoint: string;
    private transaction: ApiTransaction;
    private xhrFactory: ApiXhrFactory;

    constructor(@Inject('API_URL') endpoint: string) {
        this.endpoint = endpoint;
        this.xhrFactory = new ApiXhrFactory();
    }

    public bulk(fn): Observable<any> {
        this.transaction = new ApiTransaction();

        fn();

        if (this.transaction.parameters.length === 0) {
            throw Error('No actions added to the bulk call.');
        }

        let completeFn = (): void => {
        };

        let observers = this.transaction.observers;

        let response = this.xhrFactory.createApiXhr('POST', this.endpoint + this.transaction.action, this.transaction.parameters);
        response.progress.subscribe(
            (response: any): void => {
                if (response.hasOwnProperty('progress')) {
                    // This is a "progress" message.
                    observers[response.index].progress.next(response.progress)
                } else {
                    // This is a "result" message.
                    finalizeResponse(observers[response.index].result, response.result);
                }
            },
            null,
            (): void => {
                completeFn();
            }
        );
        // We must subscribe to the result for the request to fire.
        response.result.subscribe();

        this.transaction = null;

        return new Observable<any>((observer: Observer<any>)=> {
            completeFn = () => {
                observer.complete();
            };
        });
    }

    private command(command: string, parameters?: Object): ApiResponse<Progress.IProgress, Result.IResult> {
        if (this.transaction) {
            let observerContainer = new ObserverContainer();
            let progressObserver = new Observable<Progress.IProgress>((observer: Observer<Progress.IProgress>): void => {
                this.transaction.progress = true;
                observerContainer.progress = observer;
            });
            let resultObserver = new Observable<Result.IResult>((observer: Observer<any>): void => {
                observerContainer.result = observer;
            });

            let response: ApiResponse<Progress.IProgress, Result.IResult> = new ApiResponse(progressObserver, resultObserver);
            this.transaction.push(command, parameters, observerContainer);

            return response;
        }
        return this.xhrFactory.createApiXhr('POST', this.endpoint + command, parameters);
    }

    public siteConnect(url: string, checkUrl: boolean = false, httpUsername?: string, httpPassword?: string, adminUsername?: string, adminPassword?: string, ftpMethod?: string, ftpUsername?: string, ftpPassword?: string, ftpHost?: string, ftpPort?: number): IProgressAwareApiResponse<Result.ISiteConnect, Progress.ISiteConnect> {
        return this.command(
            'site.connect?include=state,state.modules,state.themes,state.coreUpdates,state.moduleUpdates,state.themeUpdates',
            {
                url: url,
                checkUrl: checkUrl,
                httpUsername: httpUsername,
                httpPassword: httpPassword,
                adminUsername: adminUsername,
                adminPassword: adminPassword,
                ftpMethod: ftpMethod,
                ftpUsername: ftpUsername,
                ftpPassword: ftpPassword,
                ftpHost: ftpHost,
                ftpPort: ftpPort
            }
        );
    }

    public sitePing(id: string): IApiResponse<Result.ISitePing> {
        return this.command('site.ping?include=modules,themes,coreUpdates,moduleUpdates,themeUpdates', {site: id});
    }

    public siteDisconnect(id: string): IApiResponse<Result.ISiteDisconnect> {
        return this.command('site.disconnect', {site: id});
    }
}

class ObserverContainer {
    public progress: Observer<any> = new MockObserver;
    public result: Observer<any> = new MockObserver;
}

class MockObserver<T> implements Observer<T> {
    isUnsubscribed: boolean = true;

    next(value: T): void {
    }

    error(err: any): void {
    }

    complete(): void {
    }
}
