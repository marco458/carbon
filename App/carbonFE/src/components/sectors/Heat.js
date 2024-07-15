import useFetch from "../../useFetch";
import SectorView from "./SectorView";

const Heat = () => {

    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data, isPending, error } = useFetch(apiUrl + '/heats', 'GET',{ page: '1', items_per_page: '100' });

    return ( 
        <div>
            <p className="link-p">Unesite novi zapis u bazu podataka</p>
            { (data) && (<SectorView 
                factorTitle="JAVNE KOTLOVNICE - FAKTORI EMISIJA STAKLENIČKIH PLINOVA"
                factors={data.filter((factor) => factor.sub_sector === 'Public boiler houses')}
                fqcn="heat"
                factorType=""
                />
                )}        

            { (data) && (<SectorView 
                factorTitle="JAVNE TOPLANE - FAKTORI EMISIJA STAKLENIČKIH PLINOVA"
                factors={data.filter((factor) => factor.sub_sector === 'Public heating plants')}
                fqcn="heat"
                factorType=""
                />
                )}        


            { (data) && (<SectorView 
                factorTitle="SUSTAVI ZA PROIZVODNJU TOPLINE - FAKTORI EMISIJA STAKLENIČKIH PLINOVA"
                factors={data.filter((factor) => factor.sub_sector === 'Heat production systems')}
                fqcn="heat"
                factorType="technology"
                />
                )}      


          </div>
    );
}
 
export default Heat;