const {
    createApp
} = Vue;

//import GridComponent from "public/js/aeroscroll-gallery-vue/dist/aeroscroll-gallery-vue.umd.js"



// Here we will load the App.vue
// -----------------------------
function TestLoadVue(shortcode) {
    setTimeout(() => {
        console.log('TestLoadVue');
        /* createApp({
            template : '<div><h1>Loaded Vue for: '+shortcode+'</h1><VScroller>??</VScroller></div>',
            data() {
                return {
                    count: 0
                }
            },
            components: {
                //aeroscrollgridvue    
                //'VScroller': Vue.defineAsyncComponent( () => import('./aeroscrollgridvue-browser.min.js'))      
            }
        }).mount('#app_aeroscroll_'+shortcode); */

        createApp(AeroscrollGrid).mount('#app_aeroscroll_' + shortcode);

    }, 200);

    //console.log("TestLoadVue: ", shortcode);
}