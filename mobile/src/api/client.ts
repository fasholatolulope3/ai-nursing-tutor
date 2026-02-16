import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'https://ai-nursing-tutor.onrender.com/api/v1', // Update based on actual Render URL
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 10000,
});

// Request interceptor for auth
apiClient.interceptors.request.use(async (config) => {
  // You can add logic here to fetch tokens from secure storage (e.g., Capacitor Preferences)
  // const token = await Preferences.get({ key: 'auth_token' });
  // if (token.value) {
  //   config.headers.Authorization = `Bearer ${token.value}`;
  // }
  return config;
}, (error) => {
  return Promise.reject(error);
});

export default apiClient;
