import * as Constraint from '../api/constraint';

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
    public static createConstraint(name: string, data: any): Constraint.IConstraint {
        let constraintName: string = transformConstraintName(name);
        if (Constraint[constraintName]) {
            return new Constraint[constraintName](data);
        }
        // @todo: log this case, since it probably shouldn't happen.
        return new Constraint.Constraint(data);
    }
}
