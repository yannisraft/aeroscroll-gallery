<template>
    <div class="home">
        <div class="q-fit q-column q-no-wrap q-justify-start q-items-stretch q-content-start">
            <q-card flat bordered class="admin-card" style="position: relative;">
                <div class="q-row q-py-sm aeroscroll-page-header">
                    <span>{{ t("settings") }}</span>
                </div>
                <div class="row q-pa-md">
                    <div class="col-xs-12 col-sm-12 col-md text-left q-px-sm aeroscroll-edit-label-col" style="flex-basis: 30%; flex-grow: 0;">
                        <div class="text-field-header">{{ t("license_status") }}</div>
                        <div class="text-caption">{{ t("license_status_desc") }}</div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md" style="display: flex; align-items: center;">
                        <q-spinner v-if="loading_license" color="primary" size="2em" />
                        <div
                            v-if="!loading_license"
                            class="text-field-header aeroscroll-licensestatus-label"
                            :style="{ color: license_active ? (license_servererror ? '#de4a2c' : '#61b33e' ) : '#de4a2c' }"
                        >
                            {{ license_active ? (license_servererror ? t("licence_status_text_servererror") : t("licence_status_text_valid")) : ( license_expired ? t("licence_status_text_expired") : t("licence_status_text_invalid")) }}
                        </div>
                        <q-btn v-if="license_expired" style="margin-left: 20px;" color="light-green" @click="RenewLicense()" icon="key" :label="t('license_renew_license')" dense />
                    </div>
                </div>
                <div class="row q-pa-md">
                    <div class="col-xs-12 col-sm-12 col-md text-left q-px-sm aeroscroll-edit-label-col" style="flex-basis: 30%; flex-grow: 0;">
                        <div class="text-field-header">{{ t("select_product") }}</div>
                        <div class="text-caption">{{ t("select_product_desc") }}</div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md">
                        <q-select filled v-model="productselected" :options="productoptions" label="Product" />
                    </div>
                </div>
                <div class="row q-pa-md">
                    <div class="col-xs-12 col-sm-12 col-md text-left q-px-sm aeroscroll-edit-label-col" style="flex-basis: 30%; flex-grow: 0;">
                        <div class="text-field-header">{{ t("serial_key") }}</div>
                        <div class="text-caption">{{ t("serial_key_desc") }}</div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md">
                        <q-input
                            class="condensed-width"
                            outlined
                            v-model="serial_key"
                            dense
                            mask="aaaa-xxxx-xxxx-xxxx-xxxx"
                            hint="Serial: ssss-xxxx-xxxx-xxxx-xxxx"
                            :loading="activating_license"
                            @update:model-value="SerialInputChanged"
                        ></q-input>
                    </div>
                </div>
                <div v-if="notification_active" class="row q-pa-md">
                    <div class="col-xs-12 col-sm-12 col-md text-left q-px-sm aeroscroll-edit-label-col" style="flex-basis: 30%; flex-grow: 0;"></div>
                    <div class="col-xs-12 col-sm-12 col-md">
                        <span :style="{ color: notification_status === 0 ? '#e53935' : '#8bc34a' }" class="notification_label">{{
                            notification_text
                        }}</span>
                    </div>
                </div>
                <div class="row q-pa-md">
                    <div class="col-xs-12 col-sm-12 col-md text-left q-px-sm aeroscroll-edit-label-col" style="flex-basis: 30%; flex-grow: 0;"></div>
                    <div class="col-xs-12 col-sm-12 col-md">
                        <q-btn color="light-green" @click="ActivateLicense()" icon="key" :label="t('license_activate_license')" dense />
                        <q-btn
                            color="red-7"
                            @click="DeactivateLicense()"
                            icon="close"
                            :label="t('license_deactivate_license')"
                            dense
                            style="margin-left: 10px;"
                        />
                        <q-btn
                            color="primary"
                            @click="CheckLicense()"
                            icon="sync"
                            :label="t('license_check_license')"
                            dense
                            style="margin-left: 10px;"
                        />
                    </div>
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
    name: "SettingsView",
    setup(props) {
        const $q = useQuasar();
        const { t } = useI18n();

        let license_active = ref(false);
        let license_expired = ref(false);
        let license_servererror = ref(false);
        let serial_key = ref("");
        let serial_email = ref("");
        let serial_product_id = ref("");
        let loading_license = ref(true);
        let activating_license = ref(false);

        let notification_active = ref(false);
        let notification_text = ref("");
        let notification_status = ref(0);

        let productoptions = [
            {
                label: "Aeroscroll Gallery Pro – Basic",
                value: "806"
            },
            {
                label: "Aeroscroll Gallery Pro – Plus",
                value: "932"
            },
            {
                label: "Aeroscroll Gallery Pro – Expert",
                value: "933"
            }
        ];
        let productselected = ref({
            label: "Aeroscroll Gallery Pro – Basic",
            value: "806"
        });

        let { locale } = useI18n({ useScope: "global" });

        function GetLicense() {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/manageserial?request=get`;
            let _APEX = window["APEX"];
            console.log("APEX: ", _APEX);
            console.log("APEX: ", window["APEX"]);
            
            fetch(finalurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-WP-Nonce": _APEX.manageserial.nonce
                }
            })
                .then((response) => {
                    //console.log("GET LICENSE response: ", response.text());
                    return response.json();
                })
                .then((result) => {
                    loading_license.value = false;

                    if (result) {
                        license_active.value = result.active;

                        if(result.code === "key_expired") license_expired.value = true;
                        
                        if(result.storedserialkey.length > 0)
                        {
                            serial_key.value = result.storedserialkey;
                        }

                        if(result.code === "server_error") {
                            license_servererror.value = true;                            
                        }

                        var found = productoptions.find(item => item.value === result.productid);
                        if(found !== null && typeof found !== 'undefined')
                        {
                            productselected.value = found;
                        }                        
                    }
                });
        }

        function CheckLicense() {
            loading_license.value = true;
            notification_active.value = false;            

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }
            let _APEX = window["APEX"];

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/manageserial?request=check`;

            fetch(finalurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-WP-Nonce": _APEX.manageserial.nonce
                }
            })
                .then((response) => {
                    //console.log("GET LICENSE response: ", response.text());
                    return response.json();
                })
                .then((result) => {
                    loading_license.value = false;

                    if (result) {
                        license_active.value = result.active;
                        if(result.code === "server_error") {
                            license_servererror.value = true;                            
                        }

                        if(result.serialkey) {
                            serial_key.value = result.serialkey;
                        }

                        var found = productoptions.find(item => item.value === result.productid);
                        productselected.value = found;
                    }
                });
        }

        function ActivateLicense() {
            notification_active.value = false;

            if (serial_key.value != "") {
                activating_license.value = true;
                let _REST_URL = "http://localhost/";
                let _APEX = window["APEX"];

                if (window["REST_URL"]) {
                    _REST_URL = window["REST_URL"].url;
                }

                let finalurl =
                    `${_REST_URL}/wp-json/aeroscroll/v1/manageserial?request=activate&serial_key=` +
                    serial_key.value +
                    `&product_id=` +
                    productselected.value.value;

                fetch(finalurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX["manageserial"].nonce
                    },
                })
                    .then((response) => {
                        //console.log("GET LICENSE response: ", response.text());
                        return response.json();
                    })
                    .then((result) => {
                        activating_license.value = false;
                        if (result) {
                            if (result.active === true) {
                                notification_status.value = 1;
                                notification_text.value = t("serialkey_valid_activated");

                                var found = productoptions.find(item => item.value === result.productid);
                                productselected.value = found;
                            } else {
                                if (result.code === "instance_already_activated") {
                                    notification_status.value = 1;
                                    notification_text.value = t("serialkey_already_activated");
                                } else {
                                    notification_status.value = 0;
                                    notification_text.value = t("serialkey_invalid");
                                }
                            }
                            notification_active.value = true;
                            if(result.code !== "no_activations_left") license_active.value = result.active;
                        } else {
                        }
                    });
            } else {
                notification_status.value = 0;
                notification_active.value = true;
                notification_text.value = t("serial_field_must_notbe_empty");
            }
        }

        function DeactivateLicense() {
            notification_active.value = false;

            activating_license.value = true;
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }
            let _APEX = window["APEX"];

            let finalurl =
                `${_REST_URL}/wp-json/aeroscroll/v1/manageserial?request=deactivate`;

            fetch(finalurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    // eslint-disable-next-line
                    "X-WP-Nonce": _APEX["manageserial"].nonce
                }
            })
                .then((response) => {
                    //console.log("GET LICENSE response: ", response.text());
                    return response.json();
                })
                .then((result) => {
                    activating_license.value = false;

                    if (result) {
                        if(result.code === "key_deactivated") {
                            notification_status.value = 1;
                            notification_text.value = t("serialkey_deactivated");
                        }

                        notification_active.value = true;
                        license_active.value = result.active;
                    } else {
                    }
                });
        }

        function SerialInputChanged(value) {
            notification_active.value = false;
        }

        function RenewLicense() {
            //
            window.open('https://www.aeroscroll.com/#pricing', '_blank');
        }

        onBeforeMount(() => {
            var incomingLocale = window["TRANSLATIONS"].locale;
            if (incomingLocale) {
                locale.value = incomingLocale;
            }
        });

        onMounted(() => {
            loading_license.value = true;
            // Get Stored Serial
            GetLicense();
        });

        return {
            t,
            loading_license,
            activating_license,
            license_active,
            serial_key,
            serial_email,
            serial_product_id,
            notification_active,
            notification_text,
            notification_status,
            productoptions,
            productselected,
            license_expired,
            license_servererror,
            CheckLicense,
            ActivateLicense,
            DeactivateLicense,
            SerialInputChanged,
            RenewLicense
        };
    }
});
</script>

<style>
.aeroscroll-licensestatus-label {
    font-size: 12pt;
    padding-left: 10px;
}

.notification_label {
    font-size: 11pt;
    font-weight: bold;
}
</style>
