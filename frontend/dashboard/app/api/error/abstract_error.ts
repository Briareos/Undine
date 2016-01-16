export interface IError {
    name: string;
}

export abstract class AbstractError implements IError {
    protected data: any;

    constructor(data: any) {
        this.data = data;
    }

    get name(): string {
        return this.data.error;
    }
}
