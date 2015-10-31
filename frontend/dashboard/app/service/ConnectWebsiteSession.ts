export class ConnectWebsiteSession {
    public url: string;
    public httpUsername: string;
    public httpPassword: string;
    public ftpMethod: string;
    public ftpUsername: string;
    public ftpPassword: string;
    public ftpHost: string;
    public ftpPort: string;
    public adminUsername: string;
    public adminPassword: string;

    public clearHttp(): void {
        this.url = this.httpUsername = null;
    }

    public clearFtp(): void {
        this.httpPassword = this.ftpMethod = this.ftpUsername = this.ftpPassword = this.ftpHost = this.ftpPort = null;
    }

    public clearAdmin(): void {
        this.adminUsername = this.adminPassword = null;
    }

    public clearAll(): void {
        this.clearAdmin();
        this.clearHttp();
        this.clearFtp();
    }
}
