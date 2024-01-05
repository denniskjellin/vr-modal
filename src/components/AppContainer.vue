<template>
  <div>
    <Popup v-if="isPopupVisible" :jsonData="popupData" @close="closePopup" />
  </div>
</template>

<script>
import Popup from './Popup.vue';

export default {
  name: 'AppContainer',
  components: {
    Popup,
  },
  data() {
    return {
      isPopupVisible: false,
      popupData: {
        title: '',
        description: '',
        linkTitle: '',
        link: '',
      },
    };
  },
  mounted() {
    // Fetch data from WordPress and update popupData
    this.fetchPopupData();
  },
  methods: {
    openPopup() {
      this.isPopupVisible = true;
    },
    closePopup() {
      // Hide popup and remove it from the DOM when closed
      this.isPopupVisible = false;
      const popupDiv = document.getElementById('app');
      popupDiv.style.display = 'none';
    },
    fetchPopupData() {
       let urlEndpoint; 
       currentDomain = window.location.hostname;
       urlEndpoint = `https://${currentDomain}/wp-json/vr-modal/v1/modal-data`;
      fetch(urlEndpoint)
        .then(response => response.json())
        .then(data => {
          // Update popupData with the fetched data
          this.popupData = data;
          this.isPopupVisible = true;
        })
        .catch(error => {
          console.error('Error fetching data from WordPress:', error);
        });
    },
  },
};
</script>

<style>
#app {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  color: #000;
  font-family: Roboto, sans-serif, Arial, Helvetica;
}
</style>
