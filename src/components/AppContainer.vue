<template>
  <div v-if="showPopup">
    <Popup v-if="isPopupVisible" :jsonData="popupData" @close="closePopup" />
  </div>
</template>

<script>
import Popup from './Popup.vue'

export default {
  name: 'AppContainer',
  components: {
    Popup
  },
  data() {
    return {
      showPopup: false,
      isPopupVisible: false,
      popupData: {
        title: '',
        content: '',
        btnText: '',
        url: ''
      }
    }
  },
  watch: {
    popupData: {
      handler(newData) {
        // Check if there is valid data available before showing the popup
        if (this.hasValidData(newData)) {
          this.isPopupVisible = true
        }
      },
      deep: true
    }
  },
  mounted() {
    // Check if the URL contains a specific query parameter
    let url = window.location.search
    if (
      url.includes('?utm_medium=') ||
      url.includes('&utm_medium=') ||
      url.includes('?utm_source=') ||
      url.includes('&utm_source=')
    ) {
      // Set cookie to indicate that the modal has been shown
      document.cookie = 'vr_modal_cookie=vr_modal_shown; expires=' + 0 + '; path=/'

      // Optionally, you can also immediately hide the modal if it's already visible
      this.showPopup = false
      const overlayElement = document.getElementById('vr-modal')
      if (overlayElement) {
        overlayElement.style.display = 'none'
      }

      // No need to proceed with the rest of the logic
      return
    }

    // Continue with the rest of the logic...
    let showModal = true

    // Check if modal cookie is set
    let cookies = document.cookie.split(';')
    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i].split('=')
      if (cookie[0].trim() === 'vr_modal_cookie') {
        showModal = false
      }
    }

    if (showModal) {
      this.showPopup = true
      // Set cookie to prevent modal from showing again
      document.cookie = 'vr_modal_cookie=vr_modal_shown; expires=' + 0 + '; path=/'
    } else {
      const overlayElement = document.getElementById('vr-modal')
      if (overlayElement) {
        overlayElement.style.display = 'none'
      }
    }
    // Fetch data from WordPress and update popupData
    this.fetchPopupData()
  },
  methods: {
    closePopup() {
      // Hide popup and remove it from the DOM when closed
      this.isPopupVisible = false
      const popupDiv = document.getElementById('vr-modal')
      popupDiv.remove()
    },
    fetchPopupData() {
      const currentDomain = window.location.hostname
      const urlEndpoint = `https://${currentDomain}/wp-json/vr-modal/v1/modal-data`
      // console.log('Fetching data from:', urlEndpoint)

      fetch(urlEndpoint)
        .then((response) => response.json())
        .then((data) => {
          // Check if data is not empty and has at least one post
          if (data.length > 0) {
            const firstPost = data[0]

            // Update popupData with the data from the first post
            this.popupData.title = firstPost.vrm_title
            this.popupData.content = firstPost.vrm_content
            // If 'vrm_button_title' and 'link' are present in the post data, update them as well
            if ('vrm_button_title' in firstPost && 'vrm_button_url' in firstPost) {
              this.popupData.btnText = firstPost.vrm_button_title
              this.popupData.url = firstPost.vrm_button_url
            }
          }
        })
        .catch((error) => {
          console.error('Error fetching data from WordPress:', error)
        })
    },
    hasValidData(data) {
      return data.title || data.content || (data.btnText && data.url)
    }
  }
}
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
}
</style>
