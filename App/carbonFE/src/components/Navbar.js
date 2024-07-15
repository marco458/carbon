import React from 'react';
import { AppBar, Toolbar, Typography, IconButton, Button } from '@mui/material';
import { ExitToApp as ExitToAppIcon } from '@mui/icons-material';
import { useLocation, Link } from 'react-router-dom';
import { useNavigate } from 'react-router-dom';

const Navbar = () => {
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('token');
    navigate('/login');
  };

  const location = useLocation();
  let renderNavbar = true;
  if (location.pathname === '/login' || location.pathname === '/register') {
    renderNavbar = false;
  }

  return (
    <div>
      {renderNavbar && (
        <AppBar position="fixed" className="appBar">
          <Toolbar>
            <div style={{ display: 'flex', justifyContent: 'space-between', width: '100%' }}>
              <div className='navbar-button'>
                <Button color="inherit"  sx={{ fontSize: '1rem' }} component={Link} to="/">Sustav praćenja Ugljičnog otiska</Button>
              </div>
              <div className='navbar-button'>
                <Button color="inherit"  sx={{ fontSize: '1rem' }} component={Link} to="/report">Kreiraj izvještaj</Button>
              </div>
              <div className='navbar-button'>
                <Button color="inherit"  sx={{ fontSize: '1rem' }} component={Link} to="/organization">Podaci organizacije</Button>
              </div>
              <div>
                <IconButton 
                  color="inherit"
                  aria-label="logout" 
                  onClick={handleLogout}
                  title="Odjava"
                >
                  <ExitToAppIcon style={{ fontSize: '3rem' }}/>
                </IconButton>
              </div>
            </div>
          </Toolbar>
        </AppBar>
      )}
    </div>
  );
};

export default Navbar;
