import axios from 'axios';

let httpClient = axios.create({
    baseURL: '/api',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
});

export async function getDriveRoot() {
    try {
        const response = await httpClient.get('/drive/root');
        return response;
    } catch (error) {
        return error;
    }
}
