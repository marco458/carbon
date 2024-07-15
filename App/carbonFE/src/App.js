// App.js
import './App.css';

import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Home from './components/Home';
import Login from './components/Login';
import Register from './components/Register';

import Fuel from './components/sectors/Fuel';
import AirConditioning from './components/sectors/AirConditioning';
import FreightTransportation from './components/sectors/FreightTransportation';
import PassengerTransportation from './components/sectors/PassengerTransportation';
import Heat from './components/sectors/Heat';
import LandConversion from './components/sectors/LandConversion';
import Waste from './components/sectors/Waste';
import ElectricalEnergy from './components/sectors/ElectricalEnergy';

import '@fontsource/roboto/300.css';
import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@fontsource/roboto/700.css'; 
import NotFound from './NotFound';
import Navbar from './components/Navbar';
import FactorUser from './components/factorUser/factorUser';
import Report from './components/factorUser/Report';
import OrganizationFactors from './components/factorUser/OrganizationFactors';

function App() {
  return (

    <Router>
    <head>
        <meta name="viewport" content="initial-scale=1, width=device-width" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    </head>
      <div>
      <Navbar />
      <div className='content'>
      <Routes>
        <Route path="/" exact element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />

        <Route path="/sector/fuel" element={<Fuel />} />
        <Route path="/sector/energy" element={<ElectricalEnergy />} />
        <Route path="/sector/heat" element={<Heat />} />
        <Route path="/sector/passenger" element={<PassengerTransportation />} />
        <Route path="/sector/freight" element={<FreightTransportation />} />
        <Route path="/sector/land" element={<LandConversion />} />
        <Route path="/sector/waste" element={<Waste />} />
        <Route path="/sector/air" element={<AirConditioning />} />
        <Route path="/report" element={<Report/>} />

        <Route path="/factor-users" element={<FactorUser />}/>
        <Route path="/organization" element={<OrganizationFactors />}/>
        

        <Route path="*" element={<NotFound />}/> 
      </Routes>
      </div>
      </div>
    </Router>
  );
}

export default App;