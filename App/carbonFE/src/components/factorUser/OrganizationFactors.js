import React, { useState } from 'react';
import useFetch from '../../useFetch';
import { CircularProgress, Typography, Paper, List, ListItem, ListItemText, Divider, IconButton, Button } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import LocationPopup from '../LocationPopup';

const remapFactorFqcn = (factorFqcn) => {
    const mapping = {
        'App\\Entity\\Fuel\\Fuel': 'Goriva',
        'App\\Entity\\ElectricalEnergy\\ElectricalEnergy': 'Električna energija',
        'App\\Entity\\Transportation\\PassengerTransportation': 'Putnički promet',
        'App\\Entity\\Transportation\\FreightTransportation': 'Teretni promet',
        'App\\Entity\\Heat\\Heat': 'Toplina',
        'App\\Entity\\LandConversion\\LandConversion': 'Prenamjena zemljišta',
        'App\\Entity\\Waste\\Waste': 'Otpad',
        'App\\Entity\\AirConditioning\\AirConditioning': 'Rashladni uređaji',        
    };
    return mapping[factorFqcn] || factorFqcn;
};

const remapGasActivity = (gasActivity) => {
    const mapping = {
        'upstream': 'Ulazni tok', 
        'combustion': 'Izgaranje',
        'waste treatment': 'Obrada otpada',
    };
    if (gasActivity === null) {
        return '';
    }
    return mapping[gasActivity] || gasActivity;
};

const remapConsumption = (consumption) => {
    const mapping = {
        'direct': 'Izravna',
        'indirect': 'Neizravna'
    };
    return mapping[consumption] || consumption;
};

const OrganizationFactors = () => {
    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const [refetch, setRefetch] = useState(0);
    const { data: factorUserData, isPending: factorUserIsPending, error: factorUserError } = useFetch(apiUrl + '/factor-users?d=' + refetch, 'GET', { page: '1', items_per_page: '500', user: localStorage.getItem('user')}, [refetch]);
    const { data: locationsData, isPending: locationsIsPending, error: locationsError } = useFetch(apiUrl + '/locations?d=' + refetch, 'GET', { page: '1', items_per_page: '500', user_id: localStorage.getItem('user')}, [refetch]);

    const [isLocationPopupOpen, setLocationPopupOpen] = useState(false);

    const handleDelete = async (id) => {
        const token = localStorage.getItem('token');
        try {
            await fetch(apiUrl + '/factor-users/' + id, {
                method: 'DELETE',
                headers: {
                    'Authorization': `${token}`,
                }
            });
            setRefetch(refetch + 1);
        } catch (error) {
            console.error('Error deleting factor:', error);
        }
    };

    const handleLocationDelete = async (id) => {
        const token = localStorage.getItem('token');
        try {
            await fetch(apiUrl + '/locations/' + id, {
                method: 'DELETE',
                headers: {
                    'Authorization': `${token}`,
                }
            });
            setRefetch(refetch + 1);
        } catch (error) {
            console.error('Error deleting location:', error);
        }
    };

    const formatDate = (date) => {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Intl.DateTimeFormat('en-GB', options).format(new Date(date));
    };

    return (
        <Paper elevation={3} className="container">
            <Typography variant="h4" gutterBottom>
                Faktori i lokacije organizacije ({factorUserData && factorUserData[0]?.user?.organization_name})
            </Typography>
            <br/>

            <LocationPopup open={isLocationPopupOpen} handleClose={() => { setLocationPopupOpen(false); setRefetch(refetch + 1); }} />

            <Typography variant="h5" gutterBottom>
                Trenutne lokacije:
            </Typography>

            {locationsIsPending && <CircularProgress className="loading" />}
            {locationsError && <Typography variant="body2" color="error" className="error">{locationsError}</Typography>}
            {locationsData && locationsData.items && (
                <List>
                    {locationsData.items.map((location, index) => (
                        <React.Fragment key={location.id}>
                            <ListItem>
                                <ListItemText
                                    primary={`${index + 1}. ${location.name}`}
                                    secondary={
                                        ` ID: ${location.id} |
                                        Razina1: ${location.level1} | 
                                        Razina2: ${location.level2} |
                                        Opis: ${location.description}`
                                    }
                                />
                                <IconButton onClick={() => handleLocationDelete(location.id)} edge="end" aria-label="delete">
                                    <DeleteIcon />
                                </IconButton>
                            </ListItem>
                            <Divider />
                        </React.Fragment>
                    ))}
                </List>
            )}

            <br/>
            <Button variant="contained" color="primary" onClick={() => setLocationPopupOpen(true)}>
                Dodaj lokaciju
            </Button>

            <br/><br/><br/><br/>
            <Typography variant="h5" gutterBottom>
                Uneseni faktori:
            </Typography>
            {factorUserIsPending && <CircularProgress className="loading" />}
            {factorUserError && <Typography variant="body2" color="error" className="error">{factorUserError}</Typography>}
            {factorUserData && (
                <List>
                    {factorUserData.map((factorUser, index) => (
                        <React.Fragment key={factorUser.factor_user_id}>
                            <ListItem>
                                <ListItemText
                                    primary={`${index + 1}. ${remapFactorFqcn(factorUser.factor_fqcn)}`}
                                    secondary={
                                        `Količina: ${factorUser.amount} ${factorUser.unit}  | 
                                        Godina: ${factorUser.year} | 
                                        Datum: ${formatDate(factorUser.date)} |
                                        Aktivnost plina: ${remapGasActivity(factorUser.gas_activity)} |
                                        Potrošnja: ${remapConsumption(factorUser.consumption)}`
                                    }
                                />
                                <IconButton onClick={() => handleDelete(factorUser.factor_user_id)} edge="end" aria-label="delete">
                                    <DeleteIcon />
                                </IconButton>
                            </ListItem>
                            <Divider />
                        </React.Fragment>
                    ))}
                </List>
            )}
        </Paper>
    );
};

export default OrganizationFactors;
