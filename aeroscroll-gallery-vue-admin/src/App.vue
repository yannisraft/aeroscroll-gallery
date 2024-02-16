<template>
    <div>
        <div class="q-row aeroscroll-head">
            <img :src="GetHeadImage()" height="40" alt="Aeroscroll"/>
        </div>
        <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet" type="text/css" />
        <div class="q-pa-md">
            <HomeView v-if="page === 'aeroscroll-gallery-home'" />
            <ImageGalleriesView v-if="page === 'aeroscroll-gallery-imagegalleries'" />
            <HelpView v-if="page === 'aeroscroll-gallery-help'" />
            <SettingsView v-if="page === 'aeroscroll-gallery-settings'" />
        </div>
    </div>
</template>

<script>
import HomeView from "./views/HomeView.vue";
import ImageGalleriesView from "./views/ImageGalleriesView.vue";
import HelpView from "./views/HelpView.vue";
import SettingsView from "./views/SettingsView.vue";

import { ref, defineComponent, onActivated, onMounted } from "vue";

export default {
    name: "LayoutDefault",
    components: {
        HomeView,
        ImageGalleriesView,
        HelpView,
        SettingsView
    },
    props: {
        page: {
            type: String,
            default: "aeroscroll-gallery-home"
        }
    },
    setup(props) {
        //let pageID = ref("");

        function GetHeadImage() {
            return window["MEDIA_URL"] + 'images/logo_head.png';
        }

        onMounted(() => {
            //pageID.value = props.page;
        });

        return {
            GetHeadImage
        };
    }
};
</script>

<style>
.condensed-width {
    max-width: 500px;
}

.condensed-width-200 {
    max-width: 200px;
}
</style>
