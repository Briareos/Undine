import {AbstractError} from "./abstract_error";

export class SiteIdNotProvided extends AbstractError {
}

export class SiteNotFound extends AbstractError {
}

export class SiteUrlEmpty extends AbstractError {
}

export class SiteUrlInvalid extends AbstractError {
}

export class SiteUrlTooLong extends AbstractError {
}
