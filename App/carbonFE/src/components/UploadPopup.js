import React, { useState } from 'react';
import { Modal, Box, Button, Typography, IconButton, Alert } from '@mui/material';
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

const UploadPopup = ({ open, handleClose }) => {
  const navigate = useNavigate();
  const [file, setFile] = useState(null);
  const [message, setMessage] = useState(null);
  const [messageType, setMessageType] = useState(null);

  const handleFileChange = (event) => {
    setFile(event.target.files[0]);
  };

  const handleUpload = async () => {
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);

    const token = localStorage.getItem('token');

    try {
      const response = await fetch(process.env.REACT_APP_BACKEND_API_URL + '/import', {
        method: 'POST',
        headers: {
          'Authorization': `${token}`,
        },
        body: formData,
      });

      if (response.ok) {
        setMessage('Podaci uspješno pohranjeni');
        setMessageType('success');
      } else {
        setMessage('Podaci neuspješno pohranjeni');
        setMessageType('error');
      }
    } catch (error) {
      setMessage('Došlo je do pogreške tijekom pohrane');
      setMessageType('error');
    }
  };

  const handleClosePopup = () => {
    setMessage(null);
    setMessageType(null);
    handleClose();
  };

  return (
    <Modal open={open} onClose={handleClosePopup}>
      <StyledModalBox>
        <Header>
          <Typography variant="h6">Učitaj csv podatke</Typography>
          <IconButton onClick={handleClosePopup}>
            <CloseIcon />
          </IconButton>
        </Header>
        <Box>
          {message && (
            <Alert severity={messageType}>
              {message}
            </Alert>
          )}
          <input type="file" accept=".csv" onChange={handleFileChange} style={{ marginBottom: '16px', padding: '16px' }} />
          <Button variant="contained" onClick={handleUpload}>
            Učitaj
          </Button>
        </Box>
      </StyledModalBox>
    </Modal>
  );
};

export default UploadPopup;
