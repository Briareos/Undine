interface ISiteExtension {
    slug: string;
    name: string;
    description: string;
    version: string;
}

interface IModule extends ISiteExtension {
    required: boolean;
}

interface ITheme extends ISiteExtension {
}
