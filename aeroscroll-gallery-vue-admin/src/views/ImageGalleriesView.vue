<template>
    <div class="home">
        <div class="q-fit q-column q-no-wrap q-justify-start q-items-stretch q-content-start">
            <!-- <div class="q-row q-py-md">
                <h5 style="margin: 0px;">{{ t('manage_imagegalleries') }}</h5>
            </div> -->
            <q-card flat bordered class="admin-card" style="position: relative;">
                <!-- <div v-if="!editMode" class="q-row q-py-sm text-right">
                <div class="q-col">

                </div>
                <div class="q-col q-px-sm">
                    <q-btn color="primary" label="Create" @click="CreateGalleryClicked"></q-btn>
                </div>
            </div> -->
                <div class="q-row q-py-sm aeroscroll-page-header">
                    <span>{{ t("manage_imagegalleries") }}</span>
                </div>
                <div v-if="!editMode" class="q-row q-pb-sm">
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
                            <q-inner-loading showing color="primary" />
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
                    <q-tabs v-model="tab" no-caps align="left" class="q-pl-md">
                        <q-tab name="settings" icon="settings" :label="t('settings')" />
                        <q-tab name="images" icon="images" :label="t('images')" :disable="createMode" />
                        <!-- <q-tab name="upgrade" icon="upgrade" label="Upgrade" /> -->
                    </q-tabs>
                    <q-tab-panels v-model="tab" animated>
                        <q-tab-panel name="settings">
                            <div class="text-h6">{{ t("settings") }}</div>
                            <p>{{ t("settings_desc") }}</p>
                            <div class="q-row q-py-sm">
                                <div class="q-col-2 text-left q-px-sm">
                                    <div class="text-field-header">ID</div>
                                    <div class="text-caption">{{ t("id_desc_collections") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined v-model="editingItem.id" dense readonly></q-input>
                                </div>
                            </div>
                            <div class="q-row q-py-sm">
                                <div class="q-col-2 text-left q-px-sm">
                                    <div class="text-field-header">{{ t("imagegal_title") }}</div>
                                    <div class="text-caption">{{ t("imagegal_title_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined v-model="editingItem.title" dense></q-input>
                                </div>
                            </div>
                            <!-- <div class="q-row q-py-sm">
                                <div class="q-col-2 text-left q-px-sm">
                                    <div class="text-field-header">{{ t("slug") }}</div>
                                    <div class="text-caption">{{ t("imagegal_slug_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width text-lowercase" outlined v-model="editingItem.slug" dense></q-input>
                                </div>
                            </div> -->
                            <div class="q-row q-py-sm">
                                <div class="q-col-2 text-left q-px-sm">
                                    <div class="text-field-header">{{ t("description") }}</div>
                                    <!-- <div class="text-caption">{{ t('slug') }}</div> -->
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined v-model="editingItem.description" type="textarea" dense></q-input>
                                </div>
                            </div>
                            <!-- <div class="q-row q-py-sm">
                            <div class="q-col-2 text-left q-px-sm">
                                <div class="text-field-header">Ordering</div>
                                <div class="text-caption">Select Ordering</div>
                            </div>
                            <div class="q-col">
                                <q-input class="condensed-width" outlined v-model="editingItem.ordering" type="number" dense></q-input>
                            </div>
                        </div> -->
                            <div class="q-row q-py-sm q-items-center">
                                <div class="q-col-2 text-left q-px-sm">
                                    <div class="text-field-header">{{ t("published") }}</div>
                                    <!-- <div class="text-caption">Enable or Disable the Grid</div> -->
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
                                <span style="color: #f85c2f;">Please Save first in order to activate Images selection Tab!</span>
                            </div>

                            <!-- <div class="q-row q-py-sm">
                            <div class="q-col"></div>
                            <div class="q-col-1 text-right">
                                <q-btn color="primary" label="Cancel" @click="CancelEditingClicked"></q-btn>
                            </div>
                            <div class="q-col-1 text-right q-mx-sm">
                                <q-btn color="primary" label="Save" @click="SaveEditingClicked"></q-btn>
                            </div>
                        </div> -->
                        </q-tab-panel>

                        <q-tab-panel name="images">
                            <div class="text-h6">{{ t("selectedimages") }}</div>
                            <p>{{ t("selectedimages_desc") }}</p>

                            <!-- Images Grid -->
                            <div>
                                <div class="q-row q-py-lg q-pl-lg">
                                    <!-- <div class="q-col imagesdraggable-header-cell" style="flex-basis: 80px; flex-grow: 0;">
                                        <div style="color: rgb(69, 158, 235); font-weight: bold; font-size: 16px;">#</div>
                                    </div> -->
                                    <!-- <div class="q-col text-left">
                                        <q-checkbox
                                            @update:model-value="
                                                (value) => {
                                                    CheckboxSelectAllImages($event);
                                                }
                                            "
                                            v-model="allimagesselected"
                                        /><span style="margin-left: 30px; font-weight: bold; font-size: 1rem;">{{ t('selectall') }}</span>
                                    </div> -->
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
                                                <q-icon size="sm" name="workspace_premium" class="q-mr-sm protooltip_icon" /><span
                                                    class="protooltip_label"
                                                    >{{ t("profeature") }}</span
                                                >
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
                                                <q-icon size="sm" name="workspace_premium" class="q-mr-sm protooltip_icon" /><span
                                                    class="protooltip_label"
                                                    >{{ t("profeature") }}</span
                                                >
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
                                        <q-btn
                                            color="primary"
                                            style="margin-left: 10px; margin-bottom: 10px;"
                                            :label="t('addimages')"
                                            icon="folder_zip"
                                            @click="OnAddImagesClicked"
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
                                    <!-- <div>{{ element.name }}</div> -->
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
                                                            <span style="white-space: break-spaces; text-align: center;">{{
                                                                t("error_loading_image")
                                                            }}</span>
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
                                                <!-- {{ t("page") }}: {{ scope.pagination.page }} {{ t("of") }}: {{ scope.pagesNumber }}

                                                {{ t("rowsperpage") }}: <q-select outlined dense v-model="scope.pagination.rowsPerPage" :options="[10, 25, 50, 100, 200, 500, 0]" />

                                                <q-btn
                                                    v-if="scope.pagesNumber > 2"
                                                    icon="first_page"
                                                    color="grey-8"
                                                    round
                                                    dense
                                                    flat
                                                    :disable="scope.isFirstPage"
                                                    @click="scope.firstPage"
                                                />

                                                <q-btn
                                                    icon="chevron_left"
                                                    color="grey-8"
                                                    round
                                                    dense
                                                    flat
                                                    :disable="scope.isFirstPage"
                                                    @click="scope.prevPage"
                                                />

                                                <q-btn
                                                    icon="chevron_right"
                                                    color="grey-8"
                                                    round
                                                    dense
                                                    flat
                                                    :disable="scope.isLastPage"
                                                    @click="scope.nextPage"
                                                />

                                                <q-btn
                                                    v-if="scope.pagesNumber > 2"
                                                    icon="last_page"
                                                    color="grey-8"
                                                    round
                                                    dense
                                                    flat
                                                    :disable="scope.isLastPage"
                                                    @click="scope.lastPage"
                                                /> -->
                                                <div>{{ t("totalimages") }} : {{ imagegallery_rows.length }}</div>
                                            </div>
                                        </template>
                                    </q-table>
                                    <!-- <Container 
                                        orientation="vertical" 
                                        drag-class="card-ghost" 
                                        drop-class="card-ghost-drop" 
                                        :get-ghost-parent="getGhostParent" 
                                        :remove-on-drop-out="true" 
                                        @drop-ready="onDropReady"
                                        @drop="onDrop">
                                        <Draggable v-for="(item, i) in imagegallery_rows" :key="item.id">
                                            <div class="draggable-item">{{ i + 1 }} -> {{ item.name }}</div>
                                        </Draggable>
                                    </Container> -->
                                </div>

                                <!-- <q-table id="igtable" ref="igtableref" grid title="Images" class="aeroscroll-table-imagegalleries" :rows="imagegallery_rows" :columns="imagegallery_columns" row-key="id" :filter="searchValue_images" :filter-method="SearchMethodImages" hide-header>
                                <template v-slot:top-right>
                                    <q-input outlined dense debounce="300" v-model="searchValue_images" placeholder="Search">
                                        <template v-slot:append>
                                            <q-icon name="search" />
                                        </template>
                                    </q-input>
                                </template>
                                <template v-slot:item="props">
                                    <div class="q-pa-md col-xs-12 col-sm-6 col-md-4">
                                        <q-card :id="props.row.id">
                                            <q-card-section class="text-center unselectable">
                                                <strong>{{ props.row.name }}</strong>
                                            </q-card-section>
                                            <q-separator />
                                            <q-card-section class="flex flex-center unselectable">
                                                <q-img class="unselectable" :src="props.row.image" loading="lazy" spinner-color="primary" height="140px" style="max-width: 150px" />
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </template>
                            </q-table> -->
                            </div>
                        </q-tab-panel>

                        <!-- <q-tab-panel name="upgrade">
                        <div class="text-h6">Movies</div>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    </q-tab-panel> -->
                    </q-tab-panels>
                    <div class="aeroscroll-flex q-pa-md">
                        <div class="aeroscroll-flex-child"></div>
                        <div class="fixflex text-right">
                            <q-btn color="primary" :label="t('cancel')" @click="CancelEditingClicked" dense></q-btn>
                        </div>
                        <div class="fixflex text-right" style="margin-left: 10px;">
                            <q-btn color="green" :label="t('savechanges')" @click="SaveEditingClicked" dense></q-btn>
                        </div>
                    </div>
                </div>
            </q-card>
        </div>
        <!-- <q-dialog v-model="filemanager_visible" fullWidth fullHeight> -->
        <FileManager v-model="filemanager_visible" :ISNOTPRO="!ispro" @onImagesSelected="OnImagesSelected" @onCloseManager="onCloseManager" />
        <!-- </q-dialog> -->

        <!-- Popp dialog for edit image -->
        <transition name="fade">
            <div v-if="editimagedialog" class="q-custom-dialog">
                <div class="q-custom-dialog-bg"></div>
                <q-card class="my-card" style="min-width: 500px;">
                    <q-img class="popupeditimage_img" loading="lazy" spinner-color="primary" :src="editimagedialog_data.image">
                        <!-- <div class="popupeditimage_mask">
                            <q-icon name="change_circle" color="white" size="4rem" />
                            <span>{{ t('changeimage') }}</span>
                        </div>  -->
                    </q-img>
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
                        <q-btn @click="editimagedialog = false" color="primary" :label="t('cancel')"></q-btn>
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
            //rowsNumber: imagegallery_rows.value.length
            // rowsNumber: xx if getting data from a server
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
                } /* ,
                {
                    name: "slug",
                    field: "slug",
                    required: true,
                    label: "Slug",
                    align: "left",
                    sortable: true
                } */,
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
                /* {
                name: "updated_at",
                field: "updated_at",
                required: false,
                label: "Update Date",
                align: "left",
                sortable: true
            }, */
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
            //console.log("deleteItem: ", val);
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
                    // console.log('>>>> OK')
                    //var rest_url = "http://localhost/wp_playground/wp-json";
                    $q.loading.show();

                    let _REST_URL = "http://localhost/";
                    if (window["REST_URL"]) {
                        _REST_URL = window["REST_URL"].url;
                    }

                    //let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/updateimagegallery`;
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
                                //console.log("POST delete result: ", data);
                                $q.loading.hide();
                                GetGalleries();
                            });
                    }
                })
                .onCancel(() => {
                    // console.log('>>>> Cancel')
                })
                .onDismiss(() => {
                    // console.log('I am triggered on both OK and Cancel')
                });
        };

        const EditGalleryClicked = (val) => {
            if (licenseActive.value === true) {
                console.log("editItem: ", JSON.parse(JSON.stringify(val)));
                editMode.value = true;
                createMode.value = false;
                editingItem.value = JSON.parse(JSON.stringify(val));
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
            //console.log("editItem: ", JSON.parse(JSON.stringify(val)));
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
            //let addimagesurl = `${_REST_URL}/wp-json/aeroscroll/v1/addgalleryimages`;
            //let updateimagesurl = `${_REST_URL}/wp-json/aeroscroll/v1/updategalleryimages`;

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
            //console.log("Get Image Collections");

            loading.value = true;

            tablerows.value = [];

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/getimagegalleries`;
            let _APEX = window["APEX"];
            //console.log(_APEX);
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
                        //console.log("GetGalleries response: ", response);
                        return response.json();
                    })
                    .then((result) => {
                        let data = result.imagegalleries;

                        // Set All categories
                        //console.log("imagegalleries: ", data);
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
                        console.log("gallery images: ", data);
                        if (data.galleryimages) {
                            for (var f = 0; f < data.galleryimages.length; f++) {
                                data.galleryimages[f].selected = false;
                                data.galleryimages[f].edit = true;

                                //data.galleryimages[f].title = "Test Title"; // REVIEW
                                //data.galleryimages[f].description = "Test a very very long description with text that is very very long"; // REVIEW
                            }

                            imagegallery_rows.value = data.galleryimages;

                            // For TEST
                            //imagegallery_rows.value = data.galleryimages.slice(1, 30);
                            imagegallery_rows_selected.value = {};
                            imagegallery_rows_selected_arr.value = [];

                            //initialPagination.value.rowsNumber = imagegallery_rows.value.length;

                            /* initialPagination.value = {            
                                rowsPerPage: 25
                                rowsNumber: imagegallery_rows.value.length
                            } */
                            //console.log("IN PA : ", initialPagination.value);
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

        /* function onDragEnter(evt, item) {
            console.log("onDragEnter");
        }

        function onDragLeave(evt, list) {
            console.log("onDragLeave");
        }

        function onDragOver(evt, list) {
            console.log("onDragOver");
        }

        function onDrop(dropResult) {
            console.log("onDrop: ", dropResult);
            imagegallery_rows.value = applyDrag(imagegallery_rows.value, dropResult);
        }

        function onDropReady(dropResult) {
            console.log('drop ready', dropResult);
        } */

        function getGhostParent() {
            return document.body;
        }

        /* function applyDrag(arr, dragResult) {
            const { removedIndex, addedIndex, payload } = dragResult;

            if (removedIndex === null && addedIndex === null) return arr;
            const result = [...arr];
            let itemToAdd = payload;

            if (removedIndex !== null) {
                itemToAdd = result.splice(removedIndex, 1)[0];
            }
            if (addedIndex !== null) {
                result.splice(addedIndex, 0, itemToAdd);
            }
            return result;
        } */

        function AddDragListeners() {
            var igtable = document.getElementById("igtable");
            //console.log("AddDragListeners: ", igtable);
            if (igtable) {
                igtable.addEventListener("mousedown", (e) => {
                    console.log("mousedown");
                });

                igtable.addEventListener("mouseup", (e) => {
                    console.log("mouseup");
                });

                igtable.addEventListener("mousemove", (e) => {
                    console.log("mousemove");
                });
            }
        }

        function RefreshTable() {
            if (licenseActive.value === true) {
                GetGalleries();
            }
        }

        function RefreshTableImages() {
            GetGalleryImages();
        }

        function DraggableEnded(evt) {
            drag.value = false;

            /* // Set new Order
            imagegallery_rows.value[evt.oldIndex].order = evt.newIndex+1;
            imagegallery_rows.value[evt.oldIndex].updated = true;

            // Also update all previous images by the amount +1 or -1
            if(evt.newIndex < evt.oldIndex)
            {
                for(var j=evt.oldIndex-1; j >= evt.newIndex; j--)
                {
                    imagegallery_rows.value[j].order = imagegallery_rows.value[j].order+1;
                    imagegallery_rows.value[j].updated = true;
                }
            } else {
                for(var i=evt.oldIndex+1; i < evt.newIndex-1; i++)
                {
                    imagegallery_rows.value[i].order = imagegallery_rows.value[i].order-1;
                    imagegallery_rows.value[i].updated = true;
                }
            } */

            //console.log("evt: ", evt);

            /* var A_ItemOrder = imagegallery_rows.value[evt.oldIndex].order;
            var B_ItemOrder = imagegallery_rows.value[evt.newIndex].order;

            console.log("oldIndex: ", evt.oldIndex);
            console.log("newIndex: ", evt.newIndex);

            console.log("oldOrder: ", A_ItemOrder);
            console.log("newOrder: ", B_ItemOrder);

            imagegallery_rows.value[evt.oldIndex].order = B_ItemOrder;
            imagegallery_rows.value[evt.oldIndex].updated = true;
            imagegallery_rows.value[evt.newIndex].order = A_ItemOrder;
            imagegallery_rows.value[evt.newIndex].updated = true; */
        }

        function OnAddImagesClicked() {
            const timeStamp = Date.now();
            filemanager_visible.value = true;
        }

        function OnImagesSelected(images) {
            // TODO
            // HERE INSERT IN DATABASE Immediatelly
            console.log("OnImagesSelected: ", images);

            var last_index = imagegallery_rows.value.length;

            var _index = last_index;
            var imagestoadd = [];
            for (const key in images) {
                var img = images[key];
                if (img.folder !== 1) {
                    var item = {
                        id: img.id,
                        //id: Math.floor(Math.random() * 1000000000) + 10000000000,
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
                    //console.log("img.relativedir: ", img.relativedir);

                    if (img.optimized) {
                        item.optimized = img.optimized;
                        item.optimizedsize = img.optimizedsize;
                    }

                    // Will update on Refresh
                    //imagegallery_rows.value.push(item);
                    imagestoadd.push(item);

                    _index++;
                }
            }

            console.log("... imagestoadd: ", imagestoadd);

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
                        //console.log("POST delete image from gallery result: ", data);
                        $q.loading.hide();

                        var listToDelete = [img_id];
                        listToDelete.forEach((x) =>
                            imagegallery_rows_selected_arr.value.splice(
                                imagegallery_rows_selected_arr.value.findIndex((n) => n.id === x),
                                1
                            )
                        );

                        //delete imagegallery_rows_selected.value[img_id];
                        imagegallery_rows.value = RemoveItemFromArraywithID(imagegallery_rows.value, image.id);
                    });
            }
        }

        function EditGalleryImageClicked(imageobj) {
            console.log("Edit: ", imageobj);
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
            //console.log("imagegallery_rows: ", imagegallery_rows.value);
            imagegallery_rows_selected_arr.value = [];

            for (var k = 0; k < imagegallery_rows.value.length; k++) {
                //imagegallery_rows_selected.value[imagegallery_rows.value[k].id] = true;
                imagegallery_rows_selected_arr.value.push(imagegallery_rows.value[k].id);
                imagegallery_rows.value[k].selected = !allimagesselected.value;
            }
        }

        /* function CheckboxSelectedImage(value, element, evt) {
            element.selected = value;
            allimagesselected.value = false;

            if (value === true) {
                imagegallery_rows_selected.value[element.id] = true;
            } else {
                delete imagegallery_rows_selected.value[element.id];
            }
        } */

        function OnDeleteMultipleGalleryImagesClicked() {
            var total_images = [];

            if (imagegallery_rows_selected_arr.value.length > 0) {
                //if (Object.keys(imagegallery_rows_selected.value).length > 0) {
                for (var k = 0; k < imagegallery_rows_selected_arr.value.length; k++) {
                    //for (var key in imagegallery_rows_selected.value) {
                    var it = imagegallery_rows_selected_arr.value[k];
                    total_images.push(it);
                }

                //console.log("total_images: ", JSON.stringify(total_images) );

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
                console.log("X-WP-Nonce: ", _APEX.deletegalleryimages.nonce);
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
                            //console.log("RES::: ", response.text());
                            return response.json();
                        })
                        .then((data) => {
                            console.log("POST delete MULTIPLE image from gallery result: ", data);
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
            //console.log(editimagedialog_data.value);
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
                    .then((response) => response.json())
                    .then(async (data) => {
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
                console.log("newPagination: ", newPagination.rowsPerPage);
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
                /* group: "description", */
                ghostClass: "ghost"
            };
        });

        /* function CheckLicense(callback) {
            console.log("CheckLicense");

            var body_data = JSON.stringify({
                request: "check",
                email: "imoufa@gmail.com",
                serial_key: "",
                product_id: 806
            });

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            var rdata_email = "imoufa@gmail.com";
            var rdata_serial_key = "123";
            var rdata_product_id = 806;

            let _APEX = window["APEX"];

            let finalurl =
                `${_REST_URL}/wp-json/aeroscroll/v1/manageserial?request=get`;

            fetch(finalurl, {
                method: "GET",
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
                    console.log("LICENSE: ", result);
                    callback(result);
                });
        } */

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
                        console.log("RSULT:", data);
                        if (data) {
                            if (data.success === true) {
                                console.log("exportfile: ", data.exportfile);
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

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/importimagegallery`;
            //let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/importimagegallery?imagegallery_id=` + editingItem.value.id;

            return finalurl;
        }

        function onUploaderFilesAdded(info) {
            console.log("onUploaderFilesAdded: ", info);

            $q.loading.show({
                message: t("uploadingimportfile")
            });
            GetUploaderHeaders();

            fileImporterElement.value.upload();


           /*  let _REST_URL = "http://localhost/";
            let _NONCENAME = "importimagegallery";

            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
                _NONCENAME = "importimagegallery";
            }

            // Update directly in DB
            let updateimagesurl = `${_REST_URL}/wp-json/aeroscroll/v1/importimagegallery`;

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
                        "X-WP-Nonce": _APEX[_NONCENAME].nonce
                    },
                    body: body_data
                })
                    .then((response) => response.json())
                    .then(async (data) => {
                        console.log("RSULT:", data);
                        if (data) {
                            if (data.success === true) {
                                console.log("exportfile: ", data.exportfile);
                                if (data.exportfile !== "") {
                                    document.location.href = data.exportfile;
                                }
                            }
                        }
                        $q.loading.hide();
                    });
            } */
        }

        function onUploadedImportGallery(info) {
            $q.loading.hide();
            console.log("onUploadedImportGallery: ", info);

            try {
                var ressp = JSON.parse(info.xhr.responseText);
                if (ressp.success === true) {
                    GetGalleryImages();
                }
            } catch (ex) {}
        }

        function onUploaderFilesFailed(info) {
            $q.loading.hide();

            console.log("onUploaderFilesFailed: ", info);

            $q.dialog({
                title: t("error"),
                message: t("erroruploadingfile")
            });
        }

        function onCloseManager() {
            $q.loading.hide();
        }

        function GetUploaderHeaders() {
            let _headers = [];

            let _APEX = window["APEX"];
            if (_APEX) {
                _headers = [
                    {
                        name: "X-WP-Nonce",
                        value: _APEX.importimagegallery.nonce//_APEX["importimagegallery"].nonce
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
            console.log("_headers: ", _headers);
            uploaderHeaders.value = _headers;
        }

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
                    }, _APEX.manageserial.nonce);
                } else {
                    // Else Get Galleries
                    GetGalleries();
                    licenseActive.value = true;
                }
            });

            categories_options.value = [];
            if ($q.sessionStorage.getItem("ig_table_rowsperpage")) {
                if ($q.sessionStorage.getItem("ig_table_rowsperpage") !== "undefined") {
                    initialPagination.value.rowsPerPage = $q.sessionStorage.getItem("ig_table_rowsperpage");
                }
            }
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
.fade-leave-to

/* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>
