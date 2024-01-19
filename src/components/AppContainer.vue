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
        content: '',
        btnText: '',
        url: '',
      },
    };
  },
  watch: {
    popupData: {
      handler(newData) {
        // Check if there is valid data available before showing the popup
        if (this.hasValidData(newData)) {
          this.isPopupVisible = true;
        }
      },
      deep: true,
    },
  },
  mounted() {
    // Fetch data from WordPress and update popupData
    this.fetchPopupData();
  },
  methods: {
    closePopup() {
      // Hide popup and remove it from the DOM when closed
      this.isPopupVisible = false;
      const popupDiv = document.getElementById('vr-modal');
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

            console.log('Title:', firstPost.vrm_title);
            console.log('Content:', firstPost.vrm_content);
            console.log('Button Title:', firstPost.vrm_button_title);
            console.log('Button URL:', firstPost.vrm_button_url);

            console.log('firstPost Title:', firstPost.vrm_title);
            console.log('firstPost Content:', firstPost.vrm_content);
            console.log('firstPost Button Title:', firstPost.vrm_button_title);
            console.log('firstPost Button URL:', firstPost.vrm_button_url);



            // Update popupData with the data from the first post
            this.popupData.title = firstPost.vrm_title;
            this.popupData.content = firstPost.vrm_content;
            // If 'vrm_button_title' and 'link' are present in the post data, update them as well
            if ('vrm_button_title' in firstPost && 'vrm_button_url' in firstPost) {
              this.popupData.btnText = firstPost.vrm_button_title;
              this.popupData.url= firstPost.vrm_button_url;
            }
          }
          console.log('Data from WordPress:', data)
        })
        .catch(error => {
          console.error('Error fetching data from WordPress:', error);
        });
    },
    hasValidData(data) {
      return (
        data.title ||
        data.content ||
        (data.btnText && data.url)
      );
    },
  },
};
</script>

<style lang="scss">
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

  h2 {
    font-family: Roboto, sans-serif, Arial, Helvetica;
  }
}
</style>
