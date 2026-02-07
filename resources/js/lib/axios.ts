import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
    withCredentials: true,
    withXSRFToken: true
});

export default apiClient;
