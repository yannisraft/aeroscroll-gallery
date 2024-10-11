<template>
    <!-- notice dialogRef here -->
    <!-- <q-dialog v-model="dialogVisible" ref="dialogRef" @hide="onDialogHide" fullWidth fullHeight>
        <q-card class="q-dialog-plugin q-column" style="overflow: hidden;"> -->
    <transition name="fade">
        <div v-if="dialogVisible" class="q-custom-dialog q-column">
            <div class="q-custom-dialog-bg"></div>
            <q-card class="q-custom-dialog-card q-column" style="overflow: hidden;">
                <!-- <div class="q-column no-wrap self-stretch"> -->
                <div class="q-row" style="flex-grow: 1;">
                    <div class="q-row q-py-sm aeroscroll-page-header">
                        <span>{{ t("browseimages") }}</span>
                    </div>
                    <div class="q-col-12">
                        <q-banner inline-actions class="text-white bg-orange-10" v-if="GDLOADED_notification">
                            <q-icon name="auto_fix_high" size="11pt"></q-icon>Image Optimization feature is disabled. Please enable the PHP Extension
                            GD in your Web hosting panel.
                            <q-btn
                                flat
                                color="white"
                                label="Dismiss"
                                @click="GDLOADED_notification ? (GDLOADED_notification = false) : (GDLOADED_notification = true)"
                            />
                        </q-banner>
                    </div>
                    <div class="q-col-12 q-pt-md">
                        <!-- <q-table id="igtable" ref="igtableref" :pagination="initialPagination" grid class="aeroscroll-table-filemanager" :sort-method="SortItems" :rows="filemanager_rows" :columns="imagegallery_columns" row-key="id" hide-header> -->
                        <q-table
                            grid-header
                            id="igtable"
                            ref="igtableref"
                            :loading="filegridloading"
                            :pagination="initialPagination"
                            grid
                            class="aeroscroll-table-filemanager"
                            :visible-columns="['name', 'size', 'date']"
                            :sort-method="GridSorting"
                            :rows="filemanager_rows"
                            :columns="imagegallery_columns"
                            :filter="searchValue"
                            :filter-method="SearchMethod"
                            :rows-per-page-options="[10, 25, 50, 100, 200, 500]"
                            row-key="id"
                            hide-header
                        >
                            <template v-slot:loading>
                                <q-inner-loading showing color="primary" />
                            </template>
                            <template v-slot:top>
                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    <div style="display: flex;">
                                        <div style="flex-grow: 1;"></div>
                                        <!-- <div
                                            style="flex-basis: 200px; margin-right: 10px; align-items: center; justify-content: center; display: flex;">
                                            <q-btn :color="togglemodecolor" label="Select Images"
                                                @click="openMediaLibrary" size="md">
                                            </q-btn>
                                        </div>

                                         <div
                                            style="flex-basis: 200px; margin-right: 10px; align-items: center; justify-content: center; display: flex;">
                                            <q-btn :color="togglemodecolor" :label="t(filemanagermode) + ' Mode'"
                                                @click="ToggleMode" size="md">
                                                <q-tooltip>{{ t("fileexplorermode") }}</q-tooltip>
                                            </q-btn>
                                        </div> -->
                                        <div style="flex-basis: 60px; margin-right: 10px;">
                                            <q-btn color="primary" icon="arrow_upward" @click="UpFolderClicked" :disable="isRoot === true">
                                                <q-tooltip>{{ t("uponelevel") }}</q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div style="flex-basis: 60px; margin-right: 10px;">
                                            <q-btn
                                                color="primary"
                                                icon="edit"
                                                @click="RenameItemClicked"
                                                :disable="Object.keys(selectedItems).length === 0"
                                            >
                                                <q-tooltip>{{ t("rename") }}</q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div style="flex-basis: 60px; margin-right: 10px;">
                                            <q-btn
                                                color="primary"
                                                :disable="filemanagermode === 'mode_medialibrary'"
                                                icon="create_new_folder"
                                                @click="CreateFolderClicked"
                                            >
                                                <q-tooltip>{{ t("createfolder") }}</q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div style="flex-basis: 60px; margin-right: 10px;">
                                            <q-btn
                                                color="primary"
                                                icon="delete"
                                                @click="DeleteBtnClicked"
                                                :disable="Object.keys(selectedItems).length === 0"
                                            >
                                                <q-tooltip>{{ t("delete") }}</q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div style="flex-basis: 60px; margin-right: 10px;">
                                            <q-btn color="primary" icon="upload" @click="UploadFilesClicked">
                                                <q-tooltip>{{ t("uploadfiles") }}</q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div style="flex-basis: 60px; margin-right: 10px;">
                                            <q-btn
                                                color="primary"
                                                icon="auto_fix_high"
                                                @click="OptimizeAll"
                                                :disable="GDLOADED === false || ISNOTPRO"
                                            >
                                                <q-tooltip :offset="[10, -80]">{{ t("optimizeallinfolder") }}</q-tooltip>
                                                <q-tooltip v-if="ISNOTPRO" class="protooltip bg-blue-1" :offset="[10, 10]">
                                                    <q-icon size="sm" name="workspace_premium" class="q-mr-sm protooltip_icon" /><span
                                                        class="protooltip_label"
                                                        >{{ t("profeature") }}</span
                                                    >
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div style="flex-basis: 250px;">
                                            <q-input
                                                outlined
                                                dense
                                                debounce="300"
                                                class="q-py-sm"
                                                v-model="searchValue"
                                                :placeholder="t('search')"
                                                style="padding: unset; line-height: unset; min-height: unset;"
                                            >
                                                <template v-slot:append>
                                                    <q-icon name="search"></q-icon>
                                                </template>
                                            </q-input>
                                        </div>
                                    </div>
                                    <div class="aeroscroll-table-filemanager-path">
                                        <b>{{ t("path") }}:</b> <span>{{ currentRelativeURL }}</span>
                                    </div>
                                </div>
                            </template>
                            <template v-slot:item="props">
                                <div class="filemitem q-pa-sm q-col-xs-6 q-col-sm-3 q-col-md-2">
                                    <q-card class="fileitemcard" :id="props.row.id" :class="GetSelectedItemClass(props.row.id) ? 'q-card-focused' : ''">
                                        <div
                                            @click.stop="ClickedFileMItem(props.row)"
                                            @dblclick.stop="DoubleClickedFileMItem(props.row)"
                                            class="card-clickarea"
                                        ></div>
                                        <q-card-section class="flex flex-center unselectable">
                                            <q-img
                                                v-if="props.row.folder !== 1"
                                                class="unselectable"
                                                :src="props.row.image"
                                                loading="lazy"
                                                spinner-color="primary"
                                                height="100px"
                                                style="max-width: 150px;"
                                            />
                                            <q-icon
                                                class="folder-icon"
                                                v-if="props.row.folder === 1 && props.row.id !== 'uponefolder'"
                                                name="folder"
                                                color="primary"
                                                size="100px"
                                            />
                                            <q-icon
                                                class="folder-icon"
                                                v-if="props.row.id === 'uponefolder'"
                                                name="drive_folder_upload"
                                                color="primary"
                                                size="100px"
                                            />
                                        </q-card-section>
                                        <q-separator />
                                        <q-card-section class="filemitemlabel text-center unselectable">
                                            <span>{{ props.row.name }}</span>
                                        </q-card-section>
                                        <q-card-section class="filemitemsize text-center unselectable">
                                            <div v-if="props.row.id !== 'uponefolder' && !props.row.optimized">
                                                {{ CalculateSize(props.row.size) }}
                                            </div>
                                            <div class="optimized_size" v-if="props.row.id !== 'uponefolder' && props.row.optimized">
                                                <span class="non_optimized_size_text">{{ CalculateSize(props.row.size) }}</span
                                                ><span class="optimized_size_text">{{ CalculateSize(props.row.optimizedsize) }}</span>
                                            </div>
                                        </q-card-section>
                                        <q-card-section
                                            v-if="GDLOADED"
                                            class="text-center unselectable card-compressstatus-container"
                                            style="height: 48px;"
                                        >
                                            <q-chip
                                                :clickable="!props.row.optimized"
                                                v-if="props.row.folder !== 1 && GDLOADED"
                                                @click="OptimizeSingle(props.row)"
                                                class="card-compressstatus"
                                                text-color="white"
                                                :color="props.row.optimized ? 'green' : 'primary'"
                                                :style="props.row.optimized ? 'cursor: default;' : 'cursor: pointer;'"
                                                :disable="ISNOTPRO"
                                            >
                                                <q-icon
                                                    v-if="!props.row.optimizing"
                                                    :name="props.row.optimized ? 'done' : props.row.optimizing ? '' : 'auto_fix_high'"
                                                    color="white"
                                                    size="10pt"
                                                />
                                                <q-spinner v-if="props.row.optimizing" color="white" size="10pt" />
                                                <div class="optimizebtn_label">
                                                    {{ props.row.optimized ? t("optimized") : t("optimize") }}
                                                </div>
                                                <q-tooltip v-if="ISNOTPRO" class="protooltip bg-blue-1" :offset="[10, 10]">
                                                    <q-icon size="sm" name="workspace_premium" class="q-mr-sm protooltip_icon" /><span
                                                        class="protooltip_label"
                                                        >{{ t("profeature") }}</span
                                                    >
                                                </q-tooltip>
                                            </q-chip>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </template>
                            <template v-slot:pagination="scope">
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

                                <q-btn icon="chevron_left" color="grey-8" round dense flat :disable="scope.isFirstPage" @click="scope.prevPage" />

                                <q-btn icon="chevron_right" color="grey-8" round dense flat :disable="scope.isLastPage" @click="scope.nextPage" />

                                <q-btn
                                    v-if="scope.pagesNumber > 2"
                                    icon="last_page"
                                    color="grey-8"
                                    round
                                    dense
                                    flat
                                    :disable="scope.isLastPage"
                                    @click="scope.lastPage"
                                />
                            </template>
                        </q-table>
                    </div>
                    <div class="q-col-12">
                        <q-card-actions align="right">
                            <q-btn color="primary" :label="t('close')" @click="onCancelClick" />
                            <q-btn
                                v-if="singleselectionmode === false"
                                color="primary"
                                :label="t('add')"
                                @click="onAddImagesClicked"
                                :disable="Object.keys(selectedItems).length === 0"
                            />
                            <q-btn
                                v-if="singleselectionmode === true"
                                color="primary"
                                :label="t('select')"
                                @click="onSelectImageClicked"
                                :disable="Object.keys(selectedItems).length === 0"
                            />
                        </q-card-actions>
                    </div>
                </div>
                <div class="q-row"></div>

                <!-- Upload Dialog-->
                <q-dialog class="uploaddialog" v-model="uploadDialog" persistent transition-show="scale" transition-hide="scale">
                    <q-card style="padding: 10px; width: 50%;">
                        <div class="q-row" style="flex-grow: 1;">
                            <div class="q-col-12">
                                <q-uploader
                                    ref="imageuploaderRef"
                                    label="Upload Images"
                                    class="imageuploader full-width"
                                    :headers="uploaderHeaders"
                                    :url="GetUploaderURL()"
                                    accept=".jfif, .gif, .jpg, .jpeg, .png, .webp"
                                    color="primary"
                                    multiple
                                    batch
                                    flat
                                    bordered
                                    :filter="checkFileTypeImage"
                                    @uploaded="onUploadedImages"
                                    @added="onUploaderFilesAdded"
                                >
                                    <template v-slot:header="scope">
                                        <div class="q-row no-wrap items-center q-pa-sm q-gutter-xs">
                                            <q-btn
                                                v-if="scope.queuedFiles.length > 0"
                                                icon="clear_all"
                                                @click="scope.removeQueuedFiles"
                                                round
                                                dense
                                                flat
                                            >
                                                <q-tooltip>Clear All</q-tooltip>
                                            </q-btn>
                                            <q-btn
                                                v-if="scope.uploadedFiles.length > 0"
                                                icon="done_all"
                                                @click="scope.removeUploadedFiles"
                                                round
                                                dense
                                                flat
                                            >
                                                <q-tooltip>Remove Uploaded Files</q-tooltip>
                                            </q-btn>
                                            <q-spinner v-if="scope.isUploading" class="q-uploader__spinner" />
                                            <div class="q-col">
                                                <div class="q-uploader__title">Upload your files</div>
                                                <div class="q-uploader__subtitle">{{ scope.uploadSizeLabel }} / {{ scope.uploadProgressLabel }}</div>
                                            </div>
                                            <q-btn v-if="scope.canAddFiles" type="a" icon="add_box" @click="scope.pickFiles" round dense flat>
                                                <q-uploader-add-trigger />
                                                <q-tooltip>Pick Files</q-tooltip>
                                            </q-btn>
                                            <q-btn v-if="scope.canUpload" icon="cloud_upload" @click="scope.upload" round dense flat>
                                                <q-tooltip>Upload Files</q-tooltip>
                                            </q-btn>

                                            <q-btn v-if="scope.isUploading" icon="clear" @click="scope.abort" round dense flat>
                                                <q-tooltip>Abort Upload</q-tooltip>
                                            </q-btn>
                                        </div>
                                    </template>
                                    <template v-slot:list="scope">
                                        <q-list separator>
                                            <q-item v-for="file in scope.files" :key="file.__key">
                                                <q-item-section>
                                                    <q-item-label class="full-width ellipsis">
                                                        {{ file.name }}
                                                    </q-item-label>

                                                    <q-item-label caption> Status: {{ file.__status }} </q-item-label>

                                                    <q-item-label caption> {{ file.__sizeLabel }} / {{ file.__progressLabel }} </q-item-label>
                                                </q-item-section>

                                                <q-item-section v-if="file.__img" thumbnail class="gt-xs">
                                                    <img :src="file.__img.src" />
                                                </q-item-section>

                                                <q-item-section top side>
                                                    <q-btn class="gt-xs" size="12px" flat dense round icon="delete" @click="scope.removeFile(file)" />
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </template>
                                </q-uploader>
                            </div>
                            <div class="q-col-12">
                                <q-card-actions align="right">
                                    <q-btn color="primary" label="Cancel" @click="onCancelClickUploadDialog" />
                                    <q-btn color="primary" label="OK" @click="onOKClickUploadDialog" />
                                </q-card-actions>
                            </div>
                        </div>
                    </q-card>
                </q-dialog>
                <!-- / Upload Dialog-->

                <!-- Create Folder / Rename Dialog-->
                <q-dialog v-model="createRenameDialog" class="createfolderdialog" persistent transition-show="scale" transition-hide="scale">
                    <q-card style="min-width: 350px;">
                        <q-card-section>
                            <div class="text-h6">{{ dialogCreateRenameTitle }}</div>
                        </q-card-section>

                        <q-card-section class="q-pt-none">
                            <q-input
                                class="inputfield_createfolder"
                                ref="dialogCreateRenameInputRef"
                                dense
                                v-model="dialogCreateRenameValue"
                                :rules="nameRules"
                                autofocus
                                @keyup.enter="prompt = false"
                            />
                        </q-card-section>

                        <q-card-actions class="text-primary">
                            <q-btn flat :label="t('cancel')" @click="CreateRenameDialogCancel" />
                            <q-btn flat :label="dialogCreateRenameBtnLabel" @click="CreateRenameDialogSubmit" />
                        </q-card-actions>
                    </q-card>
                </q-dialog>

                <!-- Dialog for confirm delete image from Image Galleries -->
                <q-dialog v-model="dialogConfirmDeletion" persistent>
                    <q-card>
                        <q-card-section class="row items-center">
                            <div class="q-row" style="flex-grow: 1;">
                                <div class="q-col-2">
                                    <q-avatar icon="delete_sweep" color="primary" text-color="white" />
                                </div>
                                <div class="q-col-9">
                                    <span class="q-ml-sm">{{ dialogConfirmDeletion_message }}</span>
                                </div>
                            </div>
                        </q-card-section>

                        <q-card-actions>
                            <q-btn :label="t('cancel')" color="primary" v-close-popup />
                            <q-btn :label="t('delete_fromimagecollections_no')" color="primary" v-close-popup @click="DeleteBtnClicked_Event_Β;" />
                            <q-btn :label="t('delete_fromimagecollections_yes')" color="primary" v-close-popup @click="DeleteBtnClicked_Event_A" />
                        </q-card-actions>
                    </q-card>
                </q-dialog>

                <!-- Loader for Multiple Images Optimizations -->
                <!-- <q-linear-progress stripe rounded size="20px" :value="progress2" color="warning" class="q-mt-sm" /> -->
            </q-card>
        </div>
    </transition>

    <!-- </q-dialog> -->
