import React, { useState } from 'react';
import { TextField, Button, FormControl, InputLabel, Select, MenuItem, CircularProgress, Alert } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import { useLocation } from 'react-router-dom';
import useFetch from '../../useFetch';

const FactorUser = () => {
    const location = useLocation();
    console.log('state', location.state);

    const { id = '', fqcn = '', factorDescription = '', unit = '' } = location.state || {};

    const [formData, setFormData] = useState({
        factor_fqcn: fqcn,
        factor_id: id,
        amount: '',
        date: '',
        gas_activity: '',
        consumption: '',
        unit: unit,
        location: '',
    });

    const navigate = useNavigate();

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prevData) => ({
            ...prevData,
            [name]: name === 'location' ? `api/v1/locations/${value}` : value, // Update location to IRI format
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        const { gas_activity, consumption, ...rest } = formData;
        const dataToSubmit = {
            ...rest,
            gas_activity: gas_activity || null,
            consumption: consumption || null,
        };

        const token = localStorage.getItem('token');

        fetch(`${process.env.REACT_APP_BACKEND_API_URL}/factor-users`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                'Authorization': `${token}`,
            },
            body: JSON.stringify(dataToSubmit),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                console.log('New factor added');
                navigate(-1);
            })
            .catch(err => {
                console.error('Error occurred:', err);
                alert('Failed to save the factor. Please try again.');
            });
    };

    // Fetch locations data
    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data: locationsData, isPending: locationsIsPending, error: locationsError } = useFetch(
        `${apiUrl}/locations`,
        'GET',
        { page: '1', items_per_page: '500', user_id: localStorage.getItem('user') }
    );

    return (
        <form className="form-container" onSubmit={handleSubmit}>
            <p>{factorDescription}</p>
            <TextField
                label={`Količina ${unit}`}
                name="amount"
                value={formData.amount}
                onChange={handleChange}
                required
                fullWidth
                margin="normal"
            />
            <TextField
                type="date"
                label="Datum"
                name="date"
                value={formData.date}
                onChange={handleChange}
                required
                InputLabelProps={{ shrink: true }}
                fullWidth
                margin="normal"
            />
            <FormControl fullWidth margin="normal">
                <InputLabel id="gas-activity-label">Aktivnost plina</InputLabel>
                <Select
                    labelId="gas-activity-label"
                    name="gas_activity"
                    value={formData.gas_activity}
                    onChange={handleChange}
                    label="Aktivnost plina"
                >
                    <MenuItem value="upstream">Ulazni tok</MenuItem>
                    <MenuItem value="combustion">Izgaranje</MenuItem>
                    <MenuItem value="waste treatment">Obrada otpada</MenuItem>
                </Select>
            </FormControl>
            <FormControl fullWidth margin="normal">
                <InputLabel id="consumption-label">Potrošnja</InputLabel>
                <Select
                    labelId="consumption-label"
                    name="consumption"
                    value={formData.consumption}
                    onChange={handleChange}
                    label="Potrošnja"
                >
                    <MenuItem value="direct">Izravna</MenuItem>
                    <MenuItem value="indirect">Neizravna</MenuItem>
                </Select>
            </FormControl>
            <FormControl fullWidth margin="normal">
                <InputLabel id="location-label">Lokacija</InputLabel>
                {locationsIsPending ? (
                    <CircularProgress />
                ) : locationsError ? (
                    <Alert severity="error">Error loading locations</Alert>
                ) : (
                    <Select
                        labelId="location-label"
                        name="location"
                        value={formData.location.split('/').pop() || ''} // Extract ID from IRI for display
                        onChange={handleChange}
                        label="Lokacija"
                    >
                        {locationsData && locationsData.items.map((loc) => (
                            <MenuItem key={loc.id} value={loc.id}>
                                {loc.name}
                            </MenuItem>
                        ))}
                    </Select>
                )}
            </FormControl>
            <Button
                variant="contained"
                color="success"
                type="submit"
                sx={{ marginTop: '1rem', alignSelf: 'flex-end' }}
            >
                Submit
            </Button>
        </form>
    );
};

export default FactorUser;
