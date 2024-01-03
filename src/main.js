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

// Function to fetch data asynchronously
async function fetchData() {
    try {
        // Fetching data from a WordPress endpoint
        const response = await fetch('https://genteknik.local/wp-json/vr-modal/v1/modal-data');
        // Parsing the response as JSON
        const data = await response.json();
        // Returning the data
        return data;
    } catch (fetchError) {
        // Handle errors during data fetching
        console.error('Error fetching data from WordPress:', fetchError);
        throw fetchError;
    }
}

// async function to fetch data and mount the app
(async () => {
    try {
        // Fetching data from a WordPress endpoint
        const modalData = await fetchData();
        // Mounting the app with the retrieved data
        app.config.globalProperties.$modalData = modalData;
        // Mounting the app to the element with the id 'app'
        app.mount('#app');
    } catch (initError) {
        // Handle errors during data fetching or app mounting
        console.error('Error during app initialization:', initError);
    }
})();

