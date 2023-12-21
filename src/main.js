import './assets/main.css';
import { createApp } from 'vue';
import App from './App.vue';

const app = createApp(App);

// Fetch data from WordPress and pass it to the app
fetch('https://genteknik.local/wp-json/vr-modal/v1/modal-data')
    .then(response => response.json())
    .then(data => {
        app.config.globalProperties.$modalData = data;
        app.mount('#app');
    })
    .catch(error => {
        console.error('Error fetching data from WordPress:', error);
    });
