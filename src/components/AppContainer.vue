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
      popupDiv.remove();
    },
    fetchPopupData() {
      
  const currentDomain = window.location.hostname;
  const urlEndpoint = `https://${currentDomain}/wp-json/vr-modal/v1/modal-data`;

  fetch(urlEndpoint)
    .then(response => response.json())
    .then(data => {
      // Check if data is not empty and has at least one post
      if (data.length > 0) {
        const firstPost = data[0];

        // Update popupData with the data from the first post
        this.popupData.title = firstPost.title;
        this.popupData.description = firstPost.description;
        // If 'linkTitle' and 'link' are present in the post data, update them as well
        if ('linkTitle' in firstPost && 'link' in firstPost) {
          this.popupData.linkTitle = firstPost.linkTitle;
          this.popupData.link = firstPost.link;
        }
      }

      // Move setting isPopupVisible to here
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
#vr-modal {
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
