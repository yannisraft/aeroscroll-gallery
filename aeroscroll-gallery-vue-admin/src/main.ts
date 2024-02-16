import { createApp } from 'vue'
import App from './App.vue'
//import router from './router'
import store from './store'

//import Vue3EasyDataTable from 'vue3-easy-data-table';
//import 'vue3-easy-data-table/dist/style.css';
import { Quasar } from 'quasar'
import quasarUserOptions from './quasar-user-options'
import i18n from './i18n.js'

const _win = (window as any);
const _page = _win.aeroscroll_page;
//console.log("TEST: ", win.aeroscroll_page);

const app = createApp(App, { page: _page}).use(i18n).use(Quasar, quasarUserOptions);
//app.component('EasyDataTable', Vue3EasyDataTable);
//app.config.globalProperties.$APEX = APEX;
//app.use(store).use(router).mount('#wp-vue-app-admin');

// CREATE SHADOW
let wpMount = document.querySelector("#wp-vue-app-admin");
let holder = document.createElement("div");

if(wpMount)
{
    let shadow = wpMount.attachShadow({ mode: "open" });

    // ATTACH SHADOW CSS
    let cssholder = document.createElement("link");
    cssholder.type = 'text/css';
    cssholder.rel = 'stylesheet';
    cssholder.href = window['MEDIA_URL'] + 'dist/css/app.css';

    let extracssholder = document.createElement("link");
    extracssholder.type = 'text/css';
    extracssholder.rel = 'stylesheet';
    extracssholder.href = window['MEDIA_URL'] + 'css/aeroscroll-gallery-admin.css';

    /* let styleholder = document.createElement("style");
    styleholder.innerHTML = ":host { all: initial }";
    shadow.appendChild(styleholder); */

    shadow.appendChild(cssholder);
    shadow.appendChild(extracssholder);
    shadow.appendChild(holder);

    app.use(store).mount(holder);
}

