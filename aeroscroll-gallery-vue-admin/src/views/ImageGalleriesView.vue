<template>
    <div class="home">
        <div class="q-fit q-column q-no-wrap q-justify-start q-items-stretch q-content-start">
            <q-card flat bordered class="admin-card" style="position: relative;">
                <div class="q-row q-py-sm aeroscroll-page-header">
                    <span>{{ t("manage_imagegalleries") }}</span>
                </div>
                <div v-if="!editMode" class="q-row q-pb-sm">
                    <div v-if="loading" class="panelloader">
                        <q-spinner color="white" size="3em" />
                        <span style="color: white; margin-top: 10px;">{{ t("loading") + "..." }}</span>
                    </div>
                    <div v-if="!licenseActive" class="license_inactive_panel">
                        <span v-if="licenseNotActive">{{ t("please_activate_license") }}</span>
                    </div>
                    <q-table
                        style="width: 100%;"
                        :loading="loading"
                        :rows="tablerows"
                        :columns="tablecolumns"
                        :filter="searchValue"
                        :filter-method="SearchMethod"
                        row-key="id"
                        :pagination="initialPagination"
                        @update:pagination="PaginationUpdated"
                    >
                        <template v-slot:loading>
                            <!-- <q-inner-loading showing color="primary" /> -->
                        </template>
                        <template v-slot:top-right>
                            <div class="q-col q-px-sm" style="flex: 0;">
                                <q-btn color="primary" :label="t('create')" @click="CreateGalleryClicked"></q-btn>
                            </div>
                            <div class="q-col q-px-sm" style="flex: 0;">
                                <q-btn color="primary" icon="refresh" @click="RefreshTable"></q-btn>
                            </div>
                            <div class="q-col q-pl-sm">
                                <q-input outlined dense debounce="300" class="q-py-sm" v-model="searchValue" :placeholder="t('search')">
                                    <template v-slot:append>
                                        <q-icon name="search"></q-icon>
                                    </template>
                                </q-input>
                            </div>
                        </template>
                        <template v-slot:body-cell-published="props">
                            <q-td :props="props">
                                <div>
                                    <q-toggle v-model="props.row.published" @click="TogglePublished(props.row)" />
                                </div>
                            </q-td>
                        </template>
                        <template v-slot:body-cell-orientation="props">
                            <q-td :props="props" class="text-capitalize">
                                {{ props.row.orientation }}
                            </q-td>
                        </template>
                        <template v-slot:body-cell-layout="props">
                            <q-td :props="props" class="text-capitalize">
                                {{ props.row.layout }}
                            </q-td>
                        </template>
                        <template v-slot:body-cell-theme="props">
                            <q-td :props="props" class="text-capitalize">
                                {{ props.row.theme }}
                            </q-td>
                        </template>
                        <template v-slot:body-cell-operation="props">
                            <q-td :props="props">
                                <div>
                                    <q-btn color="primary" icon="edit" @click="EditGalleryClicked(props.row)" dense></q-btn>
                                    <q-btn
                                        color="primary"
                                        icon="delete"
                                        @click.stop="DeleteGalleryClicked(props.row)"
                                        dense
                                        style="margin-left: 10px;"
                                    ></q-btn>
                                </div>
                            </q-td>
                        </template>
                    </q-table>
                </div>

                <div v-if="editMode" class="q-row q-column q-no-wrap q-justify-start q-items-stretch q-content-start">
                    <div class="q-row q-py-md q-px-md">
                        <h6 class="aeroscroll-subheader" style="margin: 0px;">{{ t("editimagegallery") }}</h6>
                    </div>
                    <div class="q-row q-py-md q-pl-lg">
                        <!-- <div class="text-h6">{{ t("settings") }}</div>
                        <p>{{ t("settings_desc") }}</p> -->
                        <div class="q-row">
                            <div class="q-col">
                                <div class="q-row q-py-sm">
                                    <div class="q-col-3 text-left q-px-sm">
                                        <div class="text-field-header">ID</div>
                                        <div class="text-caption">{{ t("id_desc_collections") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-input class="condensed-width" outlined v-model="editingItem.id" dense readonly></q-input>
                                    </div>
                                </div>
                            </div>
                            <div class="q-col">
                                <div class="q-row q-py-sm">
                                    <div class="q-col-3 text-left q-px-sm">
                                        <div class="text-field-header">{{ t("imagegal_title") }}</div>
                                        <div class="text-caption">{{ t("imagegal_title_desc") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-input class="condensed-width" outlined v-model="editingItem.title" dense></q-input>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="q-row q-py-sm">
                            <div class="q-col-2 text-left q-px-sm">
                                <div class="text-field-header">{{ t("description") }}</div>
                            </div>
                            <div class="q-col">
                                <q-input class="condensed-width" outlined v-model="editingItem.description"
                                    type="textarea" dense></q-input>
                            </div>
                        </div> -->
                        <div class="q-row q-py-sm q-items-center">
                            <div class="q-col-2 text-left q-px-sm">
                                <div class="text-field-header">{{ t("published") }}</div>
                            </div>
                            <div class="q-col">
                                <q-toggle v-model="editingItem.published"></q-toggle>
                            </div>
                        </div>

                        <!-- Notification -->
                        <div v-if="notification_success" class="q-row q-py-sm">
                            <q-banner dense inline-actions class="text-white bg-green">
                                {{ t("changessavedsuccessfully") }}
                                <template v-slot:action>
                                    <q-btn flat color="white" label="Dismiss" />
                                </template>
                            </q-banner>
                        </div>

                        <!-- Notify disabled Images -->
                        <div v-if="createMode" class="q-row q-py-sm">
                            <span style="color: #f85c2f;">Please Save first in order to add Images</span>
                        </div>
                    </div>

                    <q-separator class="q-mb-sm" />

                    <!-- Images Grid -->
                    <div>
                        <div class="q-row q-px-lg">
                            <div class="text-h6">{{ t("selectedimages") }}</div>
                        </div>
                        <div class="q-row q-px-lg">
                            <p>{{ t("selectedimages_desc") }}</p>
                        </div>
                        <div class="q-row q-pt-lg q-pl-lg q-px-lg">
                            <div class="q-col text-right">
                                <q-btn
                                    color="primary"
                                    style="margin-left: 10px; margin-bottom: 10px;"
                                    :label="t('exportgallery')"
                                    icon="folder_zip"
                                    @click="ExportGallery"
                                    :disable="!ispro"
                                    type="a"
                                >
                                    <q-tooltip v-if="!ispro" class="protooltip bg-blue-1" :offset="[10, 10]">
                                        <q-icon size="sm" name="workspace_premium" class="q-mr-sm protooltip_icon" /><span class="protooltip_label">{{
                                            t("profeature")
                                        }}</span>
                                    </q-tooltip>
                                </q-btn>
                                <q-btn
                                    color="primary"
                                    style="margin-left: 10px; margin-bottom: 10px;"
                                    :label="t('importgallery')"
                                    icon="folder_zip"
                                    :disable="!ispro"
                                    @click="ImportGallery"
                                >
                                    <q-uploader
                                        ref="fileImporterElement"
                                        label="Auto Uploader"
                                        :url="GetUploaderURL()"
                                        :headers="uploaderHeaders"
                                        accept=".zip"
                                        style="display: none;"
                                        batch
                                        @uploaded="onUploadedImportGallery"
                                        @added="onUploaderFilesAdded"
                                        @failed="onUploaderFilesFailed"
                                    />
                                    <q-tooltip v-if="!ispro" class="protooltip bg-blue-1" :offset="[10, 10]">
                                        <q-icon size="sm" name="workspace_premium" class="q-mr-sm protooltip_icon" /><span class="protooltip_label">{{
                                            t("profeature")
                                        }}</span>
                                    </q-tooltip>
                                </q-btn>
                                <q-btn
                                    color="primary"
                                    style="margin-left: 10px; margin-bottom: 10px;"
                                    :label="t('deleteselected')"
                                    icon="delete"
                                    @click="OnDeleteMultipleGalleryImagesClicked"
                                    :disable="imagegallery_rows_selected_arr.length === 0"
                                ></q-btn>
                                <!-- <q-btn color="primary" style="margin-left: 10px; margin-bottom: 10px;" :disable="createMode"
                                    :label="t('addimages')" icon="folder_zip" @click="OnAddImagesClicked"></q-btn>
                                <q-btn color="primary" style="margin-left: 10px; margin-bottom: 10px;" :disable="createMode"
                                    label="Add Images using WP Media Library" icon="folder_zip" @click="OnAddImagesClicked"></q-btn> -->
                            </div>
                        </div>

                        <div class="q-row q-pb-lg q-pl-lg q-px-lg">
                            <div class="q-col text-right">
                                <q-btn
                                    color="primary"
                                    style="margin-left: 10px; margin-bottom: 10px;"
                                    :disable="createMode"
                                    :label="t('addimages')"
                                    icon="folder_zip"
                                    @click="OnAddImagesClicked"
                                ></q-btn>
                                <q-btn
                                    color="primary"
                                    style="margin-left: 10px; margin-bottom: 10px;"
                                    :disable="createMode"
                                    label="Add Images using WP Media Library"
                                    icon="folder_zip"
                                    @click="openMediaLibrary"
                                ></q-btn>
                            </div>
                        </div>

                        <div class="aeroscroll-table-imagegalleries">
                            <div v-if="imagegallery_rows.length === 0" class="aeroscroll-imagegallery-list-empty">
                                <q-card class="aeroscroll-imagegallery-list-empty-card">
                                    <img style="width: 65px; margin-right: 20px;" :src="empty_image" /><span>{{
                                        t("noimageingalleryclicktoadd")
                                    }}</span>
                                </q-card>
                            </div>
                            <div v-if="imagegallery_loading" class="aeroscroll-imagegallery-list-empty">
                                <q-inner-loading showing color="primary" />
                            </div>
                            <!-- LINK Draggable-->
                            <q-table
                                v-if="imagegallery_rows.length > 0"
                                class="gal-sticky-header-table"
                                style="height: 500px;"
                                flat
                                bordered
                                :rows="imagegallery_rows"
                                :columns="imagegallery_columns"
                                row-key="id"
                                selection="multiple"
                                virtual-scroll
                                :rows-per-page-options="[0]"
                                v-model:selected="imagegallery_rows_selected_arr"
                            >
                                <template v-slot:body-cell-edit="props">
                                    <q-td :props="props">
                                        <q-btn icon="edit" @click="EditGalleryImageClicked(props.row)" flat dense>
                                            <q-tooltip>{{ t("editimage") }}</q-tooltip>
                                        </q-btn>
                                    </q-td>
                                </template>
                                <template v-slot:body-cell-image="props">
                                    <q-td :props="props">
                                        <q-img
                                            class="card-image"
                                            :src="props.row.image"
                                            loading="lazy"
                                            spinner-color="primary"
                                            height="100px"
                                            style="max-width: 100px;"
                                        >
                                            <template v-slot:error>
                                                <div class="flex flex-center bg-red-4 text-white" style="width: 100%; height: 100%;">
                                                    <span style="white-space: break-spaces; text-align: center;">{{ t("error_loading_image") }}</span>
                                                </div>
                                            </template>
                                        </q-img>
                                    </q-td>
                                </template>
                                <template v-slot:body-cell-description="props">
                                    <q-td style="text-wrap: wrap;" :props="props">
                                        {{ props.row.description }}
                                    </q-td>
                                </template>
                                <template v-slot:bottom="scope">
                                    <div class="qtb_bottom_left">
                                        <span v-if="imagegallery_rows_selected_arr.length > 0"
                                            >{{ t("selected") }}: {{ imagegallery_rows_selected_arr.length }}</span
                                        >
                                    </div>
                                    <div class="qtb_bottom">
                                        <div>{{ t("totalimages") }} : {{ imagegallery_rows.length }}</div>
                                    </div>
                                </template>
                            </q-table>
                        </div>
                    </div>
                    <div class="aeroscroll-flex q-pa-md">
                        <div class="aeroscroll-flex-child"></div>
                        <div class="fixflex text-right">
                            <q-btn color="primary" :label="t('close')" @click="CancelEditingClicked" dense></q-btn>
                        </div>
                        <div class="fixflex text-right" style="margin-left: 10px;">
                            <q-btn color="green" :label="t('savechanges')" @click="SaveEditingClicked" dense></q-btn>
                        </div>
                    </div>
                </div>
            </q-card>
        </div>
        <FileManager v-model="filemanager_visible" :ISNOTPRO="!ispro" @onImagesSelected="OnImagesSelected" @onCloseManager="onCloseManager" />

        <!-- Popp dialog for edit image -->
        <transition name="fade">
            <div v-if="editimagedialog" class="q-custom-dialog">
                <div class="q-custom-dialog-bg"></div>
                <q-card class="my-card" style="min-width: 500px;">
                    <q-img class="popupeditimage_img" loading="lazy" spinner-color="primary" :src="editimagedialog_data.image"> </q-img>
                    <q-card-section>
                        <div class="q-row q-no-wrap items-center">
                            <div class="q-col text-subtitle1 ellipsis">
                                {{ t("imagetitle") }}
                            </div>
                        </div>
                        <q-input class="condensed-width" outlined v-model="editimagedialog_data.title" type="label" dense></q-input>
                    </q-card-section>

                    <q-card-section class="q-pt-none">
                        <div class="q-row q-no-wrap items-center">
                            <div class="q-col text-subtitle1 ellipsis">
                                {{ t("imagedesc") }}
                            </div>
                        </div>
                        <q-input class="condensed-width" outlined v-model="editimagedialog_data.description" type="textarea" dense></q-input>
                    </q-card-section>

                    <q-separator />

                    <q-card-actions align="right" class="q-col items-center justify-end">
                        <q-btn @click="editimagedialog = false" color="primary" :label="t('close')"></q-btn>
                        <q-btn v-close-popup color="primary" :label="t('save')" @click="SaveImageDetails" />
                    </q-card-actions>
                </q-card>
            </div>
        </transition>
    </div>
</template>

<script>
import { ref, computed, defineComponent, onActivated, onMounted, watchEffect, reactive, onBeforeMount } from "vue";

import { Container, Draggable } from "vue3-smooth-dnd";
import { applyDrag, generateItems } from "./../utils/ddhelpers";

import { VueDraggableNext } from "vue-draggable-next";
import draggable from "vuedraggable";

import { useQuasar } from "quasar";

import FileManager from "./../components/FileManager.vue";

import helpers from "./../Helpers/helpers";
import { useI18n } from "vue-i18n";

var IS_PRO = window["IS_PRO"].is_pro.toLowerCase() === "true" ? true : false;

export default defineComponent({
    name: "ImageGalleriesView",
    components: {
        FileManager,
        Container,
        Draggable
    },
    setup(props) {
        const { t } = useI18n();
        let { locale } = useI18n({ useScope: "global" });

        const $q = useQuasar();

        const CalculateSize = helpers.CalculateSize;
        const GetDateFromTimestamp = helpers.GetDateFromTimestamp;
        const RemoveItemFromArraywithID = helpers.RemoveItemFromArraywithID;

        let searchField = ref("vertical");
        let searchValue = ref("");
        let searchValue_images = ref("");
        let editMode = ref(false);
        let notification_success = ref(false);
        let justLoaded = true;

        let editingItem = ref();
        let createMode = ref(false);

        let categories_options = ref([]);

        let tablerows = ref([]);
        let tab = ref("settings");
        let igtableref = ref(null);

        let loading = ref(false);
        let drag = ref(false);
        let filemanager_visible = ref(false);
        let ispro = ref(false);
        ispro.value = IS_PRO;

        let editimagedialog = ref(false);
        let editimagedialog_data = ref({
            id: "",
            image: "",
            title: "",
            desc: "",
            order: 0
        });

        const filename_str = t("filename");
        const title_str = t("title");
        const description_str = t("description");

        let allimagesselected = ref(false);

        let empty_image = ref(window["MEDIA_URL"] + "images/emptylist.png");

        let imagegallery_rows = ref([]);
        let imagegallery_rows_selected = ref({});
        let imagegallery_rows_selected_arr = ref([]);
        let imagegallery_loading = ref(false);

        let initialPagination = ref({
            rowsPerPage: 25
        });

        let licenseActive = ref(false);
        let licenseNotActive = ref(false);
        let importFile = ref(null);
        let fileImporterElement = ref(null);
        let uploadProgress = ref({
            percent: 0,
            color: "warning",
            status: "idle"
        });
        let uploaderHeaders = ref([]);

        // Reactive state to hold selected images and image IDs
        const selectedImages = ref([]);
        const imageIds = ref([]);
        let frame = null;

        const tablecolumns = computed(() => {
            return [
                {
                    name: "published",
                    field: "published",
                    required: true,
                    label: t("published"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "operation",
                    field: "operation",
                    required: true,
                    label: t("operation"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "id",
                    field: "id",
                    required: true,
                    label: "ID",
                    align: "left",
                    sortable: true
                },
                {
                    name: "title",
                    field: "title",
                    required: true,
                    label: t("title"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "description",
                    field: "description",
                    required: false,
                    label: t("description"),
                    align: "left",
                    sortable: false
                },
                {
                    name: "created_at",
                    field: "created_at",
                    required: false,
                    label: t("creation_date"),
                    align: "left",
                    sortable: true
                }
            ];
        });

        const imagegallery_columns = [
            {
                name: "id",
                field: "id",
                required: true,
                label: "ID",
                align: "left",
                sortable: true,
                style: "width: 40px"
            },
            {
                name: "edit",
                field: "edit",
                label: t("edit"),
                align: "left",
                sortable: false,
                style: "width: 40px"
            },
            {
                name: "name",
                field: "name",
                required: true,
                label: t("filename"),
                align: "left",
                sortable: true,
                style: "width: 150px"
            },
            {
                name: "title",
                field: "title",
                required: true,
                label: t("title"),
                align: "left",
                sortable: true
            },
            {
                name: "description",
                field: "description",
                required: true,
                label: t("description"),
                align: "left",
                sortable: true
            },
            {
                name: "image",
                field: "image",
                required: true,
                label: "Image",
                align: "left",
                sortable: false,
                style: "width: 150px"
            }
        ];

        const DeleteGalleryClicked = (val) => {
            $q.dialog({
                title: "Confirm",
                message: "Are you sure you want to delete the specific Image Gallery?",
                class: "qconfirmdialog",
                cancel: {
                    push: true
                },
                ok: {
                    label: "Delete",
                    push: true,
                    color: "negative"
                },
                persistent: true
            })
                .onOk(() => {
                    $q.loading.show();

                    let _REST_URL = "http://localhost/";
                    if (window["REST_URL"]) {
                        _REST_URL = window["REST_URL"].url;
                    }

                    let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/deleteimagegallery`;

                    let _APEX = window["APEX"];
                    if (_APEX) {
                        fetch(finalurl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                // eslint-disable-next-line
                                "X-WP-Nonce": _APEX.deleteimagegallery.nonce
                            },
                            body: JSON.stringify(val)
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                $q.loading.hide();
                                GetGalleries();
                            });
                    }
                })
                .onCancel(() => {
                    //
                })
                .onDismiss(() => {
                    //
                });
        };

        const EditGalleryClicked = (val) => {
            if (licenseActive.value === true) {
                editMode.value = true;
                createMode.value = false;
                editingItem.value = JSON.parse(JSON.stringify(val));
                GetUploaderHeaders();

                imagegallery_rows.value = [];
                imagegallery_rows_selected.value = {};
                imagegallery_rows_selected_arr.value = [];
                setTimeout(() => {
                    tab.value = "settings";
                }, 100);

                AddDragListeners();
                GetGalleryImages();
            }
        };

        const TogglePublished = (val) => {
            editMode.value = false;
            createMode.value = false;
            editingItem.value = JSON.parse(JSON.stringify(val));

            SaveEditingClicked();
        };

        const CancelEditingClicked = () => {
            editMode.value = false;
            $q.loading.hide();
        };

        const SaveEditingClicked = () => {
            let _REST_URL = "http://localhost/";
            let _NONCENAME = "updateimagegallery";

            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
                _NONCENAME = "addimagegallery";
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/updateimagegallery`;
            if (createMode.value) {
                finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/addimagegallery`;
            }

            $q.loading.show();

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX[_NONCENAME].nonce
                    },
                    body: JSON.stringify(editingItem.value)
                })
                    .then((response) => response.json())
                    .then(async (data) => {
                        GetGalleries();
                        editMode.value = false;
                        $q.loading.hide();
                    });
            }
        };

        const CreateGalleryClicked = () => {
            if (licenseActive.value === true) {
                createMode.value = true;
                imagegallery_rows.value = [];
                imagegallery_rows_selected.value = {};
                imagegallery_rows_selected_arr.value = [];

                editingItem.value = {
                    published: true,
                    id: "Created on Save",
                    title: t("newimagecollection"),
                    description: "",
                    slug: "new-image-slug",
                    created_at: "",
                    updated_at: "",
                    images: []
                };
                editMode.value = true;

                setTimeout(() => {
                    tab.value = "settings";
                }, 100);
            }
        };

        function AddGalleryImages(imagestoadd) {
            let _REST_URL = "http://localhost/";
            let _NONCENAME = "updateimagegallery";

            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
                _NONCENAME = "addimagegallery";
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/addgalleryimages`;
            $q.loading.show();

            var body_data = JSON.stringify({
                images: imagestoadd,
                imagegallery_id: editingItem.value.id
            });

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX[_NONCENAME].nonce
                    },
                    body: body_data
                })
                    .then((response) => response.json())
                    .then(async (data) => {
                        $q.loading.hide();

                        GetGalleryImages();
                    })
                    .catch((error) => {
                        $q.loading.hide();
                        console.log(error);
                    });
            }
        }

        function GetGalleries() {
            loading.value = true;
            tablerows.value = [];

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/getimagegalleries`;
            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX.getimagegalleries.nonce
                    }
                })
                    .then((response) => {
                        return response.json();
                    })
                    .then((result) => {
                        let data = result.imagegalleries;

                        // Set All categories
                        if (data.length > 0) {
                            data.forEach((element) => {
                                element["published"] === 1 || element["published"] === "1"
                                    ? (element["published"] = true)
                                    : (element["published"] = false);
                            });
                        }

                        tablerows.value = data;

                        loading.value = false;
                    });
            }
        }

        function GetGalleryImages() {
            loading.value = true;
            imagegallery_loading.value = true;

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl =
                `${_REST_URL}/wp-json/aeroscroll/v1/getgalleryimages?` +
                new URLSearchParams({
                    imagegallery_id: editingItem.value.id
                });
            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX.getgalleryimages.nonce
                    }
                })
                    .then((response) => {
                        return response.json();
                    })
                    .then((result) => {
                        let data = result;
                        imagegallery_loading.value = false;

                        // Set All categories
                        if (data.galleryimages) {
                            for (var f = 0; f < data.galleryimages.length; f++) {
                                data.galleryimages[f].selected = false;
                                data.galleryimages[f].edit = true;
                            }

                            imagegallery_rows.value = data.galleryimages;

                            // For TEST
                            imagegallery_rows_selected.value = {};
                            imagegallery_rows_selected_arr.value = [];
                        }

                        loading.value = false;
                    });
            }
        }

        function GenerateSlug(title) {
            var _slug = title
                .toLowerCase()
                .replace(/ /g, "-")
                .replace(/[^\w-]+/g, "");
            return _slug;
        }

        function SearchMethod(rows, terms) {
            const lowerTerms = terms ? terms.toLowerCase() : "";
            const filteredRows = rows.filter((row) => {
                for (var k = 0; k < tablecolumns.value.length; k++) {
                    let col = tablecolumns.value[k];
                    if ((row[col.name] + "").toLowerCase().includes(lowerTerms)) {
                        return true;
                    }
                }
            });
            return filteredRows;
        }

        function SearchMethodImages(rows, terms) {
            const lowerTerms = terms ? terms.toLowerCase() : "";
            const filteredRows = rows.filter((row) => {
                for (var k = 0; k < imagegallery_columns.length; k++) {
                    let col = imagegallery_columns[k];
                    if ((row[col.name] + "").toLowerCase().includes(lowerTerms)) {
                        return true;
                    }
                }
            });
            return filteredRows;
        }

        function getGhostParent() {
            return document.body;
        }

        function AddDragListeners() {
            var igtable = document.getElementById("igtable");
            if (igtable) {
                igtable.addEventListener("mousedown", (e) => {
                    //
                });

                igtable.addEventListener("mouseup", (e) => {
                    //
                });

                igtable.addEventListener("mousemove", (e) => {
                    //
                });
            }
        }

        function RefreshTable() {
            if (licenseActive.value === true) {
                GetGalleries();
            }
        }

        function DraggableEnded(evt) {
            drag.value = false;
        }

        function OnAddImagesClicked() {
            const timeStamp = Date.now();
            filemanager_visible.value = true;
        }

        function OnImagesSelected(images) {
            var last_index = imagegallery_rows.value.length;

            console.log("OnImagesSelected: ", images);

            var _index = last_index;
            var imagestoadd = [];
            for (const key in images) {
                var img = images[key];

                if (img.relativeurlfile) img.image = img.relativeurlfile;

                if (img.folder !== 1) {
                    var item = {
                        id: img.id,
                        order: _index + 1,
                        name: img.name,
                        image: img.image,
                        size: img.size,
                        date: img.date,
                        relativedir: img.relativedir,
                        selected: false,
                        edit: true,
                        justadded: true,
                        updated: false,
                        title: "",
                        description: ""
                    };

                    if (img.optimized) {
                        item.optimized = img.optimized;
                        item.optimizedsize = img.optimizedsize;
                    }

                    // Will update on Refresh
                    imagestoadd.push(item);

                    _index++;
                }
            }

            if (imagestoadd.length > 0) {
                // Insert into DB and Refresh
                AddGalleryImages(imagestoadd);
            }
        }

        function OnImagesMediaLibrarySelected(images) {
            var last_index = imagegallery_rows.value.length;

            console.log("OnImagesSelected: ", images);
            var _index = last_index;

            console.log("OnImagesMediaLibrarySelected: ", images);

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            var imagestoadd = [];
            for (var k = 0; k < images.length; k++) {
                var img = images[k];
                console.log("I M G: ", img);

                var _relativedir = img.url;
                _relativedir = _relativedir.replace(img.title, "");
                _relativedir = _relativedir.replace(_REST_URL, "");

                var item = {
                    id: img.id,
                    media_gallery_id: img.id,
                    order: _index + 1,
                    name: img.title,
                    image: img.title,
                    size: img.filesizeInBytes,
                    date: img.date,
                    relativedir: _relativedir,
                    selected: false,
                    edit: true,
                    justadded: true,
                    updated: false,
                    title: "",
                    description: ""
                };

                console.log("I T E M: ", item);

                // Will update on Refresh
                imagestoadd.push(item);

                _index++;
            }

            if (imagestoadd.length > 0) {
                // Insert into DB and Refresh
                AddGalleryImages(imagestoadd);
            }
        }

        function DeleteGalleryImageClicked(image) {
            // Delete from Database
            $q.loading.show();

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/deletegalleryimages`;
            var img_id = image.id;

            var body_data = JSON.stringify({
                images: [image.id]
            });

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX.deletegalleryimages.nonce
                    },
                    body: body_data
                })
                    .then((response) => response.json())
                    .then((data) => {
                        $q.loading.hide();

                        var listToDelete = [img_id];
                        listToDelete.forEach((x) =>
                            imagegallery_rows_selected_arr.value.splice(
                                imagegallery_rows_selected_arr.value.findIndex((n) => n.id === x),
                                1
                            )
                        );

                        imagegallery_rows.value = RemoveItemFromArraywithID(imagegallery_rows.value, image.id);
                    });
            }
        }

        function EditGalleryImageClicked(imageobj) {
            editimagedialog_data.value = {
                id: imageobj.id,
                image: imageobj.image,
                title: imageobj.title,
                description: imageobj.description,
                order: imageobj.order
            };
            editimagedialog.value = true;
        }

        function CheckboxSelectAllImages() {
            imagegallery_rows_selected_arr.value = [];

            for (var k = 0; k < imagegallery_rows.value.length; k++) {
                imagegallery_rows_selected_arr.value.push(imagegallery_rows.value[k].id);
                imagegallery_rows.value[k].selected = !allimagesselected.value;
            }
        }

        function OnDeleteMultipleGalleryImagesClicked() {
            var total_images = [];

            if (imagegallery_rows_selected_arr.value.length > 0) {
                for (var k = 0; k < imagegallery_rows_selected_arr.value.length; k++) {
                    var it = imagegallery_rows_selected_arr.value[k];
                    total_images.push(it);
                }

                $q.loading.show();

                let _REST_URL = "http://localhost/";
                if (window["REST_URL"]) {
                    _REST_URL = window["REST_URL"].url;
                }

                let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/deletegalleryimages`;
                var body_data = JSON.stringify({
                    images: total_images
                });

                let _APEX = window["APEX"];
                if (_APEX) {
                    fetch(finalurl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            // eslint-disable-next-line
                            "X-WP-Nonce": _APEX.deletegalleryimages.nonce
                        },
                        body: body_data
                    })
                        .then((response) => {
                            return response.json();
                        })
                        .then((data) => {
                            $q.loading.hide();

                            imagegallery_rows_selected.value = {};
                            imagegallery_rows_selected_arr.value = [];

                            GetGalleryImages();
                        })
                        .catch((error) => {
                            $q.loading.hide();
                        });
                }
            } else {
                // Now Images selected
                $q.dialog({
                    title: "Alert",
                    message: "No Images selected please select some"
                });
            }
        }

        function SaveImageDetails() {
            console.log("SaveImageDetails");
            editimagedialog.value = false;

            let _REST_URL = "http://localhost/";
            let _NONCENAME = "updateimagegallery";

            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
                _NONCENAME = "addimagegallery";
            }

            // Update directly in DB
            let updateimagesurl = `${_REST_URL}/wp-json/aeroscroll/v1/updategalleryimages`;

            $q.loading.show();

            var body_data = JSON.stringify({
                images: [editimagedialog_data.value],
                imagegallery_id: editingItem.value.id
            });

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(updateimagesurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX[_NONCENAME].nonce
                    },
                    body: body_data
                })
                    .then((response) => {
                        //console.log("res: ", response.text());
                        return response.json();
                    })
                    .then(async (data) => {
                        console.log("SaveImageDetails: ", data);
                        $q.loading.hide();
                    });
            }

            // Then Update List
            for (var k = 0; k < imagegallery_rows.value.length; k++) {
                var row = imagegallery_rows.value[k];
                if (row.id === editimagedialog_data.value.id) {
                    imagegallery_rows.value[k].title = editimagedialog_data.value.title;
                    imagegallery_rows.value[k].description = editimagedialog_data.value.description;
                    break;
                }
            }
        }

        function customSortImages(rows, sortBy, descending) {
            const data = [...rows];

            if (sortBy) {
                data.sort((a, b) => {
                    const x = descending ? b : a;
                    const y = descending ? a : b;

                    if (sortBy === "name") {
                        // string sort
                        return x[sortBy] > y[sortBy] ? 1 : x[sortBy] < y[sortBy] ? -1 : 0;
                    } else {
                        // numeric sort
                        return parseFloat(x[sortBy]) - parseFloat(y[sortBy]);
                    }
                });
            }

            return data;
        }

        function PaginationUpdated(newPagination) {
            if (newPagination && !justLoaded) {
                $q.sessionStorage.set("ig_table_rowsperpage", newPagination.rowsPerPage);
                initialPagination.value.rowsPerPage = newPagination.rowsPerPage;
            }
        }

        watchEffect(() => {
            if (igtableref.value) {
                AddDragListeners();
            } else {
                // not mounted yet, or the element was unmounted (e.g. by v-if)
            }
        });

        const dragOptions = computed(() => {
            return {
                animation: 0,
                ghostClass: "ghost"
            };
        });

        function ExportGallery() {
            let _REST_URL = "http://localhost/";
            let _NONCENAME = "exportimagegallery";

            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
                _NONCENAME = "exportimagegallery";
            }

            // Update directly in DB
            let updateimagesurl = `${_REST_URL}/wp-json/aeroscroll/v1/exportimagegallery`;

            $q.loading.show({
                message: t("exportinggalleryimages")
            });

            var body_data = JSON.stringify({
                imagegallery_id: editingItem.value.id
            });

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(updateimagesurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX[_NONCENAME].nonce
                    },
                    body: body_data
                })
                    .then((response) => response.json())
                    .then(async (data) => {
                        if (data) {
                            if (data.success === true) {
                                if (data.exportfile !== "") {
                                    document.location.href = data.exportfile;
                                }
                            }
                        }
                        $q.loading.hide();
                    });
            }
        }

        function ImportGallery() {
            //
            fileImporterElement.value.pickFiles();
        }

        function UploadImportGallery() {
            //
        }

        function importFilePicked(files) {
            GetUploaderHeaders();
            UploadImportGallery();
        }

        function GetUploaderURL() {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/importimagegallery?imagegallery_id=` + editingItem.value.id;
            return finalurl;
        }

        function onUploaderFilesAdded(info) {
            $q.loading.show({
                message: t("uploadingimportfile")
            });
            GetUploaderHeaders();

            fileImporterElement.value.upload();
        }

        function onUploadedImportGallery(info) {
            $q.loading.hide();
            console.log("onUploadedImportGallery: ", info);

            try {
                var ressp = JSON.parse(info.xhr.responseText);
                if (ressp.success === true) {
                    GetGalleryImages();
                } else {
                    if (ressp.error_code) {
                        if (ressp.error_code === "upload_max_filesize") {
                            $q.dialog({
                                title: t("error"),
                                message: t("upload_max_filesize_error")
                            });
                        }
                    }
                }
            } catch (ex) {}
        }

        function onUploaderFilesFailed(info) {
            $q.loading.hide();

            $q.dialog({
                title: t("error"),
                message: t("erroruploadingfile")
            });
        }

        function onCloseManager() {
            setTimeout(() => {
                $q.loading.hide();
                GetGalleryImages();
            }, 1000);
        }

        function GetUploaderHeaders() {
            let _headers = [];

            let _APEX = window["APEX"];
            if (_APEX.importimagegallery) {
                if (_APEX) {
                    _headers = [
                        {
                            name: "X-WP-Nonce",
                            value: _APEX.importimagegallery.nonce //_APEX["importimagegallery"].nonce
                        },
                        {
                            name: "relativedir",
                            value: "root"
                        },
                        {
                            name: "imagegallery_id",
                            value: editingItem.value.id
                        }
                    ];
                }
                uploaderHeaders.value = _headers;
            }
        }

        // Function to open WordPress Media Library
        const openMediaLibrary = () => {
            if (!wp || !wp.media) {
                console.error("wp.media is not available");
                return;
            }

            // Open the media dialog
            frame.open();
        };

        onBeforeMount(() => {
            var incomingLocale = window["TRANSLATIONS"].locale;
            if (incomingLocale) {
                locale.value = incomingLocale;
            }

            setTimeout(() => {
                justLoaded = false;
            }, 200);
        });

        onMounted(() => {
            document.addEventListener("DOMContentLoaded", function () {
                loading.value = true;
                var pro_func = document.defaultView.window["pro_func"];
                if (ispro.value === true) {
                    let _APEX = window["APEX"];

                    // 1. First check License
                    pro_func.CheckLicense((license_result) => {
                        if (license_result.active === true || license_result.code === "key_expired") {
                            licenseActive.value = true;

                            // 2. Then Get Galleries
                            GetGalleries();
                        } else {
                            licenseNotActive.value = true;
                            licenseActive.value = false;
                        }

                        loading.value = false;
                    }, _APEX.manageserial.nonce);
                } else {
                    // Else Get Galleries
                    GetGalleries();
                    licenseActive.value = true;
                    loading.value = false;
                }
            });

            categories_options.value = [];
            if ($q.sessionStorage.getItem("ig_table_rowsperpage")) {
                if ($q.sessionStorage.getItem("ig_table_rowsperpage") !== "undefined") {
                    initialPagination.value.rowsPerPage = $q.sessionStorage.getItem("ig_table_rowsperpage");
                }
            }

            // Ensure wp.media is available
            setTimeout(() => {
                console.log(wp);
                if (wp) {
                    frame = wp.media({
                        title: "Select Images",
                        button: {
                            text: "Add to Library"
                        },
                        multiple: true // Allow multiple selection
                    });

                    // When an image is selected
                    frame.on("select", () => {
                        // Get selected images from media library
                        const attachments = frame.state().get("selection").toJSON();

                        // Reset selected images and IDs
                        selectedImages.value = [];
                        imageIds.value = [];

                        //console.log("A T T A C H M E N T S: ", attachments);

                        // Process the selected images
                        /* attachments.forEach((attachment) => {
                        // Add image to selected images array
                        selectedImages.value.push({
                            id: attachment.id,
                            url: attachment.url,
                        });

                        // Add image ID to imageIds array
                        imageIds.value.push(attachment.id);
                    }); */

                        OnImagesMediaLibrarySelected(attachments);
                    });
                }
            }, 1000);
        });

        return {
            t,
            fileImporterElement,
            uploadProgress,
            importFile,
            igtableref,
            loading,
            drag,
            dragOptions,
            imagegallery_loading,
            filemanager_visible,
            initialPagination,
            tab,
            tablecolumns,
            tablerows,
            imagegallery_columns,
            imagegallery_rows,
            imagegallery_rows_selected,
            imagegallery_rows_selected_arr,
            searchField,
            searchValue,
            searchValue_images,
            editMode,
            editingItem,
            notification_success,
            createMode,
            empty_image,
            categories_options,
            editimagedialog,
            editimagedialog_data,
            allimagesselected,
            licenseActive,
            licenseNotActive,
            uploaderHeaders,
            ispro,
            selectedImages,
            imageIds,
            openMediaLibrary,
            onCloseManager,
            EditGalleryClicked,
            DeleteGalleryClicked,
            CancelEditingClicked,
            SaveEditingClicked,
            CreateGalleryClicked,
            TogglePublished,
            SearchMethod,
            SearchMethodImages,
            PaginationUpdated,
            GetUploaderURL,
            onUploaderFilesAdded,
            getGhostParent,
            RefreshTable,
            DraggableEnded,
            CalculateSize,
            GetDateFromTimestamp,
            OnAddImagesClicked,
            OnImagesSelected,
            GetGalleryImages,
            DeleteGalleryImageClicked,
            EditGalleryImageClicked,
            CheckboxSelectAllImages,
            OnDeleteMultipleGalleryImagesClicked,
            SaveImageDetails,
            customSortImages,
            ExportGallery,
            ImportGallery,
            UploadImportGallery,
            importFilePicked,
            GetUploaderHeaders,
            onUploadedImportGallery,
            onUploaderFilesFailed,
            filename_str,
            title_str,
            description_str
        };
    }
});
</script>
<style scoped>
.uploadingLabel {
    font-size: 13pt;
    color: #2c3338;
    z-index: 100;
    position: relative;
    top: -30px;
    position: absolute;
}
</style>

<style lang="scss" scoped>
.operation-icon {
    cursor: pointer;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
