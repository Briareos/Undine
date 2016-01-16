import * as Errors from './errors';

/**
 * Transforms strings like connect_site.unknown_error to ConnectSiteUnknownError.
 */
function transformConstraintName(name: string): string {
    return name
    // Capitalize first letter.
        .replace(/^(.)/, (s: string) => s.toUpperCase())
        // Capitalize letters after . and _ and omit those separators.
        .replace(/(?:_|\.)(.)/g, (ignore: string, s: string) => s.toUpperCase());
}

export default class ConstraintFactory {
    public static createConstraint(name: string, data: any): Errors.IError {
        let errorClass: string = transformConstraintName(name);
        if (Errors[errorClass]) {
            return new Errors[errorClass](data);
        }

        throw Error(`Unknown error returned: ${name} (class: ${errorClass})`);
    }
}
