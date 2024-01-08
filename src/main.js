/*
 * Vue App Initialization Script
 * Author: Dennis Kjellin
 * Date: 2023-12-01
 * Description: This script initializes a Vue.js application, fetches data from a WordPress 
 *              endpoint, and mounts the app with the retrieved data. Error handling is
 *              implemented to log any issues during data fetching or app 
 *              initialization.
 */

// Import the main CSS file
import './assets/main.css';

// Import the Vue.js framework and the root component
import { createApp } from 'vue';
import App from './App.vue';

// Create the Vue app instance
const app = createApp(App);

// Declare currentDomain outside the try block
let currentDomain;

// Function to fetch data asynchronously
async function fetchData(url) {
    try {
        // Fetching data from the provided endpoint
        const response = await fetch(url);

        // Parsing the response as JSON
        const data = await response.json();

        // Returning the data
        return data;

    } catch (fetchError) {
        // Handle errors during data fetching
        console.error('Error fetching data:', fetchError);
        throw fetchError;
    }
}

// async function to fetch data and mount the app
(async () => {
    let urlEndpoint; // Declare urlEndpoint outside the try block

    try {
        // Construct the WordPress endpoint URL based on the current domain
        currentDomain = window.location.hostname;
        urlEndpoint = `https://${currentDomain}/wp-json/vr-modal/v1/modal-data`;

        const modalData = await fetchData(urlEndpoint);

        // Check if the user has already seen the popup (based on local storage)
        app.mount('#vr-modal');

    } catch (error) {
        console.error('Error during app initialization:', error);
    }

})();
