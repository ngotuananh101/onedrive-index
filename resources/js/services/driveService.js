import axios from 'axios';

let httpClient = axios.create({
    baseURL: '/api',
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
});

export async function getDriveRoot(query) {
    try {
        const response = await httpClient.get('/drive/root', {
            params: query
        });
        return response;
    } catch (error) {
        return error;
    }
}

export async function getFolderById(query, id) {
    try {
        const response = await httpClient.get(`/drive/folder/${id}`);
        return response;
    } catch (error) {
        return error;
    }
}

export async function getBreadcrumb(id) {
    try {
        const response = await httpClient.get(`/drive/breadcrumb/${id}`);
        return response;
    } catch (error) {
        return error;
    }
}

export async function getPreviewUrl(id) {
    try {
        const response = await httpClient.get(`/drive/preview/${id}`);
        return response;
    } catch (error) {
        return error;
    }
}

export async function searchFileAndFolder(query){
    try {
        const response = await httpClient.get('/drive/search', {
            params: query
        });
        return response.data;
    } catch (error) {
        console.log(error);
        return [];
    }
}
