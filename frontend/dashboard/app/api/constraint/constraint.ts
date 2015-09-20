class Constraint {
    protected data:any;

    constructor(data:any) {
        this.data = data;
    }

    get name() {
        return this.data.error;
    }
}
