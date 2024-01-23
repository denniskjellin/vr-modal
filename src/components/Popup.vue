<template>
  <div class="popup generic-popup" role="dialog" aria-modal="true">
    <div class="popup-overlay" @click="closePopup" aria-hidden="true"></div>
    <div class="popup-content">
      <button @click="closePopup" type="button" class="btn-close" aria-label="Close">
        <img src="/src/assets/images/close.svg" alt="Close" />
      </button>
      <div v-if="jsonData">
        <h2 ref="popup-title" tabindex="0" class="popup-title">{{ jsonData.title }}</h2>
        <p class="popup-description">{{ jsonData.content }}</p>
        <a :href="jsonData.url" class="navigation-link" :aria-label="jsonData.btnText">
          {{ jsonData.btnText }}
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
props: {
	jsonData: {
	type: Object,
	required: true
	}
},
mounted() {
console.log('Popup mounted with data:', this.jsonData);
window.addEventListener('keydown', this.handleKeyboardNavigation);
this.$refs['popup-title'].focus();
this.$refs['popup-title'].tabIndex = -1;
},

beforeUnmount() {
	window.removeEventListener('keydown', this.handleKeyboardNavigation)
},
methods: {
	closePopup() {
	this.$emit('close')
	},
	handleKeyboardNavigation(e) {
	// Tracking 'tab' and 'shift + tab' to keep focus within the tour,
	// since it otherwise tabs to content behind the popup
	if (e.key === 'Tab') {
		const focusable = document.querySelector('.generic-popup').querySelectorAll('button,a')
		if (focusable.length) {
		const first = focusable[0]
		const last = focusable[focusable.length - 1]
		const shift = e.shiftKey
		if (shift) {
			if (e.target === first) {
			last.focus()
			e.preventDefault()
			}
		} else if (e.target === last) {
			first.focus()
			e.preventDefault()
		}
		}
	}
	}
}
}
</script>

<style scoped lang="scss">
.popup {
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);
  cursor: pointer;
}

.popup-content {
  background-color: #ffffff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  max-width: 40rem;
  position: relative;
  border-radius: 0.5rem;
  padding: 1.5rem;
  word-break: keep-all;
  margin: 0 1.5rem;
}

.popup-description {
  max-width: 60ch;
}

.btn-close {
  background: transparent;
  border: none;
  position: absolute;
  top: 1rem;
  right: 1rem;
  font-size: 1rem;
  cursor: pointer;
  margin: 0;
  padding: 0;
}

.btn-close img {
  width: 2rem;
  height: 2rem;
}

.popup-title {
  color: #333;
  margin-bottom: 1rem;
  outline: none;
}

.popup-description {
  color: #555;
  font-size: 1rem;
  line-height: 1.4;
  margin-top: 0;
  margin-bottom: 1rem; 
}


.navigation-link {
  display: inline-block;
  margin-top: 1rem;
  text-decoration: underline;
  font-weight: bold;
  color: #555;
  transition: text-decoration 0.3s ease-in-out;
}

.navigation-link:hover {
  text-decoration: none;
}
</style>

