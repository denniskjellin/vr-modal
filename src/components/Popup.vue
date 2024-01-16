<template>
<div class="popup generic-popup" role="dialog" aria-modal="true">
	<div class="popup-overlay" @click="closePopup" aria-hidden="true"></div>
	<div class="popup-content">
	<button @click="closePopup" type="button" class="btn-close" aria-label="Close">
		&times;
	</button>
	<div v-if="jsonData">
		<h2 ref="popup-title" tabindex="0" class="popup-title">{{ jsonData.rubrik }}</h2>
		<p class="popup-description">{{ jsonData.innehåll }}</p>
		<a :href="jsonData.url" class="navigation-link" :aria-label="jsonData.knapptext">
		{{ jsonData.knapptext }}
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

<style scoped>
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
	opacity: 0.5;
}
.popup-content {
	background-color: #ffffff;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
	max-width: 400px;
	width: 80%;
	position: relative;
	border-radius: 8px;
	padding: 2rem;
}
.btn-close {
	color: #333;
	position: absolute;
	top: 10px;
	right: 10px;
	font-size: 16px;
	cursor: pointer;
}
.btn-close:hover,
.btn-close:focus {
	color: #555;
}
.popup-title {
	color: #333;
	font-size: 1.5rem;
	margin-bottom: 10px;
	outline: none;
}
.popup-description {
	color: #555;
	font-size: 1rem;
	line-height: 1.4;
	margin-top: 0;
}
.navigation-link {
	display: inline-block;
	padding: 8px 16px;
	margin-top: 10px;
	text-decoration: none;
	background-color: #0276a8;
	color: #fff;
	border-radius: 4px;
	transition: background-color 0.3s;
}
.navigation-link:hover {
	background-color: #2980b9;
}
.generic-popup-h2:focus {
	outline: none;
}
</style>
