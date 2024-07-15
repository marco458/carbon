import React, { useState } from 'react';
import axios from 'axios';
import { TextField, Button, Typography, Paper, Grid } from '@mui/material';
import { useNavigate } from 'react-router-dom';

function Register() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [phoneNumber, setPhoneNumber] = useState('');
  const [organizationName, setOrganizationName] = useState('');
  const [error, setError] = useState('');
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (password !== confirmPassword) {
      setError('Lozinka i ponovljena lozinka se ne podudaraju');
      e.preventDefault();
      return;
    }

    try {
      const response = await axios.post(process.env.REACT_APP_BACKEND_API_URL + '/users/register', {
        user: {
          email,
          password,
          repeat_password: confirmPassword,
          first_name: firstName,
          last_name: lastName,
          phone_number: phoneNumber,
          organization_name: organizationName
        }
      });

      navigate('/login');
    } catch (error) {
      console.log(error);
      console.log('Registracija nije uspjela');
      setError('Ova email adresa je već zauzeta');
    }
  };

  const handleLoginClick = () => {
    navigate('/login');
  };

  return (
    <Grid container className="register-container">
      <Paper elevation={3} className="register-paper">
        <Typography variant="h4" gutterBottom>
          Registriraj se
        </Typography>
        <form onSubmit={handleSubmit}>
          {/* Input fields for registration */}
          <TextField
            type="email"
            label="Email"
            fullWidth
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <TextField
            type="password"
            label="Lozinka"
            fullWidth
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <TextField
            type="password"
            label="Ponovljena lozinka"
            fullWidth
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <TextField
            type="text"
            label="Ime"
            fullWidth
            value={firstName}
            onChange={(e) => setFirstName(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <TextField
            type="text"
            label="Prezime"
            fullWidth
            value={lastName}
            onChange={(e) => setLastName(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <TextField
            type="tel"
            label="Broj telefona"
            fullWidth
            value={phoneNumber}
            onChange={(e) => setPhoneNumber(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <TextField
            type="text"
            label="Naziv organizacije"
            fullWidth
            value={organizationName}
            onChange={(e) => setOrganizationName(e.target.value)}
            className="register-input"
            style={{ marginBottom: '0.55rem' }}
            required
          />
          <Button variant="contained" type="submit" fullWidth className="register-button">
            Registriraj se
          </Button>
        </form>
        {error && <Typography variant="body2" color="error">{error}</Typography>}
        <Button color="primary" onClick={handleLoginClick} style={{ marginTop: '0.55rem' }}>
          Natrag
        </Button>
      </Paper>
    </Grid>
  );
}

export default Register;
