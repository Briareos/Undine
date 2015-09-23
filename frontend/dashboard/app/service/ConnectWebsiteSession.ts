class ConnectWebsiteSession {
    url:string;
    httpUsername:string;
    httpPassword:string;
    ftpMethod:string;
    ftpUsername:string;
    ftpPassword:string;
    ftpHost:string;
    ftpPort:string;
    adminUsername:string;
    adminPassword:string;

    clear() {
        this.url = this.httpUsername = null;
        this.httpPassword = this.ftpMethod = this.ftpUsername = this.ftpPassword = this.ftpHost = this.ftpPort = null;
        this.adminUsername = this.adminPassword = null
    }
}

angular.module('undine.dashboard')
    .factory('ConnectWebsiteSession', function () {
        return new ConnectWebsiteSession();
    });
