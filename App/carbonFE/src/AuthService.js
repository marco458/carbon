// AuthService.js
import axios from 'axios';

const API_URL = 'http://localhost/api/v1';

const AuthService = {
  login: async (email, password) => {
    try {
      const response = await axios.post(`${API_URL}/token`, {
        email,
        password,
      });
      return [response.data.token_key, response.data.user.id];
    } catch (error) {
      throw new Error('Invalid credentials');
    }
  },
};

export default AuthService;
