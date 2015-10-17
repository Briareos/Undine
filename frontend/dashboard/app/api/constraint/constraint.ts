class Constraint {
    protected data: any;

    constructor(data: any) {
        this.data = data;
    }

    get name(): string {
        return this.data.error;
    }
}
