import { Link } from "react-router-dom";
import React, { useState } from 'react';
import useFetch from "../../useFetch";
import SectorView from "./SectorView";

const Fuel = () => {
    
    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data, isPending, error } = useFetch(apiUrl + '/fuels', 'GET',{ page: '1', items_per_page: '100' });

    return ( 
        <div>
            <p className="link-p">Unesite novi zapis u bazu podataka</p>
            { (data) && (<SectorView 
                factorTitle="FOSILNA GORIVA - FAKTORI EMISIJA STAKLENIČKIH PLINOVA"
                factors={data.filter((factor) => factor.sub_sector === 'fossil fuels')}
                fqcn="fuel"
                factorType=""
                />
                )}      

            { (data) && (<SectorView 
                factorTitle="BIOMASA - FAKTORI EMISIJA STAKLENIČKIH PLINOVA"
                factors={data.filter((factor) => factor.sub_sector === 'biomass green houses')}
                fqcn="fuel"
                factorType=""
                />
                )}      

            { (data) && (<SectorView 
                factorTitle="BIOMASA - FAKTORI BIOGENIH EMISIJA CO2"
                factors={data.filter((factor) => factor.sub_sector === 'biomass biogenic emissions')}
                fqcn="fuel"
                factorType=""
                />
                )}   
        </div>
     );
}

export default Fuel;