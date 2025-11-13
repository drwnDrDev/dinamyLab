import { useState, useEffect } from 'react';
import axios from 'axios';

axios.defaults.withCredentials = true;

const useAxiosAuth = () => {
    const [csrfLoaded, setCsrfLoaded] = useState(false);
    const [error, setError] = useState(null);

    const getCsrfCookie = async () => {
        try {
            await axios.get('/sanctum/csrf-cookie');
            console.log('✅ CSRF cookie obtenida');
            setCsrfLoaded(true);
            setError(null);
        } catch (err) {
            console.error('❌ Error al obtener CSRF cookie:', err);
            setError(err.message);
            setCsrfLoaded(false);
        }
    };

    // Obtener cookie al montar
    useEffect(() => {
        getCsrfCookie();
    }, []);

    // Interceptor para detectar expiración y renovar
    useEffect(() => {
        const responseInterceptor = axios.interceptors.response.use(
            response => response,
            async error => {
                if (error.response?.status === 401) {
                    console.warn('⚠️ Sesión expirada, renovando cookie...');
                    await getCsrfCookie();
                }
                return Promise.reject(error);
            }
        );

        return () => {
            axios.interceptors.response.eject(responseInterceptor);
        };
    }, []);

    return { axiosInstance: axios, csrfLoaded, error };
};

export default useAxiosAuth;