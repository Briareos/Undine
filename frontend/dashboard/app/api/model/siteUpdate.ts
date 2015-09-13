enum SiteUpdateType {
    core,
    module,
    theme,
}

enum SiteUpdateStatus {
    not_secure,
    revoked,
    not_supported,
    not_current,
    current,
    not_checked,
    unknown,
    not_fetched,
    fetch_pending,
}

interface SiteUpdate {
    type:SiteUpdateType,
    name:string,
    slug:string,
    existingVersion:string,
    recommendedVersion:string,
    status:SiteUpdateStatus,
    enabled:boolean,
    package?:string,
    project?:string,
    includes:Array<string>,
    baseThemes:Array<string>
    subThemes:Array<string>,
}

interface CoreUpdate extends SiteUpdate{
    name:string,
    existingVersion:string
    recommendedVersion:string
    status:SiteUpdateStatus,
}

interface ModuleUpdate extends SiteUpdate {
    name:string,
    slug:string,
    existingVersion:string,
    status:SiteUpdateStatus,
    enabled:boolean,
    package?:string,
    project?:string,
    includes:Array<string>
}

interface ThemeUpdate extends SiteUpdate {
    name:string,
    slug:string,
    existingVersion:string,
    status:SiteUpdateStatus,
    enabled:boolean,
    baseThemes:Array<string>,
    subThemes:Array<string>,
}
