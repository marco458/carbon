import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Link } from 'react-router-dom';
import Masonry from '@mui/lab/Masonry';
import { Box, Typography, Button } from '@mui/material';
import { styled } from '@mui/system';
import UploadPopup from './UploadPopup';

const StyledLink = styled(Link)(({ theme }) => ({
  width: '100%',
  height: '100%',
  boxShadow: '0px 3px 10px rgba(0, 0, 0, 0.1)',
  textDecoration: 'none',
  fontSize: '20px',
  color: 'black',
  borderRadius: '15px',
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  transition: 'box-shadow 0.3s ease, transform 0.3s ease',
  '&:hover': {
    boxShadow: '0px 3px 12px rgba(0, 0, 0, 0.15)', // Significantly reduced hover effect
    transform: 'scale(1.01)', // Significantly reduced hover effect
  },
}));

const sectors = [
  { path: '/sector/fuel', label: 'Goriva', height: 200 },
  { path: '/sector/energy', label: 'Električna energija', height: 300 },
  { path: '/sector/heat', label: 'Toplina', height: 250 },
  { path: '/sector/passenger', label: 'Putnički promet', height: 400 },
  { path: '/sector/freight', label: 'Teretni Promet', height: 150 },
  { path: '/sector/air', label: 'Rashladni uređaji', height: 350 },
  { path: '/sector/land', label: 'Prenamjena zemljišta', height: 275 },
  { path: '/sector/waste', label: 'Otpad', height: 225 },
];

function Home() {
  const navigate = useNavigate();
  const [isPopupOpen, setIsPopupOpen] = useState(false);

  useEffect(() => {
    const isAuthenticated = localStorage.getItem('token');
    if (!isAuthenticated) {
      console.log('Token not present');
      navigate('/login');
    } 
  }, [navigate]);

  const handleOpenPopup = () => {
    setIsPopupOpen(true);
  };

  const handleClosePopup = () => {
    setIsPopupOpen(false);
  };

  return (
    <Box sx={{ backgroundColor: '#fff', padding: '2rem' }}>
      <Button variant="contained" color="primary" onClick={handleOpenPopup} sx={{ mb: 2 }}>
        Učitaj csv podatke
      </Button>
      <Masonry columns={{ xs: 1, sm: 2, md: 3 }} spacing={2}>
        {sectors.map((sector, index) => (
          <Box
            key={index}
            sx={{
              height: sector.height, 
              backgroundColor: index % 3 === 2 ? '#7bed7b' : index % 2 === 0 ? '#2f83d6' : '#ffffff', 
              borderRadius: '15px' 
            }}
          >        
            <StyledLink to={sector.path}>
              <Typography variant="h5">{sector.label}</Typography>
            </StyledLink>
          </Box>
        ))}
      </Masonry>
      <UploadPopup open={isPopupOpen} handleClose={handleClosePopup} />
    </Box>
  );
}

export default Home;
