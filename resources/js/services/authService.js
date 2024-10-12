import axios from 'axios';

let httpClient = axios.create({
    baseURL: '/api',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
});

async function checkLogin() {
    try {
        const response = await httpClient.get('/oauth/checkLogin');
        return response.status === 200;
    } catch (error) {
        return false;
    }
}

async function getToken(code) {
    try {
        const response = await httpClient.get('/oauth/getToken', { params: { code } });
        return response.data;
    } catch (error) {
        return error;
    }
}

async function getDriveRoot() {
    try {
        const response = await httpClient.get('/drive/root');
        return response;
    } catch (error) {
        return error;
    }

}

export { httpClient, checkLogin, getToken };
