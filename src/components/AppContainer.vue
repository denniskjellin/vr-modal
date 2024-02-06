<template>
  <div>
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
    // Fetch data from WordPress and update popupData
    console.log('Mounted hook called')
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
      console.log('Fetching data from WordPress')
      const currentDomain = window.location.hostname
      const urlEndpoint = `https://${currentDomain}/wp-json/vr-modal/v1/modal-data`

      fetch(urlEndpoint)
        .then((response) => response.json())
        .then((data) => {
          console.log('Data fetched:', data)
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
      console.log('Checking if data is valid:...')
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
