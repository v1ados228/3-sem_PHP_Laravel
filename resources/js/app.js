import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';

console.log('Initializing Vue app...');
const app = createApp(App);

// Проверяем наличие элемента #app
const appElement = document.getElementById('app');
if (appElement) {
    console.log('Mounting Vue app to #app element');
    app.mount('#app');
} else {
    console.error('Element #app not found!');
}
