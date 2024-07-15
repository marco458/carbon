import React, { useState } from 'react';
import AuthService from '../AuthService';
import { useNavigate } from 'react-router-dom';
import { TextField, Button, Typography, Paper, Grid } from '@mui/material';

function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const [token, id] = await AuthService.login(email, password);
      localStorage.setItem('token', token); // Store token in local storage
      localStorage.setItem('user', id);
      navigate('/'); // Redirect to the home page
    } catch (error) {
      console.log('Pristup odbijen. Nevažeće vjerodajnice ili korisnik deaktiviran.');
      setError('Pristup odbijen. Nevažeće vjerodajnice ili korisnik deaktiviran.');
    }
  };

  const handleRegisterClick = () => {
    navigate('/register');
  };

  return (
    <Grid container className="login-container">
      <Paper elevation={3} className="login-paper">
        <Typography variant="h4" gutterBottom>
          Prijava u sustav
        </Typography>
        <form onSubmit={handleSubmit}>
          <TextField
            type="email"
            label="Email"
            fullWidth
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="login-input"
            style={{ marginBottom: '0.7rem' }}
          />
          <TextField
            type="password"
            label="Lozinka"
            fullWidth
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="login-input"
            style={{ marginBottom: '0.7rem' }}
          />
          <Button variant="contained" type="submit" fullWidth className="login-button">
            Prijavi se
          </Button>
        </form>
        {error && <Typography variant="body2" color="error">{error}</Typography>}
        <Button color="primary" onClick={handleRegisterClick} style={{ marginTop: '0.7rem' }}>
          Registriraj se
        </Button>
      </Paper>
    </Grid>
  );
}

export default Login;
