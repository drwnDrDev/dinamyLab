import React from "react";
import axios from "axios";

const TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const apiClient = axios.create({
    headers: {
        'X-CSRF-TOKEN': TOKEN,
        'Accept': 'application/json',
    }
});

export const fetchData = async (endpoint) => {
    try {
        const response = await apiClient.get(endpoint);
        return response.data;
    } catch (error) {
        console.error(`Error fetching data from ${endpoint}:`, error);
        throw error;
    }
}

