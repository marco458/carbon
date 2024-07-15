import { Link } from "react-router-dom";
import React, { useState } from 'react';

const SectorView = ({ factorTitle, factors, fqcn, factorType = ''}) => {
    return (
            <div className="sector-view-div">
            <p className="link-p">{factorTitle}</p>
            {factors && factors.map((x) => {
                
                let description = '';
                if (fqcn === 'land' || fqcn === 'air' || fqcn === 'waste') {
                    description = x.category;
                } else if (fqcn === 'energy' && factorType === '') {
                    description = x.energy_type + ' ' + x.year;
                } else if (fqcn === 'energy' && factorType === 'power') {
                    description = x.power_plant_type;
                } else if (fqcn === 'freight') {
                    description = x.vehicle_type + ' ' +  x.fuel_and_load + ' ' +  x.euro_standard;
                } else if (fqcn === 'passenger') {
                    description = x.vehicle_type + ' ' + x.vehicle_class + ' ' + x.fuel + ' ' + x.euro_standard;
                } else if (fqcn === 'fuel') {
                    description = x.fuel_group + ' ' + x.fuel_type + ' ' + x.type_of_energy_source;
                } else if (fqcn === 'heat' && factorType === '') {
                    description = x.energy_type + ' ' + x.location;
                } else if (fqcn === 'heat' && factorType === 'technology') {
                    description = x.energy_type + ' ' + x.technology;
                } 
                
                return (
                    <Link key={x.id} className="link" to={'/factor-users'}
                        state={{ id: x.id, fqcn: fqcn, factorDescription: description, unit: x.unit.measuring_unit }}>
                        
                        {(fqcn === 'land' || fqcn === 'air' || fqcn === 'waste') &&
                            <div className="link-div" key={x.id}>
                                <p>{x.category} ({x.unit.measuring_unit})</p>
                            </div>}
                        
                        {(fqcn === 'energy' && factorType === '') &&
                            <div className="link-div" key={x.id}>
                                <p>{x.energy_type} {x.year} ({x.unit.measuring_unit})</p>
                            </div>}

                        {(fqcn === 'energy' && factorType === 'power') &&
                        <div className="link-div" key={x.id}>
                            <p>{x.power_plant_type} ({x.unit.measuring_unit})</p>
                        </div>}
                    
                        {(fqcn === 'freight') &&
                        <div className="link-div" key={x.id}>
                            <p>{x.vehicle_type} {x.fuel_and_load} {x.euro_standard} ({x.unit.measuring_unit})</p>
                        </div>}

                        {(fqcn === 'passenger') &&
                        <div className="link-div" key={x.id}>
                            <p>{x.vehicle_type} {x.vehicle_class} {x.fuel} {x.euro_standard} ({x.unit.measuring_unit})</p>
                        </div>}

                        {(fqcn === 'fuel') &&
                        <div className="link-div" key={x.id}>
                            <p>{x.fuel_group} {x.fuel_type} {x.type_of_energy_source} ({x.unit.measuring_unit})</p>
                        </div>}

                        {(fqcn === 'heat' && factorType === '') &&
                        <div className="link-div" key={x.id}>
                            <p>{x.energy_type} {x.location} ({x.unit.measuring_unit})</p>
                        </div>}

                        {(fqcn === 'heat' && factorType === 'technology') &&
                        <div className="link-div" key={x.id}>
                            <p>{x.energy_type} {x.technology} ({x.unit.measuring_unit})</p>
                        </div>}

                    </Link>
                );
            })}
            </div>
    );
}

export default SectorView;