import React, { useState, useEffect } from 'react';
import { TextField, Button, FormControl, InputLabel, Select, MenuItem } from '@mui/material';
import { useNavigate } from "react-router-dom";
import { useLocation } from 'react-router-dom';

// google-chrome --user-data-dir="~/chrome-dev-disabled-security" --disable-web-security --disable-site-isolation-trials

const Report = () => {
    const location = useLocation();
    const [loader, setLoader] = useState(false);
    const [error, setError] = useState('');
    const [locations, setLocations] = useState([]);

    const [formData, setFormData] = useState({
        from_date: '',
        to_date: '',
        location_id: ''
    });

    const navigate = useNavigate();

    useEffect(() => {
        const fetchLocations = async () => {
            try {
                const response = await fetch(process.env.REACT_APP_BACKEND_API_URL + '/locations', {
                    headers: {
                        'Authorization': `${localStorage.getItem('token')}`
                    }
                });
                if (!response.ok) {
                    throw new Error('Error fetching locations');
                }
                const data = await response.json();
                setLocations(data);
            } catch (error) {
                console.error('Error fetching locations:', error);
                setError('Pogreška pri dohvaćanju lokacija');
            }
        };

        fetchLocations();
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prevData) => ({
            ...prevData,
            [name]: value,
        }));
    };

    const handleSubmit = async (e) => {
        setLoader(true);
        setError('');
        e.preventDefault();
        const token = localStorage.getItem('token');

      // Remove locationId if it is null
      if (formData.location_id === 'all') {
          delete formData.location_id;
      }

        try {
            const response = await fetch(process.env.REACT_APP_BACKEND_API_URL + '/report', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    'Authorization': `${token}`,
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                throw new Error('Error generating PDF report');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'izvjestaj.pdf');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link); // Remove the link after download
            console.log('PDF report downloaded successfully');
        } catch (error) {
            console.error('Error generating or downloading PDF report:', error);
            setError('Pogreška pri generiranju ili preuzimanju PDF izvješća');
            // Handle error as needed
        } finally {
            setLoader(false);
        }
    };

    return (
        <form className="form-container" onSubmit={handleSubmit}>
            <TextField
                label="Datum od"
                name="from_date"
                type="date"
                value={formData.from_date}
                onChange={handleChange}
                InputLabelProps={{ shrink: true }}
                required
            />
            <TextField
                label="Datum do"
                name="to_date"
                type="date"
                value={formData.to_date}
                onChange={handleChange}
                InputLabelProps={{ shrink: true }}
                required
            />

            <FormControl fullWidth>
                <InputLabel id="location">Lokacija</InputLabel>
                <Select
                    labelId="location"
                    name="location_id"
                    value={formData.location_id}
                    onChange={handleChange}
                    label="Lokacija"
                    required
                >
                   <MenuItem value="all">Cijela organizacija</MenuItem>
                    {locations && locations.items && locations.items.map((location) => (
                        <MenuItem key={location.id} value={location.id}>
                            {location.name}
                        </MenuItem>
                    ))}
                </Select>
            </FormControl>

            <Button
                variant="contained"
                color="primary" // Green color for submit button
                type="submit"
                sx={{
                    marginTop: '1rem',
                    alignSelf: 'center',
                }}
            >
                Kreiraj izvještaj
            </Button>

            {loader && <p>Izvještaj se generira...</p>}
            {error && <p style={{ color: 'red' }}>{error}</p>}
        </form>
    );
};

export default Report; 
