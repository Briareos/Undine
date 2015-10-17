class SiteUpdateType {
    public static CORE: string = 'core';
    public static MODULE: string = 'module';
    public static THEME: string = 'theme';
}

class SiteUpdateStatus {
    public static NOT_SECURE: string = 'not_secure';
    public static REVOKED: string = 'revoked';
    public static NOT_SUPPORTED: string = 'not_supported';
    public static NOT_CURRENT: string = 'not_current';
    public static CURRENT: string = 'current';
    public static NOT_CHECKED: string = 'not_checked';
    public static UNKNOWN: string = 'unknown';
    public static NOT_FETCHED: string = 'not_fetched';
    public static FETCH_PENDING: string = 'fetch_pending';
}

interface ISiteUpdate {
    // One of SiteUpdateType.
    type: string;
    name: string;
    slug: string;
    existingVersion: string;
    recommendedVersion: string;
    // One of SiteUpdateStatus.
    status: string;
    enabled: boolean;
    package: string;
    project: string;
    includes: string[];
    baseThemes: string[];
    subThemes: string[];
}

interface ICoreUpdate extends ISiteUpdate {
    name: string;
    existingVersion: string;
    recommendedVersion: string;
    status: string;
}

interface IModuleUpdate extends ISiteUpdate {
    name: string;
    slug: string;
    existingVersion: string;
    status: string;
    enabled: boolean;
    package: string;
    project: string;
    includes: string[];
}

interface IThemeUpdate extends ISiteUpdate {
    name: string;
    slug: string;
    existingVersion: string;
    status: string;
    enabled: boolean;
    baseThemes: string[];
    subThemes: string[];
}
