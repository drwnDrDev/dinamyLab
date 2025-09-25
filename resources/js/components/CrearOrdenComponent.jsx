import React, { useState, useEffect } from 'react';
import DatosPaciente from './DatosPaciente';

const CrearOrdenComponent = () => {
    const [count, setCount] = useState(0);
    

    return (
            <>
            <DatosPaciente />
            </>
            

    );
};

export default CrearOrdenComponent;
