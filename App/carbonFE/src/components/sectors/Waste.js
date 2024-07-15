import { Link } from "react-router-dom";
import React, { useState } from 'react';
import useFetch from "../../useFetch";
import SectorView from "./SectorView";

const Waste = () => {

    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data, isPending, error } = useFetch(apiUrl + '/wastes', 'GET',{ page: '1', items_per_page: '100' });

    return ( 
        <div>
            <p className="link-p">Unesite novi zapis u bazu podataka</p>
            { (data) && (<SectorView 
                factorTitle="FAKTORI EMISIJA STAKLENIČKIH PLINOVA ZA OTPAD"
                factors={data}
                fqcn="waste"
                factorType=""
                 />
                )}
        </div>
     );
}
 
export default Waste;