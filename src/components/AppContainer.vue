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
        title: 'Dynamisk rubrik..',
        description:
          'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies  aliquam, nunc sapien ultricies nunc, quis ultricies nisl nunc eget nunc.',
        linkTitle: 'Gå till nyhetsbrevet',
        link: 'https://www.google.se'
      }
    }
  },
  mounted() {
    setTimeout(() => {
      this.isPopupVisible = true
    }, 1000)
  },
  methods: {
    openPopup() {
      this.isPopupVisible = true
    },
    closePopup() {
      this.isPopupVisible = false
    }
  }
}
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
