// axios-config.js
import axios from 'axios';

const axiosInstance = axios.create({
  baseURL: 'http://localhost/api/v1', // Your backend base URL
});

axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default axiosInstance;
