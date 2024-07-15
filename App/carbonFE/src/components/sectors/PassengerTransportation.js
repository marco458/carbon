import { Link } from "react-router-dom";
import React, { useState } from 'react';
import useFetch from "../../useFetch";
import SectorView from "./SectorView";

const PassengerTransportation = () => {
    
    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data, isPending, error } = useFetch(apiUrl + '/passenger-transportations', 'GET',{ page: '1', items_per_page: '100' });

    return ( 
        <div>
            <p className="link-p">Unesite novi zapis u bazu podataka</p>
            { (data) && (<SectorView 
                factorTitle="PUTNIČKI PROMET - CESTOVNI"
                factors={data}
                fqcn="passenger"
                factorType=""
                 />
                )}   
        </div>
     );
}

export default PassengerTransportation;