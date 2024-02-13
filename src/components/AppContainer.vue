<template>
  <div v-if="showPopup">
    <Popup v-if="isPopupVisible" :jsonData="popupData" @close="closePopup" />
    <div v-if="isPopupVisible" v-html="renderedContent"></div>
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
  computed: {
    renderedContent() {
      // Replace &nbsp; with regular spaces and convert HTML entities
      const decodedContent = this.popupData.content.replace(/&nbsp;/g, ' ')

      // Return the decoded content
      return decodedContent
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
    console.log('Starting mounted function')

    // Check if the URL contains a specific query parameter
    let url = window.location.search
    console.log('URL:', url)

    if (
      url.includes('?utm_medium=') ||
      url.includes('&utm_medium=') ||
      url.includes('?utm_source=') ||
      url.includes('&utm_source=')
    ) {
      console.log('Query parameters found. Hiding modal.')
      // Set cookie to indicate that the modal has been shown
      document.cookie = 'vr_modal_cookie=vr_modal_shown; expires=' + 0 + '; path=/'

      // Optionally, you can also immediately hide the modal if it's already visible
      this.showPopup = false
      const overlayElement = document.getElementById('vr-modal')
      // if (overlayElement) {
      //   overlayElement.style.display = 'none'
      // }

      console.log('Modal hidden. Exiting function.')
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
      console.log('Modal should be shown. Setting showPopup to true.')
      this.showPopup = true
      // Set cookie to prevent modal from showing again
      document.cookie = 'vr_modal_cookie=vr_modal_shown; expires=' + 0 + '; path=/'
    } else {
      console.log('Modal should be hidden. Hiding modal.')
      return
      const overlayElement = document.getElementById('vr-modal')
      // if (overlayElement) {
      //   overlayElement.style.display = 'none'
      // }
    }

    console.log('Fetching data from WordPress and updating popupData.')
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

      fetch(urlEndpoint)
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
          }
          return response.json()
        })
        .then((data) => {
          // Check if data is not empty and has at least one post
          if (data.length > 0) {
            const firstPost = data[0]

            // Update popupData with the data from the first post
            this.popupData.title = firstPost.vrm_title || ''
            this.popupData.content = firstPost.vrm_content || ''
            // If 'vrm_button_title' and 'link' are present in the post data, update them as well
            if ('vrm_button_title' in firstPost && 'vrm_button_url' in firstPost) {
              this.popupData.btnText = firstPost.vrm_button_title
              this.popupData.url = firstPost.vrm_button_url
            }
          }
        })
        .catch((error) => {
          console.error('Error fetching data from WordPress:', error.message)
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
