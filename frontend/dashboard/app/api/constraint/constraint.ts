export interface IConstraint {
    name: string;
}

export class Constraint implements IConstraint{
    protected data: any;

    constructor(data: any) {
        this.data = data;
    }

    get name(): string {
        return this.data.error;
    }
}
