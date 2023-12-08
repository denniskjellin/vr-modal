<template>
  <div>
    <AppContainer />
  </div>
</template>

<script>
import AppContainer from './components/AppContainer.vue'

export default {
  components: {
    AppContainer
  },
  mounted() {
    console.log('app mountar')
  }
}
</script>
