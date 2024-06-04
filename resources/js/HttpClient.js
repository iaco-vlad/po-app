import axios from 'axios';

class HttpClient {
    constructor() {
        this.client = axios.create({
            baseURL: '/api',
        });
    }

    get(url, config = {}) {
        return this.client.get(url, config);
    }

    post(url, data = {}, config = {}) {
        return this.client.post(url, data, config);
    }

    put(url, data = {}, config = {}) {
        return this.client.put(url, data, config);
    }

    delete(url, config = {}) {
        return this.client.delete(url, config);
    }
}

export default HttpClient;
