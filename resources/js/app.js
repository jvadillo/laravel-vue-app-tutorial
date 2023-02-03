import './bootstrap';
import * as Popper from '@popperjs/core';
window.Popper = Popper;
import 'bootstrap';

import { createApp } from 'vue';
// import the root component App from a single-file component.
import App from './components/Front.vue';

const app = createApp(App);
app.mount('#app');
