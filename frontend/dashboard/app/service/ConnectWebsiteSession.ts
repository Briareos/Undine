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

    clearHttp() {
        this.url = this.httpUsername = null;
    }

    clearFtp() {
        this.httpPassword = this.ftpMethod = this.ftpUsername = this.ftpPassword = this.ftpHost = this.ftpPort = null;
    }

    clearAdmin() {
        this.adminUsername = this.adminPassword = null
    }

    clearAll() {
        this.clearAdmin();
        this.clearHttp();
        this.clearFtp();
    }
}

angular.module('undine.dashboard')
    .factory('ConnectWebsiteSession', function () {
        return new ConnectWebsiteSession();
    });
