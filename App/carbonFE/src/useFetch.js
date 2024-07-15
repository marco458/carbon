import { useState, useEffect } from 'react';

    const useFetch = (url, method, params = {}, body) => {
        const[data, setData] = useState(null);
        const[isPending, setIsPending] = useState(false);
        const[error, setError] = useState(null);

    useEffect(() => {
        const abortCont = new AbortController();

        const searchParams = new URLSearchParams(params).toString();
        const fetchUrl = searchParams ? `${url}?${searchParams}` : url;
        console.log("sending request on " + fetchUrl)

        const token = localStorage.getItem('token');
        const headers = {
            'Authorization': `${token}`,
        };

        const requestOptions = {
            method,
            headers,
            signal: abortCont.signal,
        };

        if (method === 'POST' && body) {
            headers['Content-Type'] = 'application/json';
            requestOptions.body = JSON.stringify(body);
        }

        fetch(fetchUrl, requestOptions)
        .then(res => {
            if(!res.ok) {
                throw Error('could not fetch');
            }
            return res.json();
        })
        .then(data => {
            setData(data);
            setIsPending(false);
            setError(null);
        })
        .catch(err => {
            if (err.name === 'AbortError') {
                console.log('fetch aborted');
            } else {
                setIsPending(false);
                setError(err.message);
            }
        }) 

        return () => abortCont.abort();
    }, [url]);

    return {data, isPending, error};
}

export default useFetch;