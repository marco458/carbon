import React, { useState } from 'react';
import { Modal, Box, Button, Typography, IconButton, TextField, MenuItem } from '@mui/material';
import { styled } from '@mui/system';
import CloseIcon from '@mui/icons-material/Close';
import { useNavigate } from "react-router-dom";

const StyledModalBox = styled(Box)(({ theme }) => ({
  position: 'absolute',
  top: '50%',
  left: '50%',
  transform: 'translate(-50%, -50%)',
  width: '35%',
  backgroundColor: '#fff',
  border: '2px solid #000',
  boxShadow: 24,
  padding: theme.spacing(4),
  borderRadius: '15px',
  outline: 'none',
}));

const Header = styled(Box)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  marginBottom: theme.spacing(2),
}));

const LocationPopup = ({ open, handleClose }) => {
  const navigate = useNavigate();
  const [message, setMessage] = useState(null);
  const [messageType, setMessageType] = useState(null);
  const [locationName, setLocationName] = useState('');
  const [locationDescription, setLocationDescription] = useState('');
  const [locationLevel1, setLocationLevel1] = useState('nekretnine');
  const [locationLevel2, setLocationLevel2] = useState('administracija');
  const [refetch, setRefetch] = useState(0);

  const handleAdd = async () => {
    const data = {
      name: locationName,
      description: locationDescription,
      level1: locationLevel1,
      level2: locationLevel2
    };

    const token = localStorage.getItem('token');

    try {
      const response = await fetch(process.env.REACT_APP_BACKEND_API_URL + '/locations', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `${token}`,
        },
        body: JSON.stringify(data),
      });

      if (response.ok) {
        setMessage('Lokacija uspješno dodana');
        setMessageType('success');
        handleClosePopup();
      } else {
        setMessage('Pogreška prilikom dodavanja lokacije');
        setMessageType('error');
      }
    } catch (error) {
      setMessage('Došlo je do pogreške prilikom dodavanja lokacije');
      setMessageType('error');
    }
  };

  const handleClosePopup = () => {
    setMessage(null);
    setMessageType(null);
    handleClose();
  };

  const handleCloseWithRefetch = () => {
    setRefetch(prevRefetch => prevRefetch + 1);
    handleClose();
  };

  return (
    <Modal open={open} onClose={handleCloseWithRefetch}>
      <StyledModalBox>
        <Header>
          <Typography variant="h6">Dodajte novu lokaciju</Typography>
          <IconButton onClick={handleClosePopup}>
            <CloseIcon />
          </IconButton>
        </Header>
        <Box>
          <TextField
            label="Naziv"
            value={locationName}
            onChange={(e) => setLocationName(e.target.value)}
            fullWidth
            margin="normal"
          />
          <TextField
            label="Opis"
            value={locationDescription}
            onChange={(e) => setLocationDescription(e.target.value)}
            fullWidth
            margin="normal"
          />
          <TextField
            select
            label="Razina 1"
            value={locationLevel1}
            onChange={(e) => setLocationLevel1(e.target.value)}
            fullWidth
            margin="normal"
          >
            <MenuItem value="nekretnine">Nekretnine</MenuItem>
            <MenuItem value="pokretnine">Pokretnine</MenuItem>
            <MenuItem value="ostalo">Ostalo</MenuItem>
          </TextField>
          <TextField
            select
            label="Razina 2"
            value={locationLevel2}
            onChange={(e) => setLocationLevel2(e.target.value)}
            fullWidth
            margin="normal"
          >
            <MenuItem value="administracija">Administracija</MenuItem>
            <MenuItem value="proizvodnja">Proizvodnja</MenuItem>
            <MenuItem value="ostalo">Ostalo</MenuItem>
          </TextField>
          <Button variant="contained" onClick={handleAdd}>
            Dodaj lokaciju
          </Button>
        </Box>
      </StyledModalBox>
    </Modal>
  );
};

export default LocationPopup;
