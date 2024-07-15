import useFetch from "../../useFetch";
import SectorView from "./SectorView";

const AirConditioning = () => {

    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data, isPending, error } = useFetch(apiUrl + '/air-conditionings', 'GET',{ page: '1', items_per_page: '100' });

    return ( 
        <div>
            <p className="link-p">Unesite novi zapis u bazu podataka</p>
            { (data) && (<SectorView 
                factorTitle="FAKTORI EMISIJA STAKLENIČKIH PLINOVA ZA RADNE TVARI"
                factors={data}
                fqcn="air"
                factorType=""
                />
                )}        
        </div>
    );
}
 
export default AirConditioning;