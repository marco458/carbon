import useFetch from "../../useFetch";
import SectorView from "./SectorView";

const ElectricalEnergy = () => {

    const apiUrl = process.env.REACT_APP_BACKEND_API_URL;
    const { data, isPending, error } = useFetch(apiUrl + '/electrical-energies', 'GET',{ page: '1', items_per_page: '100' });

    return ( 
        <div>
            <p className="link-p">Unesite novi zapis u bazu podataka</p>
            { (data) && (<SectorView 
                factorTitle="FAKTORI EMISIJA STAKLENIČKIH PLINOVA ZA ELEKTRIČNU ENERGIJU - PROSJEK ZA POTROŠNJU U HRVATSKOJ"
                factors={data.filter((factor) => factor.sub_sector === 'average consumption')}
                fqcn="energy"
                factorType=""
                />
                )}        

            { (data) && (<SectorView 
                factorTitle="FAKTORI EMISIJA STAKLENIČKIH PLINOVA ZA ELEKTRIČNU ENERGIJU IZ OBNOVLJIVIH IZVORA U HRVATSKOJ"
                factors={data.filter((factor) => factor.sub_sector === 'renewable sources')}
                fqcn="energy"
                factorType=""
                />
                )}        


            { (data) && (<SectorView 
                factorTitle="FAKTORI EMISIJA STAKLENIČKIH PLINOVA ZA ELEKTRIČNU ENERGIJU IZ ELEKTRANA NA OBNOVLJIVE IZVORE"
                factors={data.filter((factor) => factor.sub_sector === 'renewable power plant')}
                fqcn="energy"
                factorType="power"
                />
                )}      


          </div>
    );
}
 
export default ElectricalEnergy;