interface SiteExtension {
    slug:string,
    name:string,
    description:string,
    version?:string,
}

interface Module extends SiteExtension {
    required:boolean,
}

interface Theme extends SiteExtension {
}
