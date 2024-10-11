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
        const response = await httpClient.get('/checkLogin');
        return response.data.is_login;
    } catch (error) {
        console.error('Failed to check login status:', error);
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

export { httpClient, checkLogin, getToken };
