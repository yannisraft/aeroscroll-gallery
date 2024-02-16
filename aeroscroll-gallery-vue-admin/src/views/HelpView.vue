<template>
    <div class="home">
        <div class="q-fit q-column q-no-wrap q-justify-start q-items-stretch q-content-start">
            <q-card flat bordered class="admin-card" style="position: relative;">
                <div class="q-row q-py-sm aeroscroll-page-header">
                    <span>{{ t("help_n_support") }}</span>
                </div>
                <!------------------>
                <div class="q-row q-px-lg q-pt-lg">
                    <h6 class="aeroscroll-subheader" style="margin: 0px;">FAQ {{ t('faq_desc') }}</h6>
                </div>
                <q-list style="padding: 20px;">
                    <div v-for="(faqitem, index) in faqitems" :key="faqitem.id">
                        <q-expansion-item group="somegroup" icon="help" :label="t(faqitem.question_t)" header-class="text-primary">
                            <q-card>
                                <q-card-section>
                                    <div v-html="t(faqitem.answer_t)"></div>
                                </q-card-section>
                            </q-card>                        
                        </q-expansion-item>
                        <q-separator v-if="index < faqitems.length-1"/>
                    </div>
                    
                    

                    <!-- <q-expansion-item group="somegroup" icon="perm_identity" label="Second" header-class="text-teal">
                        <q-card>
                            <q-card-section>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, eius reprehenderit eos corrupti commodi magni
                                quaerat ex numquam, dolorum officiis modi facere maiores architecto suscipit iste eveniet doloribus ullam aliquid.
                            </q-card-section>
                        </q-card>
                    </q-expansion-item>

                    <q-separator />

                    <q-expansion-item group="somegroup" icon="shopping_cart" label="Third" header-class="text-purple">
                        <q-card>
                            <q-card-section>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, eius reprehenderit eos corrupti commodi magni
                                quaerat ex numquam, dolorum officiis modi facere maiores architecto suscipit iste eveniet doloribus ullam aliquid.
                            </q-card-section>
                        </q-card>
                    </q-expansion-item>

                    <q-separator />

                    <q-expansion-item
                        group="somegroup"
                        icon="bluetooth"
                        label="Fourth"
                        header-class="bg-teal text-white"
                        expand-icon-class="text-white"
                    >
                        <q-card class="bg-teal-2">
                            <q-card-section>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem, eius reprehenderit eos corrupti commodi magni
                                quaerat ex numquam, dolorum officiis modi facere maiores architecto suscipit iste eveniet doloribus ullam aliquid.
                            </q-card-section>
                        </q-card>
                    </q-expansion-item> -->
                </q-list>
                <!------------------>
                <div class="q-row q-px-lg q-pt-lg">
                    <h6 class="aeroscroll-subheader" style="margin: 0px;">{{ t("documentation") }}</h6>
                </div>
                <div class="q-row q-px-lg q-pt-md">
                    <p>{{ t("for_documentation_desc") }}</p>
                </div>
                <div class="q-row q-px-lg q-pb-lg">
                    <a href="https://aeroscroll.com/gallery/documentation">Aeroscroll Gallery Documentation</a>
                </div>
                <!------------------>
                <div style="padding: 20px;">
                    <div class="seperate-line"></div>
                </div>
                <!------------------>
                <div class="q-row q-px-lg">
                    <h6 class="aeroscroll-subheader" style="margin: 0px;">{{ t("support") }}</h6>
                </div>
                <div class="q-row q-px-lg q-pt-md q-pb-lg">
                    <p>{{ t("for_support_desc") }} <a href="mailto:info@aeroscroll.com">support@aeroscroll.com</a></p>
                </div>
            </q-card>
        </div>
    </div>
</template>

<script>
import { ref, defineComponent, onActivated, onMounted, onBeforeMount, computed } from "vue";

import { useQuasar } from "quasar";
import { useI18n } from "vue-i18n";

export default defineComponent({
    name: "HelpView",
    setup(props) {
        const $q = useQuasar();
        const { t } = useI18n();

        let { locale } = useI18n({ useScope: "global" });

        let faqitems = ref([
            {
                id: 1,
                question_t: "faq_what_is",
                answer_t: "faq_what_is_answer",
            },
            {
                id: 2,
                question_t: "faq_how_to_add_gallery",
                answer_t: "faq_how_to_add_gallery_answer",
            },
            {
                id: 3,
                question_t: "faq_how_toadd_in_frontend",
                answer_t: "faq_how_toadd_in_frontend_answer",
            },
            {
                id: 4,
                question_t: "faq_what_layouts",
                answer_t: "faq_what_layouts_answer",
            },
            {
                id: 5,
                question_t: "faq_can_i_add_my_images",
                answer_t: "faq_can_i_add_my_images_answer",
            },
            {
                id: 6,
                question_t: "faq_can_i_use_posts",
                answer_t: "faq_can_i_use_posts_answer",
            },
            {
                id: 7,
                question_t: "faq_how_to_make_horizontal",
                answer_t: "faq_how_to_make_horizontal_answer",
            },
            {
                id: 8,
                question_t: "faq_how_to_change_theme",
                answer_t: "faq_how_to_change_theme_answer",
            }
        ]);

        onBeforeMount(() => {
            var incomingLocale = window["TRANSLATIONS"].locale;
            if (incomingLocale) {
                locale.value = incomingLocale;
            }
        });

        return {
            t,
            faqitems
        };
    }
});
</script>
