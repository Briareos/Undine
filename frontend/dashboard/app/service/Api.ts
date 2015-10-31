import {Injectable, Inject} from 'angular2/angular2';
import Observable from '@reactivex/rxjs/dist/cjs/Observable';
import Observer from '@reactivex/rxjs/dist/cjs/Observer';

import * as Result from '../api/result';
import * as Progress from '../api/progress';
import * as Constraint from '../api/constraint';
import ConstraintFactory from '../api/constraint_factory';

class ApiXhrFactory {
    public createApiXhr(method: string, url: string, data?: Object): ApiResponse<Progress.IProgress, Result.IResult> {
        method = method.toUpperCase();

        let stream: boolean = false;
        let xhr: XMLHttpRequest = new XMLHttpRequest();
        xhr.open(method, url);

        let progress = new Observable<any>((progressObserver: Observer<any>): void => {
            stream = true;

            xhr.setRequestHeader('X-Stream', '1');

            let responseBuffer: string = '', delimiterIndex: number = -1, lineObject: any, lineBuffer: string;
            let pumpResponse = (buffer: string) => {
                responseBuffer += buffer;
                while ((delimiterIndex = responseBuffer.indexOf("\n")) !== -1) {
                    lineBuffer = responseBuffer.substring(0, delimiterIndex + 1);
                    responseBuffer = responseBuffer.substring(delimiterIndex + 1);
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
                    // Last line is empty in streaming bulk responses, so check that case here.
                    if (lastLine.length) {
                        this.finalizeResponse(resultObserver, JSON.parse(lastLine));
                    }
                } else {
                    this.finalizeResponse(resultObserver, JSON.parse(xhr.response));
                }
                resultObserver.complete();
            };

            // Error event handler.
            let onError = (err) => {
                resultObserver.error(err);
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

    private finalizeResponse(observer: Observer<any>, response: any): void {
        console.log(response);
        if (response.ok === true) {
            observer.next(response);
            observer.complete();
            return;
        }

        let constraint = ConstraintFactory.createConstraint(response.error, response);
        console.log(constraint);
        observer.error(constraint);
    }
}

interface IApiResponse<T> {
    result: ErrorAwareObservable<T, Constraint.IConstraint>;
}

interface IProgressAwareApiResponse<T, U> extends IApiResponse<T> {
    progress: Observable<U>;
}

interface ErrorAwareObservable<T, U> {
    subscribe(observerOrNext?: Observer<T> | ((value: T) => void), error?: (error: U) => void, complete?: () => void): void;
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

    get result(): ErrorAwareObservable<T, Constraint.IConstraint> {
        return this._result;
    }
}

class ApiTransaction {
    private _action: string;
    private _stack: any[] = [];

    get action(): string {
        return this._action;
    }

    get stack(): any[] {
        return this._stack;
    }

    public push(action: string, params: any): void {
        if (this._action) {
            if (this._action !== action) {
                throw Error(`Transaction of type "${this._action}" cannot accept actions of type "${action}".`);
            }
        } else {
            this._action = action;
        }
        this._stack.push(params);
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

    public beginTransaction(): void {
        this.transaction = new ApiTransaction();
    }

    public commit(): void {
        // @todo: Implement!
        this.transaction = null;
    }

    public rollback(): void {
        if (!this.transaction) {
            throw Error('There is no active transaction.');
        }
        this.transaction = null;
    }

    public siteConnect(url: string, checkUrl: boolean = false, httpUsername?: string, httpPassword?: string, adminUsername?: string, adminPassword?: string, ftpMethod?: string, ftpUsername?: string, ftpPassword?: string, ftpHost?: string, ftpPort?: number): IProgressAwareApiResponse<Result.ISiteConnect, Progress.ISiteConnect> {
        return this.command(
            'site.connect',
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

    private command(command: string, parameters?: Object): ApiResponse<Progress.IProgress, Result.IResult> {
        // if (this.transaction) {
        //    let deferred: ng.IDeferred<ng.IHttpPromiseCallbackArg<IApiResult>> = this.q.defer();
        //
        //    // @todo: Save this deferred object!
        //
        //    return deferred.promise;
        // }
        return this.xhrFactory.createApiXhr('POST', this.endpoint + command, parameters);
    }
}
