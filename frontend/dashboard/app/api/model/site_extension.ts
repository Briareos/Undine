export interface ISiteExtension {
    slug: string;
    name: string;
    description: string;
    version: string;
}

export interface IModule extends ISiteExtension {
    required: boolean;
}

export interface ITheme extends ISiteExtension {
}