</template>

<script>
import { ref, computed, defineComponent, onActivated, onMounted, watch, reactive } from "vue";

import { useQuasar, useDialogPluginComponent } from "quasar";

import SortingButton from "./SortingButton.vue";

import helpers from "./../Helpers/helpers";
import { useI18n } from "vue-i18n";

export default {
    props: {
        // ...your custom props
        modelValue: {
            type: Boolean,
            default: false
        },
        singleselectionmode: {
            type: Boolean,
            default: false
        },
        ISNOTPRO: {
            type: Boolean,
            default: true
        }
    },
    components: {
        SortingButton
    },

    emits: [["update:modelValue", "onImagesSelected", "onUpdate:modelValue", "onCloseManager"]],

    setup(props, context) {
        const $q = useQuasar();
        const CalculateSize = helpers.CalculateSize;
        const { t } = useI18n();
        let { locale } = useI18n({ useScope: "global" });

        // REQUIRED; must be called inside of setup()
        //const { dialogRef, onDialogCancel } = useDialogPluginComponent();
        // dialogRef      - Vue ref to be applied to QDialog
        // onDialogHide   - Function to be used as handler for @hide on QDialog
        // onDialogOK     - Function to call to settle dialog with "ok" outcome
        //                    example: onDialogOK() - no payload
        //                    example: onDialogOK({ /*.../* }) - with payload
        // onDialogCancel - Function to call to settle dialog with "cancel" outcome

        const initialPagination = {
            sortBy: "id",
            descending: false,
            page: 1,
            rowsPerPage: 50
            // rowsNumber: xx if getting data from a server
        };

        let dialogVisible = ref(false);
        let uploadDialog = ref(false);
        let createRenameDialog = ref(false);
        let filegridloading = ref(false);
        let searchValue = ref("");
        let selectedItems = ref({});
        let sorting_name = ref("asc");
        let sorting_size = ref("none");
        let sorting_date = ref("none");
        let uploaderHeaders = ref([]);
        let dialogCreateRenameTitle = ref("");
        let dialogCreateRenameValue = ref("");
        let dialogCreateRenameBtnLabel = ref("");
        let dialogCreateFolderMode = true;
        let currentListFolder = ref("root");
        let currentRelativeURL = ref("root");
        let isRoot = ref(false);
        let MEDIA_LIBRARY_MODE = ref(true);
        let filemanagermode = ref("mode_fileexplorer");
        let togglemodecolor = ref("orange-9");

        const dialogCreateRenameInputRef = ref(null);
        const imageuploaderRef = ref(null);
        const igtableref = ref(null);
        let uploaderTotalfiles = ref(0);
        let GDLOADED = ref(false);
        let GDLOADED_notification = ref(false);

        let dialogOptimizeAll = null;
        let dialogConfirmDeletion = ref(false);
        let dialogConfirmDeletion_message = ref("");
        let dialogConfirmDeletion_btn_a = ref("OK");
        let dialogConfirmDeletion_btn_b = ref("OK");

        let ctrlKeyDown = ref(false);
        let shiftKeyDown = ref(false);

        let nameRules = [
            (val) => (val !== null && val !== "") || "Please type a correct name",
            (val) => ValidateFilename(val) || 'Please enter a valid name (Excluding \\ \/ \: \* \? " \< \> \|)'
        ];

        const imagegallery_columns = [
            {
                name: "id",
                field: "id",
                required: false,
                label: "ID",
                align: "center",
                sortable: false
            },
            {
                name: "name",
                field: "name",
                required: true,
                label: "Name",
                align: "left",
                sortable: true
            },
            {
                name: "image",
                field: "image",
                required: false,
                label: "Image",
                align: "center",
                sortable: false
            },
            {
                name: "size",
                field: "size",
                label: "Size",
                align: "left",
                sortable: true
            },
            {
                name: "date",
                field: "date",
                label: "Date",
                align: "left",
                sortable: true
            }
        ];

        let filemanager_rows = ref([]);

        function ClickedFileMItem(item) {
            if (item.id !== "uponefolder") {
                if (ctrlKeyDown.value === false) {
                    if (shiftKeyDown.value === true && props.singleselectionmode !== true) {
                        var firstKey = Object.keys(selectedItems.value)[0];
                        var firstKeyInArray = igtableref.value.computedRows.findIndex((x) => x.id === firstKey);
                        var lastKeyInarray = igtableref.value.computedRows.findIndex((x) => x.id === item.id);

                        var newSelectedItems = [];
                        if (firstKeyInArray < lastKeyInarray) {
                            for (var k = firstKeyInArray + 1; k <= lastKeyInarray; k++) {
                                newSelectedItems.push(igtableref.value.computedRows[k]);
                            }
                        } else {
                            for (var f = lastKeyInarray; f <= firstKeyInArray - 1; f++) {
                                newSelectedItems.push(igtableref.value.computedRows[f]);
                            }
                        }

                        for (var g = 0; g < newSelectedItems.length; g++) {
                            var newSelectedItem = newSelectedItems[g];
                            selectedItems.value[newSelectedItem.name] = newSelectedItem;
                        }
                    } else {
                        if (!selectedItems.value[item.id]) {
                            selectedItems.value = {};
                            selectedItems.value[item.id] = item;
                        } else {
                            selectedItems.value = {};
                        }
                    }
                } else if (props.singleselectionmode !== true) {
                    if (!selectedItems.value[item.id]) {
                        selectedItems.value[item.id] = item;
                    } else {
                        delete selectedItems.value[item.id];
                    }
                }
            }
        }

        /* function ClickedFileMItem(item) {
            console.log("item CLICKED: ", item);

            if (item.id !== "uponefolder") {
                console.log("ctrlKeyDown: ", ctrlKeyDown.value);
                console.log("shiftKeyDown: ", shiftKeyDown.value);
                console.log("singleselectionmode: ", props.singleselectionmode);
                if (ctrlKeyDown.value === false) {
                    if (shiftKeyDown.value === true && props.singleselectionmode !== true) {
                        console.log("A");
                        var firstKey = Object.keys(selectedItems.value)[0];
                        var firstKeyInArray = igtableref.value.computedRows.findIndex((x) => x.id === firstKey);
                        var lastKeyInarray = igtableref.value.computedRows.findIndex((x) => x.id === item.id);

                        var newSelectedItems = [];
                        if (firstKeyInArray < lastKeyInarray) {
                            for (var k = firstKeyInArray + 1; k <= lastKeyInarray; k++) {
                                newSelectedItems.push(igtableref.value.computedRows[k]);
                            }
                        } else {
                            for (var f = lastKeyInarray; f <= firstKeyInArray - 1; f++) {
                                newSelectedItems.push(igtableref.value.computedRows[f]);
                            }
                        }

                        for (var g = 0; g < newSelectedItems.length; g++) {
                            var newSelectedItem = newSelectedItems[g];
                            selectedItems.value[newSelectedItem.name] = newSelectedItem;
                        }
                    } else {
                        console.log("C");
                        console.log("selectedItems.value: ", selectedItems.value);
                        if (!selectedItems.value[item.id]) {
                            selectedItems.value = {};
                            selectedItems.value[item.id] = item;
                        } else {
                            selectedItems.value = {};
                        }
                    }
                } else if (props.singleselectionmode !== true) {
                    console.log("B");
                    if (!selectedItems.value[item.id]) {
                        selectedItems.value[item.id] = item;
                    } else {
                        delete selectedItems.value[item.id];
                    }
                } else {
                    selectedItems.value = {};
                }
            }
        } */

        function DoubleClickedFileMItem(item) {
            if (item.folder === 1) {
                if (item.id === "uponefolder") {
                    selectedItems.value = {};
                    ListFolder("up");
                } else {
                    selectedItems.value = {};
                    //console.log("Double Clicked Folder: ", item);

                    ListFolder(item.name);
                }
            }
        }

        function ListFolder(target) {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            //console.log("_REST_URL: ", _REST_URL);

            selectedItems.value = {};

            var listfolder_params = {
                path: "root",
                current: currentListFolder.value,
                target: target,
                relativedir: currentRelativeURL.value,
                mode: filemanagermode.value
            };

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/listfolder`;

            filegridloading.value = true;

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX.listfolder.nonce
                    },
                    body: JSON.stringify(listfolder_params)
                })
                    .then(async (response) => {
                        var txt = await response.text();
                        //console.log("RESPONSE: ", JSON.parse(txt));
                        return JSON.parse(txt);

                        //return response.json();
                    })
                    .then((dataStr) => {
                        //dataStr = unescape(dataStr);

                        try {
                            filegridloading.value = false;

                            let jsdata = JSON.parse(dataStr);
                            //console.log("POST listfolder result: ", jsdata);

                            if (jsdata.list) {
                                filemanager_rows.value = [];
                                isRoot.value = false;

                                if (jsdata.path !== null && typeof jsdata.path !== "undefined") {
                                    currentRelativeURL.value = decodeURIComponent(jsdata.path);
                                }

                                if (jsdata.isroot !== null && typeof jsdata.isroot !== "undefined") {
                                    if (jsdata.isroot === 1) {
                                        isRoot.value = true;
                                    }
                                }

                                for (var k = 0; k < jsdata.list.length; k++) {
                                    var item = jsdata.list[k];
                                    if (item.folder === 1) {
                                        filemanager_rows.value.push({
                                            //id: item.id,  // REVIEW
                                            id: item.file,
                                            name: item.file,
                                            size: item.size,
                                            date: item.date,
                                            folder: 1,
                                            relativedir: currentRelativeURL.value
                                        });
                                    } else {
                                        //console.log(item);
                                        var img_url = "";
                                        if (item.ismedia) {
                                            if (item.ismedia === 1) {
                                                if (item.absoluteurl) {
                                                    img_url = item.absoluteurl;
                                                }
                                            } else {
                                                item.relativeurl = currentRelativeURL.value;
                                                img_url = `${_REST_URL}/` + currentRelativeURL.value + "/" + item.file;
                                            }
                                        } else {
                                            item.relativeurl = currentRelativeURL.value;
                                            img_url = `${_REST_URL}/` + currentRelativeURL.value + "/" + item.file;
                                        }

                                        filemanager_rows.value.push({
                                            //id: item.id, // REVIEW
                                            id: item.file,
                                            name: item.file,
                                            image: img_url,
                                            size: item.size,
                                            date: item.date,
                                            folder: 0,
                                            relativedir: item.relativeurl,
                                            optimized: item.optimized,
                                            optimizedsize: item.optimizedsize
                                        });
                                    }
                                }

                                if (!isRoot.value) {
                                    // Finally add UP folder
                                    filemanager_rows.value.unshift({
                                        id: "uponefolder",
                                        name: "",
                                        size: 0,
                                        date: 0,
                                        folder: 1
                                    });
                                }

                                //console.log("currentRelativeURL: ", currentRelativeURL.value);
                            }
                        } catch (ex) {
                            console.error(ex);
                        }
                    });
            }
        }

        function CreateFolderClicked() {
            //console.log('CreateFolderClicked');
            dialogCreateFolderMode = true;
            dialogCreateRenameTitle.value = "New Folder Name";
            dialogCreateRenameBtnLabel.value = "Create";
            dialogCreateRenameValue.value = "NewFolder";

            createRenameDialog.value = true;
        }

        function CreateRenameDialogSubmit() {
            if (!dialogCreateRenameInputRef.value.hasError) {
                // form has no error
                createRenameDialog.value = false;

                $q.loading.show();

                let _REST_URL = "http://localhost/";
                if (window["REST_URL"]) {
                    _REST_URL = window["REST_URL"].url;
                }

                if (dialogCreateFolderMode) {
                    // Create Folder Mode
                    let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/createfolder`;

                    let _APEX = window["APEX"];
                    if (_APEX) {
                        fetch(finalurl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                // eslint-disable-next-line
                                "X-WP-Nonce": _APEX.createfolder.nonce
                            },
                            body: JSON.stringify({
                                name: dialogCreateRenameValue.value,
                                relativedir: currentRelativeURL.value
                            })
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                //console.log("POST delete item result: ", data);

                                ListFolder("refresh");
                            });
                    }
                } else {
                    // Rename Item Mode
                    let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/renameitem`;

                    var selectedItemValues = selectedItems.value[Object.keys(selectedItems.value)[0]];
                    const oldname = selectedItemValues.name;

                    console.log("RENAME: ", {
                        id: selectedItemValues.id,
                        newname: dialogCreateRenameValue.value,
                        name: oldname,
                        relativedir: selectedItemValues.relativedir
                    });

                    let _APEX = window["APEX"];
                    if (_APEX) {
                        fetch(finalurl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                // eslint-disable-next-line
                                "X-WP-Nonce": _APEX.renameitem.nonce
                            },
                            body: JSON.stringify({
                                id: selectedItemValues.id,
                                newname: dialogCreateRenameValue.value,
                                name: oldname,
                                relativedir: selectedItemValues.relativedir
                            })
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                console.log("POST rename item result: ", data);

                                ListFolder("refresh");
                            });
                    }
                }

                selectedItems.value = {};
            }
        }

        function CreateRenameDialogCancel() {
            createRenameDialog.value = false;
        }

        function RenameItemClicked() {
            if (Object.keys(selectedItems.value).length > 1) {
                $q.dialog({
                    title: "Notification",
                    message: "Please select one item to rename"
                });
            } else {
                dialogCreateFolderMode = false;
                dialogCreateRenameTitle.value = "Item New Name";
                dialogCreateRenameBtnLabel.value = "Rename";

                //const fileid = Object.keys(selectedItems.value)[0];
                var selectedItemValues = selectedItems.value[Object.keys(selectedItems.value)[0]];
                console.log("selectedItemValues: ", selectedItemValues);

                dialogCreateRenameValue.value = selectedItemValues.name;

                createRenameDialog.value = true;
            }
        }

        function UpFolderClicked() {
            //console.log('UpFolderClicked');

            if (!isRoot.value) {
                selectedItems.value = {};
                ListFolder("up");
            }
        }

        // LINK Delete Button Clicked
        function DeleteBtnClicked() {
            //console.log('DeleteBtnClicked');
            var __continue = true;

            //console.log("Delete: ", selectedItems.value);

            if (Object.keys(selectedItems.value).length > 0) {
                let selectedItemsIDS = [];
                //for (var k = 0; k < selectedItems.value.length; k++) {
                for (var key in selectedItems.value) {
                    var _item = selectedItems.value[key];
                    if (_item.id !== "uponefolder") {
                        selectedItemsIDS.push({
                            name: _item.name,
                            folder: _item.folder
                        });
                    } else {
                        if (Object.keys(selectedItems.value).length === 1) __continue = false;
                    }
                }

                if (__continue) {
                    $q.dialog({
                        title: "Confirm",
                        message: "Are you sure you want to delete the specific Item/s?",
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
                            dialogConfirmDeletion.value = true;
                            dialogConfirmDeletion_message.value = t("delete_fromimagecollections");
                            dialogConfirmDeletion_btn_a.value = t("delete_fromimagecollections_yes");
                            dialogConfirmDeletion_btn_b.value = t("delete_fromimagecollections_no");
                        })
                        .onCancel(() => {
                            // console.log('>>>> Cancel')
                        })
                        .onDismiss(() => {
                            // console.log('I am triggered on both OK and Cancel')
                        });
                }
            }
        }

        function DeleteBtnClicked_Event_A() {
            console.log("DeleteBtnClicked_Event_A");
            DeleteCommit(true);
        }

        function DeleteBtnClicked_Event_Β() {
            console.log("DeleteBtnClicked_Event_B");
            DeleteCommit(false);
        }

        function DeleteCommit(_deleterelevant) {
            $q.loading.show();

            if (_deleterelevant !== true) {
                _deleterelevant = false;
            }

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let selectedItemsIDS = [];
            //for (var k = 0; k < selectedItems.value.length; k++) {
            for (var key in selectedItems.value) {
                var _item = selectedItems.value[key];
                console.log("D E L E T E: ", _item);
                if (_item.id !== "uponefolder") {
                    selectedItemsIDS.push({
                        name: _item.name,
                        folder: _item.folder
                    });
                } else {
                    if (Object.keys(selectedItems.value).length === 1) __continue = false;
                }
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/deleteitem`;

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX.deleteitem.nonce
                    },
                    body: JSON.stringify({
                        files: selectedItemsIDS,
                        relativedir: currentRelativeURL.value,
                        deleterelevant: _deleterelevant
                    })
                })
                    .then((response) => {
                        //console.log("On Response : ", response.text());
                        var return_response = response.json();
                        return return_response;
                    })
                    .then((data) => {
                        console.log("POST delete item result: ", data);

                        ListFolder("refresh");
                    });
            }
        }

        function UploadFilesClicked() {
            uploaderTotalfiles.value = 0;
            uploadDialog.value = true;
            //console.log('UploadFilesClicked');
        }

        function SortingBtnUpdated(name) {
            //console.log('SortingBtnUpdated: ', name);
            if (name === "Name") {
                sorting_size.value = "none";
                sorting_date.value = "none";
            } else if (name === "Size") {
                sorting_name.value = "none";
                sorting_date.value = "none";
            } else if (name === "Date") {
                sorting_name.value = "none";
                sorting_size.value = "none";
            }

            SortItems();
        }

        function onCancelClickUploadDialog() {
            uploadDialog.value = false;
        }

        function onOKClickUploadDialog() {
            if (uploaderTotalfiles.value > 0) {
                console.log("upload");
                imageuploaderRef.value.upload();

                //uploadDialog.value = false;
            } else {
                $q.dialog({
                    title: "Notification",
                    message: "Please select some files to upload"
                });
            }
        }

        function SortItems() {
            let sort_param = "name";
            let sort_flow = "desc";
            if (sorting_name.value !== "none") {
                sort_flow = sorting_name.value;
            } else if (sorting_size.value !== "none") {
                sort_param = "size";
                sort_flow = sorting_name.value;
            } else if (sorting_date.value !== "none") {
                sort_param = "date";
                sort_flow = sorting_name.value;
            }

            filemanager_rows.value.sort((a, b) => {
                const x = sort_flow === "desc" ? b : a;
                const y = sort_flow === "desc" ? a : b;

                if (sorting_name.value !== "none") {
                    return x[sort_param] > y[sort_param] ? 1 : x[sort_param] < y[sort_param] ? -1 : 0;
                } else {
                    return parseInt(x[sort_param]) - parseInt(y[sort_param]);
                }
            });

            let _tempobj = null;
            let _tempobj_index = -1;
            for (var f = 0; f < filemanager_rows.value.length; f++) {
                let _obj = filemanager_rows.value[f];
                if (_obj.id === "uponefolder") {
                    _tempobj = _obj;
                    _tempobj_index = f;
                }
            }

            if (_tempobj) {
                filemanager_rows.value.splice(_tempobj_index, 1);
            }

            //console.log("filemanager_rows: ", filemanager_rows.value);
        }

        function GridSorting(rows, sortBy, descending) {
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

            let _tempobj = null;
            let _tempobj_index = -1;
            for (var f = 0; f < data.length; f++) {
                let _obj = data[f];
                if (_obj.id === "uponefolder") {
                    _tempobj = _obj;
                    _tempobj_index = f;
                }
            }

            if (_tempobj) {
                data.splice(_tempobj_index, 1);
                data.splice(0, 0, _tempobj);
            }

            return data;
        }

        /* function CalculateSize(size) {
                // size is in bytes
                var sizekb = parseInt(size / 1000);
                var size_str = sizekb + " KB";
    
                if (sizekb > 1000) {
                    size_str = parseInt(sizekb / 1000) + " MB";
                }
    
                return size_str;
            } */

        /// Optimize
        // ----------------
        function OptimizeAll() {
            //
            //console.log("OptimizeAll: ", filemanager_rows.value);
            var images = [];
            for (var m = 0; m < filemanager_rows.value.length; m++) {
                var _item = filemanager_rows.value[m];
                if (_item.folder === 0) {
                    if (!_item.optimized) {
                        images.push({
                            name: _item.name
                        });
                    }
                }
            }

            if (images.length > 0) {
                OptimizeImages(images);
            }
        }

        function OptimizeSingle(image) {
            //console.log("OptimizeSingle");
            OptimizeImages([image]);
        }

        function OptimizeImages(images) {
            var __continue = true;

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }
            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/optimizeimages`;
            let _APEX = window["APEX"];

            let selectedItemsIDS = [];
            for (var k = 0; k < images.length; k++) {
                var _item = images[k];
                selectedItemsIDS.push({
                    name: _item.name
                });
            }

            var body_data = JSON.stringify({
                images: selectedItemsIDS,
                relativedir: currentRelativeURL.value
            });

            if (images.length > 0) {
                if (images.length > 1) {
                    $q.dialog({
                        title: "Confirm",
                        message: "Optimize all images in this folder?",
                        class: "qconfirmdialog",
                        cancel: {
                            push: true
                        },
                        ok: {
                            label: "Confirm",
                            push: true,
                            color: "positive"
                        },
                        persistent: true
                    })
                        .onOk(async () => {
                            dialogOptimizeAll = $q.dialog({
                                message: "Optimizing... 0%",
                                progress: true, // we enable default settings
                                persistent: true, // we want the user to not be able to close it
                                ok: false // we want the user to not be able to close it
                            });

                            /// TODO
                            if (_APEX) {
                                let percentage = 0;
                                let step = parseInt(100 / selectedItemsIDS.length);

                                for (var f = 0; f < selectedItemsIDS.length; f++) {
                                    body_data = JSON.stringify({
                                        images: [selectedItemsIDS[f]],
                                        relativedir: currentRelativeURL.value
                                    });

                                    const response = await fetch(finalurl, {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json",
                                            // eslint-disable-next-line
                                            "X-WP-Nonce": _APEX.optimizeimages.nonce
                                        },
                                        body: body_data
                                    });
                                    const data = await response.json();

                                    percentage += step;
                                    if (percentage > 100) percentage = 100;
                                    dialogOptimizeAll.update({
                                        message: `Uploading... ` + percentage + "%"
                                    });

                                    try {
                                        var resultd = JSON.parse(data);
                                        //console.log("POST optimize ALL resultd: ", resultd);
                                        if (resultd.result === 1) {
                                            // Notify Completed
                                        }
                                    } catch (ex) {
                                        console.error(ex);
                                    }

                                    /* .then((response) => response.json())
                                                      .then((data) => {
                                                          //console.log("POST optimize ALL images result: ", data);
                                                          //ListFolder("refresh");
                  
                                                      }); */
                                }

                                dialogOptimizeAll.update({
                                    message: "Optimizing... 100%"
                                });
                                setTimeout(() => {
                                    try {
                                        if (dialogOptimizeAll) dialogOptimizeAll.hide();
                                    } catch (ex) {}

                                    ListFolder("refresh");
                                }, 300);
                            }
                        })
                        .onCancel(() => {
                            // console.log('>>>> Cancel')
                        })
                        .onDismiss(() => {
                            // console.log('I am triggered on both OK and Cancel')
                        });
                } else {
                    if (_APEX) {
                        images[0].optimizing = true;
                        fetch(finalurl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                // eslint-disable-next-line
                                "X-WP-Nonce": _APEX.optimizeimages.nonce
                            },
                            body: body_data
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                //console.log("POST optimize images result: ", data);
                                images[0].optimizing = false;

                                try {
                                    var resultd = JSON.parse(data);
                                    //console.log("resultd: ", resultd);
                                    if (resultd.result === 1) {
                                        images[0].optimized = true;
                                    }
                                } catch (ex) {
                                    console.error(ex);
                                }

                                //ListFolder("refresh");
                            });
                    }
                }
            }
        }

        /// File Q-Uploader
        // ----------------

        function GetUploaderHeaders() {
            let _headers = [];

            let _APEX = window["APEX"];
            if (_APEX) {
                _headers = [
                    {
                        name: "X-WP-Nonce",
                        value: _APEX.uploadimages.nonce
                    },
                    {
                        name: "relativedir",
                        value: currentRelativeURL.value
                    }
                ];
            }
            //console.log("_headers: ", _headers);
            uploaderHeaders.value = _headers;
        }

        function GetUploaderURL() {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/uploadimages`;

            return finalurl;
        }

        function checkFileTypeImage(files) {
            return files.filter((file) => {
                //console.log("file.type: ", file.type);
                return (
                    (file.type === "image/png" ||
                        file.type === "image/jpg" ||
                        file.type === "image/jpeg" ||
                        file.type === "image/webp" ||
                        file.type === "image/jfif" ||
                        file.type === "image/gif") &&
                    ((file) => file.size < 15000)
                );
            });
        }

        function onUploaderFilesAdded(info) {
            uploaderTotalfiles.value = info.length;
            GetUploaderHeaders();
        }

        function onUploadedImages(info) {
            console.log("onUploadedImages: ", info);
            setTimeout(() => {
                uploadDialog.value = false;
                ListFolder("refresh");
            }, 1000);
        }

        function ValidateFilename(name) {
            var rg1 = /^[^\\/:\*\?"<>\|]+$/; // forbidden characters \ / : * ? " < > |
            var rg2 = /^\./; // cannot start with dot (.)
            var rg3 = /^(nul|prn|con|lpt[0-9]|com[0-9])(\.|$)/i; // forbidden file names

            return rg1.test(name) && !rg2.test(name) && !rg3.test(name);
        }

        function SearchMethod(rows, terms) {
            const lowerTerms = terms ? terms.toLowerCase() : "";
            const filteredRows = rows.filter((row) => {
                for (var k = 0; k < imagegallery_columns.length; k++) {
                    let col = imagegallery_columns[k];
                    if (row.id === "uponefolder") {
                        return true;
                    }
                    if ((row[col.name] + "").toLowerCase().includes(lowerTerms)) {
                        return true;
                    }
                }
            });
            return filteredRows;
        }

        function CancelFileManager() {
            uploadDialog.value = false;
        }

        function onCancelClick() {
            console.log(">>> onCancelClick");
            dialogVisible.value = false;
            context.emit("update:modelValue", false);
            context.emit("onCloseManager");
        }

        function ToggleMode() {
            if (filemanagermode.value === "mode_medialibrary") {
                filemanagermode.value = "mode_fileexplorer";
                togglemodecolor.value = "cyan-8";
            } else {
                filemanagermode.value = "mode_medialibrary";
                togglemodecolor.value = "orange-9";
            }

            ListFolder("root");
        }

        function GetSelectedItemClass(rowid) {
            var valid = false;
            if (selectedItems.value[rowid] !== null && typeof selectedItems.value[rowid] !== 'undefined') {
                valid = true;
            }

            return valid;
        }

        // ----------------
        // LINK onMounted
        onMounted(() => {
            ctrlKeyDown.value = false;
            shiftKeyDown.value = false;
            selectedItems.value = {};

            GetUploaderHeaders();

            //console.log("GD LOADED: ", window.gdloaded);
            if (window.gdloaded) {
                GDLOADED.value = window.gdloaded;
                GDLOADED_notification.value = false;
            } else {
                GDLOADED_notification.value = true;
            }

            window.addEventListener("keydown", (e) => {
                if (e.ctrlKey === true) {
                    ctrlKeyDown.value = true;
                }

                if (e.shiftKey === true) {
                    shiftKeyDown.value = true;
                }
            });

            window.addEventListener("keyup", (e) => {
                ctrlKeyDown.value = false;
                shiftKeyDown.value = false;
            });
        });

        watch(
            () => props.modelValue,
            (value) => {
                //console.log("watch: ", value);

                if (value === true) {
                    setTimeout(() => {
                        // List Folder
                        ListFolder("root");

                        var filegridbg = document.querySelector(".aeroscroll-table-filemanager .q-table__grid-content");
                        if (filegridbg) {
                            filegridbg.addEventListener("click", (e) => {
                                selectedItems.value = {};
                                //console.log("clicked: ", e.target);
                            });
                        }
                    }, 500);
                }
                dialogVisible.value = value;
            }
        );

        return {
            // This is REQUIRED;
            // Need to inject these (from useDialogPluginComponent() call)
            // into the vue scope for the vue html template
            //dialogRef,

            // other methods that we used in our vue html template;
            // these are part of our example (so not required)
            onAddImagesClicked() {
                uploadDialog.value = false;

                context.emit("onImagesSelected", selectedItems.value);
                context.emit("update:modelValue", false);
            },

            onSelectImageClicked() {
                uploadDialog.value = false;

                context.emit("onImagesSelected", selectedItems.value);
                context.emit("update:modelValue", false);
            },

            // we can passthrough onDialogCancel directly
            //onCancelClick: onDialogCancel,

            ClickedFileMItem,
            t,
            isRoot,
            togglemodecolor,
            filemanagermode,
            MEDIA_LIBRARY_MODE,
            GDLOADED,
            GDLOADED_notification,
            filegridloading,
            currentListFolder,
            sorting_name,
            sorting_size,
            sorting_date,
            dialogVisible,
            uploadDialog,
            currentRelativeURL,
            createRenameDialog,
            dialogCreateRenameTitle,
            dialogCreateRenameValue,
            dialogCreateRenameBtnLabel,
            selectedItems,
            searchValue,
            imagegallery_columns,
            filemanager_rows,
            initialPagination,
            nameRules,
            dialogCreateRenameInputRef,
            igtableref,
            imageuploaderRef,
            uploaderTotalfiles,
            ctrlKeyDown,
            shiftKeyDown,
            dialogConfirmDeletion,
            dialogConfirmDeletion_message,

            onCancelClick,
            UpFolderClicked,
            DeleteBtnClicked,
            CreateFolderClicked,
            CreateRenameDialogSubmit,
            RenameItemClicked,
            SortingBtnUpdated,
            SortItems,
            GridSorting,
            uploaderHeaders,
            GetUploaderURL,
            UploadFilesClicked,
            onCancelClickUploadDialog,
            onOKClickUploadDialog,
            checkFileTypeImage,
            onUploadedImages,
            //CalculateSize,
            ListFolder,
            onUploaderFilesAdded,
            DoubleClickedFileMItem,
            OptimizeAll,
            OptimizeSingle,
            CalculateSize,
            SearchMethod,
            CancelFileManager,
            CreateRenameDialogCancel,
            ToggleMode,
            DeleteBtnClicked_Event_A,
            DeleteBtnClicked_Event_Β,
            GetSelectedItemClass
        };
    }
};
</script>

<style lang="scss">
.q-field__native {
    padding: unset;
    line-height: unset;
    min-height: unset;
    border: 0px solid;
}
</style>
