class SiteUpdateType {
    static CORE = 'core';
    static MODULE = 'module';
    static THEME = 'theme';
}

class SiteUpdateStatus {
    static NOT_SECURE = 'not_secure';
    static REVOKED = 'revoked';
    static NOT_SUPPORTED = 'not_supported';
    static NOT_CURRENT = 'not_current';
    static CURRENT = 'current';
    static NOT_CHECKED = 'not_checked';
    static UNKNOWN = 'unknown';
    static NOT_FETCHED = 'not_fetched';
    static FETCH_PENDING = 'fetch_pending';
}

interface SiteUpdate {
    // One of SiteUpdateType.
    type:string,
    name:string,
    slug:string,
    existingVersion:string,
    recommendedVersion:string,
    // One of SiteUpdateStatus.
    status:string,
    enabled:boolean,
    package?:string,
    project?:string,
    includes:Array<string>,
    baseThemes:Array<string>
    subThemes:Array<string>,
}

interface CoreUpdate extends SiteUpdate {
    name:string,
    existingVersion:string
    recommendedVersion:string
    status:string,
}

interface ModuleUpdate extends SiteUpdate {
    name:string,
    slug:string,
    existingVersion:string,
    status:string,
    enabled:boolean,
    package?:string,
    project?:string,
    includes:Array<string>
}

interface ThemeUpdate extends SiteUpdate {
    name:string,
    slug:string,
    existingVersion:string,
    status:string,
    enabled:boolean,
    baseThemes:Array<string>,
    subThemes:Array<string>,
}
