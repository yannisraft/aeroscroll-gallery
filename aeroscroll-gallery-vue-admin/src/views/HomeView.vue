<template>
    <div class="home">
        <div class="q-fit q-column q-no-wrap q-justify-start q-items-stretch q-content-start">
            <q-card flat bordered class="admin-card" style="position: relative;">
                <div class="q-row q-py-sm aeroscroll-page-header">
                    <span>{{ t("manage_galleries") }}</span>
                </div>
                <div v-if="!editMode" class="q-row q-pb-sm">   
                    <div v-if="loading" class="panelloader">
                        <q-spinner
                            color="white"
                            size="3em"
                        />
                        <span style="color: white; margin-top: 10px;">{{ t('loading') + '...' }}</span>
                    </div>         
                    <div v-if="!licenseActive" class="license_inactive_panel">
                        <span v-if="licenseNotActive">{{ t("please_activate_license") }}</span>
                    </div>
                    <q-table
                        style="width: 100%;"
                        :loading="loading"
                        separator="cell"
                        :rows="tablerows"
                        :columns="tablecolumns"
                        :filter="searchValue"
                        :filter-method="SearchMethod"
                        row-key="id"
                        :pagination="initialPagination"
                        @update:pagination="PaginationUpdated"
                    >
                        <template v-slot:loading>
                            <!-- <q-inner-loading :label="t('loading') + '...'" showing color="white">
                                <q-spinner-gears size="50px" color="primary" />
                            </q-inner-loading> -->
                        </template>
                        <template v-slot:top-right>
                            <div class="q-col q-px-sm" style="flex: 0;">
                                <q-btn color="primary" :label="t('create')" @click="CreateGridClicked"></q-btn>
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
                        <template v-slot:body-cell-shortcode="props">
                            <q-td :props="props">
                                <div @click="ClickedShortCode(props.row)" class="shortcode_container">[aeroscroll_{{ props.row.shortcode }}]</div>
                                <div v-if="props.row.shortcode_clicked" class="copiedbox">Copied!</div>
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
                                    <q-btn color="primary" icon="edit" @click="EditGridClicked(props.row)" dense></q-btn>
                                    <q-btn
                                        color="primary"
                                        icon="delete"
                                        @click.stop="DeleteGridClicked(props.row)"
                                        dense
                                        style="margin-left: 10px;"
                                    ></q-btn>
                                </div>
                            </q-td>
                        </template>
                    </q-table>
                </div>

                <div v-if="editMode" class="q-row q-column q-no-wrap justify-start items-stretch content-start q-pa-md aeroscroll-tab-content">
                    <div class="q-row q-py-md">
                        <h6 class="aeroscroll-subheader" style="margin: 0px;">{{ t("editgallery") }}</h6>
                    </div>
                    <q-tabs v-model="settingstab" no-caps align="left" class="q-pl-md galleries_tabs">
                        <q-tab name="general" icon="settings" :label="t('general')" />
                        <q-tab name="theme" icon="images" :label="t('theme')" />
                        <q-tab name="dimensions" icon="photo_size_select_small" :label="t('dimensions')" />
                        <q-tab name="socialshare" icon="share" :label="t('socialshare')" />
                        <q-tab name="watermark" icon="branding_watermark" :label="t('watermark')" />
                    </q-tabs>
                    <q-tab-panels v-model="settingstab" animated>
                        <!-- LINK TAB GENERAL-->
                        <q-tab-panel name="general">
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">ID</div>
                                    <div class="text-caption">{{ t("id_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined v-model="editingItem.id" dense readonly></q-input>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("title") }}</div>
                                    <div class="text-caption">{{ t("nameofgrid_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined v-model="editingItem.title" dense></q-input>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("shortcode") }}</div>
                                    <div class="text-caption">{{ t("shortcode_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined v-model="editingItem.shortcode" dense prefix="aeroscroll_"></q-input>
                                </div>
                            </div>
                            <div
                                class="q-row q-py-md"
                                :style="{
                                    backgroundColor: ispro ? '' : '#f4f9ff'
                                }"
                            >
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("orientation") }}</div>
                                    <div class="text-caption">{{ t("orientation_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        :disable="!ispro"
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.orientation"
                                        :options="[
                                            { label: 'Vertical', value: 'vertical' },
                                            { label: 'Horizontal', value: 'horizontal' }
                                        ]"
                                        dense
                                        outlined
                                    />
                                    <div v-if="!ispro" class="protext">
                                        <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="workspace_premium" />
                                        {{ t("profeature") }}
                                    </div>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("layout") }}</div>
                                    <div class="text-caption">{{ t("layout_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        option-value="id"
                                        option-label="title"
                                        emit-value
                                        map-options
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.layout"
                                        :options="layout_options"
                                        dense
                                        outlined
                                        options-html
                                        @update:model-value="layoutValueSelected"
                                    >
                                        <template v-slot:option="scope">
                                            <q-item
                                                v-bind="scope.itemProps"
                                                :style="{
                                                    backgroundColor: !ispro && scope.opt.id === 'masonry' ? '#f4f9ff' : '#fff'
                                                }"
                                            >
                                                <q-item-section avatar>
                                                    <q-img :src="scope.opt.image" spinner-color="white" style="height: 100px; width: 100px;" />
                                                </q-item-section>
                                                <q-item-section>
                                                    <q-item-label
                                                        :style="{
                                                            color: !ispro && scope.opt.id === 'masonry' ? '#ffa552' : 'initial'
                                                        }"
                                                        >{{ scope.opt.title }}</q-item-label
                                                    >
                                                    <div v-if="(!ispro && scope.opt.id === 'masonry')" class="protext">
                                                        <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="workspace_premium" />
                                                        {{ t("profeature") }}
                                                    </div>
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                        <template v-slot:selected-item="scope">
                                            <q-item v-bind="scope.itemProps">
                                                <q-item-section avatar>
                                                    <q-img :src="scope.opt.image" spinner-color="white" style="height: 100px; width: 100px;" />
                                                </q-item-section>
                                                <q-item-section>
                                                    <q-item-label>{{ scope.opt.title }}</q-item-label>
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-select>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("type") }}</div>
                                    <div class="text-caption">{{ t("type_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        option-value="value"
                                        option-label="label"
                                        emit-value
                                        map-options
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.type"
                                        :options="type_options"
                                        dense
                                        outlined
                                        options-html
                                    ></q-select>
                                </div>
                            </div>
                            <div v-if="editingItem.type === 'posts'" class="q-row q-py-sm" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("select_categories") }}</div>
                                    <div class="text-caption">{{ t("select_categories_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.categories"
                                        :options="categories_options"
                                        filled
                                        multiple
                                        use-chips
                                        dense
                                        outlined
                                    ></q-select>
                                </div>
                            </div>

                            <div v-if="editingItem.type === 'imagegallery'" class="q-row q-py-sm">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("select_imagegallery") }}</div>
                                    <div class="text-caption">{{ t("select_imagegallery_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.imagegallery"
                                        :options="imagegalleries_options"
                                        filled
                                        dense
                                        outlined
                                    >
                                        <template v-slot:no-option>
                                            <q-item>
                                                <q-item-section class="text-italic text-grey">
                                                    {{ t("no_imagegalleries_exist") }}
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-select>
                                </div>
                            </div>

                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("select_orderby") }}</div>
                                    <div class="text-caption">{{ t("select_orderby_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        v-if="editingItem.type === 'posts'"
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.sortby"
                                        :options="sortby_options_posts"
                                        filled
                                        dense
                                        outlined
                                    >
                                        <template v-slot:no-option>
                                            <q-item>
                                                <q-item-section class="text-italic text-grey">
                                                    No Options
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-select>
                                    <q-select
                                        v-if="editingItem.type === 'imagegallery'"
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.sortby"
                                        :options="sortby_options_imagegal"
                                        filled
                                        dense
                                        outlined
                                    >
                                        <template v-slot:no-option>
                                            <q-item>
                                                <q-item-section class="text-italic text-grey">
                                                    No Options
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-select>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("select_orderby_dir") }}</div>
                                    <div class="text-caption">{{ t("select_orderby_dir_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.sortbydir"
                                        :options="sortbydir_options"
                                        filled
                                        dense
                                        outlined
                                    >
                                        <template v-slot:no-option>
                                            <q-item>
                                                <q-item-section class="text-italic text-grey">
                                                    No Options
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-select>
                                </div>
                            </div>
                            <div v-if="editingItem.type === 'posts'" class="q-row q-py-sm" 
                            :style="{
                                    backgroundColor: ispro ? '' : '#f4f9ff',
                                    'align-items': 'center'
                                }">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("showarticleonlightbox") }}</div>
                                    <div class="text-caption">{{ t("showarticleonlightbox_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle :disable="!ispro" v-model="editingItem.articleinlightbox"></q-toggle>
                                    <div v-if="!ispro" class="protext">
                                        <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="workspace_premium" />
                                        {{ t("profeature") }}
                                    </div>
                                </div>
                            </div>
                            <div v-if="editingItem.type === 'posts'" class="q-row q-py-sm" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("readmorebtn") }}</div>
                                    <div class="text-caption">{{ t("readmorebtn_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle v-model="editingItem.showreadmore"></q-toggle>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("hasscrollbar") }}</div>
                                    <div class="text-caption">{{ t("hasscrollbar_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle v-model="editingItem.scrollbar"></q-toggle>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("usemousewheel") }}</div>
                                    <div class="text-caption">{{ t("usemousewheel_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle v-model="editingItem.usemousewheel"></q-toggle>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("scrollspeed") }}</div>
                                    <div class="text-caption">{{ t("scrollspeed_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" outlined type="number" filled v-model="editingItem.scrollspeed" dense></q-input>
                                </div>
                            </div>
                            <div
                                class="q-row q-py-md"
                                :style="{
                                    backgroundColor: ispro ? '' : '#f4f9ff'
                                }"
                            >
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("autoscroll") }}</div>
                                    <div class="text-caption">{{ t("autoscroll_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle :disable="!ispro" v-model="editingItem.autoscroll"></q-toggle>
                                    <div v-if="!ispro" class="protext">
                                        <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="workspace_premium" />
                                        {{ t("profeature") }}
                                    </div>
                                </div>
                            </div>
                            <!-- <div
                                :class="['q-row', 'q-py-md', { creditsection: !ispro }]"
                                :style="{
                                    backgroundColor: '#fff2db'
                                }"
                            >
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("poweredbyption") }}</div>
                                    <div class="text-caption">{{ t("poweredbyption_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle
                                        @update:model-value="
                                            (value) => {
                                                PoweredByClicked(value);
                                            }
                                        "
                                        v-model="editingItem.poweredbyactive"
                                    ></q-toggle>
                                    <div class="creditpremiumsupport">
                                        <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="emoji_events" /><span
                                            class="poweredbyption_desc2"
                                            >{{ t("poweredbyption_desc2") }}</span
                                        ><br /><span class="poweredbyption_desc3">{{ t("poweredbyption_desc3") }}</span>
                                    </div>
                                    <div v-if="showregisterbutton" class="creditpremiumsupport">
                                        <q-icon size="sm" style="padding-right: 10px; color: #459eeb;" name="how_to_reg" /><span
                                            class="register_desc"
                                            >{{ t("please_signup_tocontinue") }}</span
                                        ><br />
                                        <q-btn color="primary" style="margin-top: 10px;" :label="t('signup')" @click="RegisterClicked"></q-btn>
                                    </div>
                                </div>
                            </div> -->
                            <div class="q-row q-py-md items-center">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("published") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle v-model="editingItem.published"></q-toggle>
                                </div>
                            </div>
                        </q-tab-panel>
                        <!-- LINK TAB THEME-->
                        <q-tab-panel name="theme">
                            <div class="q-row q-py-md">
                                <div class="q-col-2 text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("theme") }}</div>
                                    <div class="text-caption">{{ t("theme_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-select
                                        option-value="id"
                                        option-label="name"
                                        emit-value
                                        map-options
                                        class="condensed-width text-capitalize"
                                        v-model="editingItem.theme"
                                        :options="theme_options"
                                        dense
                                        outlined
                                    ></q-select>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("themeonhover") }}</div>
                                    <div class="text-caption">{{ t("themeonhover_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <q-toggle v-model="editingItem.themeonhover"></q-toggle>
                                </div>
                            </div>

                            <div class="q-row q-py-md" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("background_color") }}</div>
                                    <div class="text-caption">{{ t("background_color_desc") }}</div>
                                </div>
                                <div class="q-col-2">
                                    <q-input filled v-model="editingItem.color_bg" class="my-input" dense>
                                        <template v-slot:append>
                                            <q-icon name="q-colorize" class="cursor-pointer aeroscroll-colorpick-icon">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-color v-model="editingItem.color_bg" default-value="#285de0" />
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                        <template v-slot:prepend>
                                            <div class="q-colorfield_preview" :style="{ 'background-color': editingItem.color_bg }"></div>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="q-col-2" style="padding: 10px;">
                                    <q-btn color="primary" :label="t('resetcolor')" @click="ResetColor('bg')"></q-btn>
                                </div>
                            </div>

                            <div class="q-row q-py-md" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("theme_color") }}</div>
                                    <div class="text-caption">{{ t("theme_color_desc") }}</div>
                                </div>
                                <div class="q-col-2">
                                    <q-input filled v-model="editingItem.color_theme_a" class="my-input" dense>
                                        <template v-slot:append>
                                            <q-icon name="q-colorize" class="cursor-pointer aeroscroll-colorpick-icon">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-color v-model="editingItem.color_theme_a" default-value="#285de0" />
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                        <template v-slot:prepend>
                                            <div class="q-colorfield_preview" :style="{ 'background-color': editingItem.color_theme_a }"></div>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="q-col-2" style="padding: 10px;">
                                    <q-btn color="primary" :label="t('resetcolor')" @click="ResetColor('theme_a')"></q-btn>
                                </div>
                            </div>

                            <div class="q-row q-py-md" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("theme_title_color") }}</div>
                                    <div class="text-caption">{{ t("theme_title_color_desc") }}</div>
                                </div>
                                <div class="q-col-2">
                                    <q-input filled v-model="editingItem.color_theme_title" class="my-input" dense>
                                        <template v-slot:append>
                                            <q-icon name="q-colorize" class="cursor-pointer aeroscroll-colorpick-icon">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-color v-model="editingItem.color_theme_title" default-value="#285de0" />
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                        <template v-slot:prepend>
                                            <div class="q-colorfield_preview" :style="{ 'background-color': editingItem.color_theme_title }"></div>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="q-col-2" style="padding: 10px;">
                                    <q-btn color="primary" :label="t('resetcolor')" @click="ResetColor('theme_title')"></q-btn>
                                </div>
                            </div>

                            <div class="q-row q-py-md" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("theme_desc_color") }}</div>
                                    <div class="text-caption">{{ t("theme_desc_color_desc") }}</div>
                                </div>
                                <div class="q-col-2">
                                    <q-input filled v-model="editingItem.color_theme_desc" class="my-input" dense>
                                        <template v-slot:append>
                                            <q-icon name="q-colorize" class="cursor-pointer aeroscroll-colorpick-icon">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-color v-model="editingItem.color_theme_desc" default-value="#285de0" />
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                        <template v-slot:prepend>
                                            <div class="q-colorfield_preview" :style="{ 'background-color': editingItem.color_theme_desc }"></div>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="q-col-2" style="padding: 10px;">
                                    <q-btn color="primary" :label="t('resetcolor')" @click="ResetColor('theme_desc')"></q-btn>
                                </div>
                            </div>

                            <div class="q-row q-py-md" style="align-items: center;">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("theme_loadingcell_bgcolor") }}</div>
                                    <div class="text-caption">{{ t("theme_loadingcell_bgcolor_desc") }}</div>
                                </div>
                                <div class="q-col-2">
                                    <q-input filled v-model="editingItem.color_cell_bg" class="my-input" dense>
                                        <template v-slot:append>
                                            <q-icon name="q-colorize" class="cursor-pointer aeroscroll-colorpick-icon">
                                                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                    <q-color v-model="editingItem.color_cell_bg" default-value="#285de0" />
                                                </q-popup-proxy>
                                            </q-icon>
                                        </template>
                                        <template v-slot:prepend>
                                            <div class="q-colorfield_preview" :style="{ 'background-color': editingItem.color_cell_bg }"></div>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="q-col-2" style="padding: 10px;">
                                    <q-btn color="primary" :label="t('resetcolor')" @click="ResetColor('cell_bg')"></q-btn>
                                </div>
                            </div>
                        </q-tab-panel>
                        <!-- LINK TAB DIMENSIONS-->
                        <q-tab-panel name="dimensions">
                            <!-- ON FUTURE Release -->

                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("numofrowsorcols") }}</div>
                                    <div class="text-caption">{{ t("numofrowsorcols_desc") }}</div>
                                </div>
                                <div class="q-col">
                                    <div class="q-row q-py-md justify-center" style="font-size: 13pt; font-weight: 400;">
                                        {{ $t("columnsbyscreenwidth_desc") }}
                                    </div>
                                    <!-- Breakpoints for Responsive Web Design in 2023 -->
                                    <div class="q-row q-py-md">
                                        <div class="q-col screensizes_col q-mr-md">
                                            <span class="screensizes_col_label_left">240 px</span>
                                            <span class="screensizes_col_label_right">480 px</span>
                                        </div>
                                        <div class="q-col screensizes_col q-mr-md">
                                            <span class="screensizes_col_label_left">480 px</span>
                                            <span class="screensizes_col_label_right">1024 px</span>
                                        </div>
                                        <div class="q-col screensizes_col">
                                            <span class="screensizes_col_label_left">1024 px</span>
                                            <span class="screensizes_col_label_right">1980+ px</span>
                                        </div>
                                    </div>
                                    <div class="q-row q-py-md">
                                        <div class="q-col q-pr-md">
                                            <q-input
                                                class="condensed-width"
                                                type="number"
                                                filled
                                                outlined
                                                v-model="editingItem.numcolumns_low"
                                                dense
                                            ></q-input>
                                        </div>
                                        <div class="q-col q-pr-md">
                                            <q-input
                                                class="condensed-width"
                                                type="number"
                                                filled
                                                outlined
                                                v-model="editingItem.numcolumns_mid"
                                                dense
                                            ></q-input>
                                        </div>
                                        <div class="q-col">
                                            <q-input
                                                class="condensed-width"
                                                type="number"
                                                filled
                                                outlined
                                                v-model="editingItem.numcolumns"
                                                dense
                                            ></q-input>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("height") }}</div>
                                    <div class="text-caption">
                                        {{ t("height_desc") }}
                                    </div>
                                </div>
                                <div class="q-col">
                                    <div style="display: flex;">
                                        <q-input class="condensed-width" filled outlined v-model="editingItem.height" dense />
                                        <q-select
                                            class="condensed-width"
                                            style="margin-left: 10px;"
                                            v-model="editingItem.heightunit"
                                            :options="['px', '%']"
                                            dense
                                        ></q-select>
                                    </div>
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("cellsize") }}</div>
                                    <div class="text-caption">
                                        {{ t("cellsize_desc") }}
                                    </div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" type="number" filled outlined v-model="editingItem.cellsize" dense />
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("sidegapsize") }}</div>
                                    <div class="text-caption">
                                        {{ t("sidegapsize_desc") }}
                                    </div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" type="number" filled outlined v-model="editingItem.sidegap" dense />
                                </div>
                            </div>
                            <div class="q-row q-py-md">
                                <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                    <div class="text-field-header">{{ t("cellgapsize") }}</div>
                                    <div class="text-caption">
                                        {{ t("cellgapsize_desc") }}
                                    </div>
                                </div>
                                <div class="q-col">
                                    <q-input class="condensed-width" type="number" filled outlined v-model="editingItem.cellgap" dense />
                                </div>
                            </div>
                        </q-tab-panel>
                        <!-- LINK TAB SOCIAL-->
                        <q-tab-panel name="socialshare">
                            <div v-if="!ispro">
                                <div class="q-row q-py-md">
                                    <div class="q-col">
                                        <q-card flat bordered :style="GetProFeaturesStyle()">
                                            <q-card-section>
                                                <div class="text-h5 q-py-md" style="color: #fff; font-size: 1.7rem;">
                                                    <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="workspace_premium" />
                                                    {{ t("premiumfeaturetitle1") }}
                                                </div>
                                            </q-card-section>

                                            <q-separator inset />

                                            <q-card-section>
                                                <div class="premiumdescstyle" v-html="t('premiumfeaturedesc')"></div>
                                            </q-card-section>
                                            <q-card-section>
                                                <q-btn color="blue-10" text-color="white" @click="UpgradeToProClicked()">
                                                    {{ t("upgradetopro") }}
                                                </q-btn>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                            </div>
                            <div v-if="ispro">
                                <div class="q-row q-py-md">
                                    <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                        <div class="text-field-header">{{ t("sharefacebookbutton") }}</div>
                                        <div class="text-caption">{{ t("sharefacebookbutton_desc") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-toggle :true-value="1" v-model="editingItem.social_share_facebook"></q-toggle>
                                    </div>
                                </div>
                                <div class="q-row q-py-md">
                                    <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                        <div class="text-field-header">{{ t("sharetwitterbutton") }}</div>
                                        <div class="text-caption">{{ t("sharetwitterbutton_desc") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-toggle :true-value="1" v-model="editingItem.social_share_twitter"></q-toggle>
                                    </div>
                                </div>
                                <div class="q-row q-py-md">
                                    <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                        <div class="text-field-header">{{ t("sharepinterestbutton") }}</div>
                                        <div class="text-caption">{{ t("sharepinterestbutton_desc") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-toggle :true-value="1" v-model="editingItem.social_share_pinterest"></q-toggle>
                                    </div>
                                </div>
                                <div class="q-row q-py-md">
                                    <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                        <div class="text-field-header">{{ t("sharetumblrbutton") }}</div>
                                        <div class="text-caption">{{ t("sharetumblrbutton_desc") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-toggle :true-value="1" v-model="editingItem.social_share_tumblr"></q-toggle>
                                    </div>
                                </div>
                                <div class="q-row q-py-md">
                                    <div class="q-col text-left q-px-sm aeroscroll-edit-label-col">
                                        <div class="text-field-header">{{ t("shareemailbutton") }}</div>
                                        <div class="text-caption">{{ t("shareemailbutton_desc") }}</div>
                                    </div>
                                    <div class="q-col">
                                        <q-toggle :true-value="1" v-model="editingItem.social_share_email"></q-toggle>
                                    </div>
                                </div>
                            </div>
                        </q-tab-panel>
                        <!-- LINK TAB WATERMARK-->
                        <q-tab-panel name="watermark">
                            <div v-if="!ispro">
                                <div class="q-row q-py-md">
                                    <div class="q-col">
                                        <q-card flat bordered :style="GetProFeaturesStyle()">
                                            <q-card-section>
                                                <div class="text-h5 q-py-md" style="color: #fff; font-size: 1.7rem;">
                                                    <q-icon size="sm" style="padding-right: 10px; color: #ffb46f;" name="workspace_premium" />
                                                    {{ t("premiumfeaturetitle2") }}
                                                </div>
                                            </q-card-section>

                                            <q-separator inset />

                                            <q-card-section>
                                                <div class="premiumdescstyle" v-html="t('premiumfeaturedesc')"></div>
                                            </q-card-section>
                                            <q-card-section>
                                                <q-btn color="blue-10" text-color="white" @click="UpgradeToProClicked()">
                                                    {{ t("upgradetopro") }}
                                                </q-btn>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </div>
                            </div>
                            <div v-if="ispro">
                                <div class="q-row">
                                    <div class="q-col-7">
                                        <div class="q-row q-py-md">
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermarktype") }}</div>
                                                <div class="text-caption">{{ t("watermarktype_desc") }}</div>
                                            </div>
                                            <div class="q-col-7">
                                                <q-select
                                                    map-options
                                                    class="condensed-width text-capitalize"
                                                    v-model="editingItem.watermark_type"
                                                    :options="watermarktype_options"
                                                    dense
                                                    outlined
                                                ></q-select>
                                            </div>
                                        </div>
                                        <div v-if="editingItem.watermark_type.value === 1" class="q-row q-py-md">
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_text_title") }}</div>
                                                <div class="text-caption">{{ t("watermark_text_desc") }}</div>
                                            </div>
                                            <div class="q-col-7">
                                                <q-input class="condensed-width" outlined v-model="editingItem.watermark_text" dense></q-input>
                                            </div>
                                        </div>
                                        <div v-if="editingItem.watermark_type.value === 1" class="q-row q-py-md">
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_fontsize_title") }}</div>
                                                <div class="text-caption">
                                                    {{ t("watermark_fontsize_desc") }}
                                                </div>
                                            </div>
                                            <div class="q-col-7">
                                                <q-input
                                                    class="condensed-width"
                                                    type="number"
                                                    filled
                                                    outlined
                                                    v-model="editingItem.watermark_fontsize"
                                                    dense
                                                />
                                            </div>
                                        </div>
                                        <div v-if="editingItem.watermark_type.value === 1" class="q-row q-py-md" style="align-items: center;">
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_color_title") }}</div>
                                                <div class="text-caption">{{ t("watermark_color_desc") }}</div>
                                            </div>
                                            <div class="q-col">
                                                <q-input filled v-model="editingItem.watermark_color" class="my-input" dense>
                                                    <template v-slot:append>
                                                        <q-icon name="q-colorize" class="cursor-pointer aeroscroll-colorpick-icon">
                                                            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                                                <q-color v-model="editingItem.watermark_color" default-value="#285de0" />
                                                            </q-popup-proxy>
                                                        </q-icon>
                                                    </template>
                                                    <template v-slot:prepend>
                                                        <div
                                                            class="q-colorfield_preview"
                                                            :style="{ 'background-color': editingItem.watermark_color }"
                                                        ></div>
                                                    </template>
                                                </q-input>
                                            </div>
                                            <div class="q-col-2" style="padding-left: 10px;">
                                                <q-btn color="primary" :label="t('resetcolor')" @click="ResetColor('watermark_color')"></q-btn>
                                            </div>
                                        </div>
                                        <div v-if="editingItem.watermark_type.value === 2" class="q-row q-py-md">
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_image_url") }}</div>
                                                <div class="text-caption">
                                                    {{ t("watermark_image_url_desc") }}
                                                </div>
                                            </div>
                                            <div class="q-col-7">
                                                <div class="q-row q-py-md">
                                                    <div class="q-col">
                                                        <q-input
                                                            class="condensed-width"
                                                            filled
                                                            outlined
                                                            v-model="editingItem.watermark_image_url"
                                                            dense
                                                            :label="t('imageurlorbrowse')"
                                                        />
                                                    </div>
                                                    <div class="q-col-4" style="padding-left: 10px;">
                                                        <q-btn color="primary" :label="t('browse')" @click="BrowseImage()" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="editingItem.watermark_type.value === 1 || editingItem.watermark_type.value === 2"
                                            class="q-row q-py-md"
                                        >
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_opacity_title") }}</div>
                                                <div class="text-caption">
                                                    {{ t("watermark_opacity_desc") }}
                                                </div>
                                            </div>
                                            <div class="q-col-7">
                                                <q-slider label-always v-model="editingItem.watermark_opacity" :min="0" :max="100" />
                                            </div>
                                        </div>
                                        <div
                                            v-if="editingItem.watermark_type.value === 1 || editingItem.watermark_type.value === 2"
                                            class="q-row q-py-md"
                                        >
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_position_title") }}</div>
                                                <div class="text-caption">
                                                    {{ t("watermark_position_desc") }}
                                                </div>
                                            </div>
                                            <div class="q-col-7" style="display: flex; justify-content: center;">
                                                <PositionToggle v-model="editingItem.watermark_position" />
                                            </div>
                                        </div>
                                        <div v-if="editingItem.watermark_type.value === 2" class="q-row q-py-md">
                                            <div class="q-col-5 text-left q-px-sm aeroscroll-edit-label-col">
                                                <div class="text-field-header">{{ t("watermark_imagesize_title") }}</div>
                                                <div class="text-caption">
                                                    {{ t("watermark_imagesize_desc") }}
                                                </div>
                                            </div>
                                            <div class="q-col-7">
                                                <q-slider label-always v-model="editingItem.watermark_image_size" :min="0" :max="100" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="q-col-5 items-center justify-center" style="display: flex;">
                                        <q-img
                                            v-if="editingItem.watermark_type.value === 1 || editingItem.watermark_type.value === 2"
                                            :src="watermark_bgimage"
                                            spinner-color="white"
                                            style="height: 400px; max-width: 400px; position: relative; container-type: size;"
                                        >
                                            <div
                                                v-if="editingItem.watermark_type.value === 1"
                                                class="watermark_overlay_text"
                                                :style="{
                                                    color: editingItem.watermark_color,
                                                    'font-size': editingItem.watermark_fontsize + 'cqw',
                                                    'line-height': editingItem.watermark_fontsize + 'cqw',
                                                    opacity: editingItem.watermark_opacity / 100,
                                                    top: GetWaterMarkPosition('top'),
                                                    left: GetWaterMarkPosition('left'),
                                                    bottom: GetWaterMarkPosition('bottom'),
                                                    right: GetWaterMarkPosition('right'),
                                                    transform: GetWaterMarkTranslate()
                                                }"
                                            >
                                                {{ editingItem.watermark_text }}
                                            </div>
                                            <div
                                                v-if="editingItem.watermark_type.value === 2"
                                                class="watermark_overlay_image"
                                                id="watermark_overlay_image"
                                                :style="{
                                                    color: editingItem.watermark_color,
                                                    'font-size': editingItem.watermark_fontsize + 'pt',
                                                    opacity: editingItem.watermark_opacity / 100,
                                                    top: GetWaterMarkPosition('top'),
                                                    left: GetWaterMarkPosition('left'),
                                                    bottom: GetWaterMarkPosition('bottom'),
                                                    right: GetWaterMarkPosition('right'),
                                                    transform: GetWaterMarkTranslate(),
                                                    width: editingItem.watermark_image_size + '%',
                                                    height: editingItem.watermark_image_size + '%'
                                                }"
                                            >
                                                <q-img :src="editingItem.watermark_image_url" spinner-color="white" width="100%" height="100%" />
                                            </div>
                                        </q-img>
                                    </div>
                                </div>
                            </div>
                        </q-tab-panel>
                        <!-- LINK TAB ADVERTISEMENT-->
                        <q-tab-panel name="advertisement"> </q-tab-panel>
                    </q-tab-panels>

                    <!-- Notification -->
                    <div v-if="notification_success" class="q-row q-py-sm">
                        <q-banner dense inline-actions class="text-white bg-green">
                            {{ t("changessavedsuccessfully") }}
                            <template v-slot:action>
                                <q-btn flat color="white" label="Dismiss" />
                            </template>
                        </q-banner>
                    </div>

                    <div class="q-row q-py-sm">
                        <div class="q-col"></div>
                        <div class="q-col text-right"></div>
                        <div class="q-col text-right q-mx-sm items-center justify-end">
                            <q-btn color="primary" :label="t('cancel')" @click="CancelEditingClicked"></q-btn>
                            <q-btn class="q-ml-md" color="primary" :label="t('save')" @click="SaveEditingClicked"></q-btn>
                        </div>
                    </div>
                    <FileManager v-model="filemanager_visible" :singleselectionmode="true" @onImagesSelected="OnImagesSelected" />
                </div>
            </q-card>
        </div>
    </div>
</template>

<script>
import { ref, defineComponent, onActivated, onMounted, onBeforeMount, computed } from "vue";

import { useQuasar } from "quasar";
import { useI18n } from "vue-i18n";

import PositionToggle from "./../components/PositionToggle.vue";
import FileManager from "./../components/FileManager.vue";

var IS_PRO = window["IS_PRO"].is_pro.toLowerCase() === "true" ? true : false;

export default defineComponent({
    name: "HomeView",
    components: {
        PositionToggle,
        FileManager
    },
    setup(props) {
        const $q = useQuasar();
        const { t } = useI18n();
        //const i18nLocale = useI18n();
        let { locale } = useI18n({ useScope: "global" });

        let searchField = ref("vertical");
        let searchValue = ref("");
        let editMode = ref(false);
        let notification_success = ref(false);
        let settingstab = ref("general");
        let justLoaded = true;
        let licenseActive = ref(false);
        let licenseNotActive = ref(false);
        let filemanager_visible = ref(false);
        let showregisterbutton = ref(false);
        let ispro = ref(false);        
        ispro.value = IS_PRO;
        let watermark_bgimage = ref(window["MEDIA_URL"] + "images/aero-horse.jpg");

        let sortby_options_posts = ref([
            {
                value: "id",
                label: "ID"
            },
            {
                value: "title",
                label: t("title")
            },
            {
                value: "author",
                label: t("author")
            },
            {
                value: "rand",
                label: t("rand")
            },
            {
                value: "date",
                label: t("date")
            }
        ]);
        let sortby_options_imagegal = ref([
            {
                value: "id",
                label: "ID"
            },
            {
                value: "image_name",
                label: t("imagename")
            },
            {
                value: "title",
                label: t("title")
            },
            {
                value: "created_at",
                label: t("date")
            }
        ]);

        let sortbydir_options = ref([
            {
                value: "ASC",
                label: t("ascending")
            },
            {
                value: "DESC",
                label: t("descending")
            }
        ]);

        let editingItem = ref({
            published: true,
            id: "Created on Save",
            cellsize: 200,
            cellsquared: false,
            created_at: "",
            isinfinite: true,
            themeonhover: true,
            showreadmore: true,
            articleinlightbox: false,
            height: "400px",
            layout: "grid",
            type: "posts",
            theme: "theme_a",
            numcolumns: 3,
            numcolumns_mid: 2,
            numcolumns_low: 1,
            numrows: 2,
            sidegap: 0,
            cellgap: 0,
            marginX: 0,
            marginY: 0,
            usemousewheel: true,
            orientation: "vertical",
            scrollbar: true,
            scrollspeed: 6,
            shortcode: "test1",
            title: "New Grid",
            updated_at: "",
            categories: [],
            color_bg: "#ffffff00",
            color_theme_a: "#2d2f31ff",
            color_theme_title: "#ffffffff",
            color_theme_desc: "#bfbfbfff",
            color_cell_bg: "#dbdfe5ff",
            sortby: MatchValue("id", sortby_options_imagegal.value),
            sortbydir: MatchValue("ASC", sortbydir_options.value),
            social_share_facebook: 0,
            social_share_twitter: 0,
            social_share_pinterest: 0,
            social_share_instagram: 0,
            social_share_tumblr: 0,
            social_share_email: 0,
            watermark_type: 0,
            watermark_text: "",
            watermark_fontsize: 16,
            watermark_color: "",
            watermark_opacity: 30,
            watermark_position: 7,
            watermark_image_url: "",
            watermark_image_size: 30,
            advertisment_type: 0,
            advertisment_text: "",
            advertisment_link: "",
            advertisment_fontsize: 16,
            advertisment_color: "",
            advertisment_opacity: 30,
            advertisment_position: 7,
            advertisment_image_url: "",
            advertisment_image_size: 30
        });
        let createMode = ref(false);

        let categories_options = ref([]);
        let imagegalleries_options = ref([]);

        let loading = ref(false);

        let tablerows = ref([]);

        let layout_options = ref([
            {
                id: "grid",
                image: window["MEDIA_URL"] + "images/aeroscroll_layout_icon_grid.png",
                title: t("layout_grid")
            },
            {
                id: "justified",
                image: window["MEDIA_URL"] + "images/aeroscroll_layout_icon_justified.png",
                title: t("layout_justified")
            },
            {
                id: "masonry",
                image: window["MEDIA_URL"] + "images/aeroscroll_layout_icon_masonry.png",
                title: t("layout_masonry")
            }
        ]);

        let type_options = ref([
            {
                value: "posts",
                label:
                    '<div class="aeroscroll-layout-option-box"><div class="aeroscroll-layout-option-image"><img src="' +
                    window["MEDIA_URL"] +
                    'images/aeroscroll_type_icon_post.png" alt="Posts Type"/></div><div class="aeroscroll-layout-option-text"><span>Posts Data</span></div></div>'
            },
            {
                value: "imagegallery",
                label:
                    '<div class="aeroscroll-layout-option-box"><div class="aeroscroll-layout-option-image"><img src="' +
                    window["MEDIA_URL"] +
                    'images/aeroscroll_type_icon_imagegallery.png" alt="Image Gallery Type"/></div><div class="aeroscroll-layout-option-text"><span>Image Gallery Data</span></div></div>'
            }
        ]);

        let theme_options = ref([
            { id: "theme_a", name: "Theme A" },
            { id: "theme_b", name: "Theme B" },
            { id: "theme_c", name: "Theme C" },
            { id: "theme_d", name: "Theme D" },
            { id: "theme_e", name: "Theme E" },
            { id: "theme_f", name: "Theme F" }
            /* { id: "theme_g", name: "Theme G" } */
        ]);

        let watermarktype_options = ref([
            { value: 0, label: t("none") },
            { value: 1, label: t("text") },
            { value: 2, label: t("image") }
        ]);

        let advertisementtype_options = ref([
            { id: 0, name: "None" },
            { id: 1, name: "Text" },
            { id: 2, name: "Image" }
        ]);

        let watermark_position_options = ref([
            { value: 1, label: t("topleft") },
            { value: 2, label: t("topcenter") },
            { value: 3, label: t("topright") },
            { value: 4, label: t("middleleft") },
            { value: 5, label: t("middlecenter") },
            { value: 6, label: t("middleright") },
            { value: 7, label: t("bottomleft") },
            { value: 8, label: t("bottomcenter") },
            { value: 9, label: t("bottomright") }
        ]);

        let initialPagination = ref({
            sortBy: "DESC",
            descending: false,
            page: 1,
            rowsPerPage: 25
        });

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
                    name: "shortcode",
                    field: "shortcode",
                    required: true,
                    label: t("shortcode"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "orientation",
                    field: "orientation",
                    required: true,
                    label: t("orientation"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "theme",
                    field: "theme",
                    required: true,
                    label: t("theme"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "created_at",
                    field: "created_at",
                    required: true,
                    label: t("creation_date"),
                    align: "left",
                    sortable: true
                },
                {
                    name: "updated_at",
                    field: "updated_at",
                    required: true,
                    label: t("update_date"),
                    align: "left",
                    sortable: true
                }
            ];
        });

        const DeleteGridClicked = (val) => {
            $q.dialog({
                title: "Confirm",
                message: "Are you sure you want to delete the specific Grid?",
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

                    let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/deletegrid`;
                    let _APEX = window["APEX"];
                    if (_APEX) {
                        fetch(finalurl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                // eslint-disable-next-line
                                "X-WP-Nonce": _APEX.deletegrid.nonce
                            },
                            body: JSON.stringify(val)
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                $q.loading.hide();
                                GetGrids();
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

        const EditGridClicked = (val) => {
            if (licenseActive.value === true) {
                editMode.value = true;
                createMode.value = false;
                editingItem.value = JSON.parse(JSON.stringify(val));

                let imagegallery_exists = false;
                for (var f = 0; f < imagegalleries_options.value.length; f++) {
                    var item = imagegalleries_options.value[f];
                    if (editingItem.value.imagegallery_id) {
                        if (item.value === parseInt(editingItem.value.imagegallery_id)) {
                            imagegallery_exists = true;
                            editingItem.value.imagegallery = {
                                value: item.value,
                                label: item.label
                            };

                            break;
                        }
                    }
                }

                if (!imagegallery_exists) {
                    delete editingItem.value.imagegallery;
                }

                if (editingItem.value.height) {
                    var numericHeight = editingItem.value.height.match(/\d/g);
                    numericHeight = numericHeight.join("");
                    var unitHeight = editingItem.value.height.replace(numericHeight, "");

                    try {
                        editingItem.value.height = Number(numericHeight);
                    } catch (ex) {}
                    editingItem.value.heightunit = unitHeight;
                }

                if (editingItem.value.type === "imagegallery") {
                    editingItem.value.sortby = MatchValue(editingItem.value.sortby, sortby_options_imagegal.value);
                } else {
                    editingItem.value.sortby = MatchValue(editingItem.value.sortby, sortby_options_posts.value);
                }

                editingItem.value.sortbydir = MatchValue(editingItem.value.sortbydir, sortbydir_options.value);

                editingItem.value.social_share_facebook === "1"
                    ? (editingItem.value.social_share_facebook = 1)
                    : (editingItem.value.social_share_facebook = 0);
                editingItem.value.social_share_twitter === "1"
                    ? (editingItem.value.social_share_twitter = 1)
                    : (editingItem.value.social_share_twitter = 0);
                editingItem.value.social_share_pinterest === "1"
                    ? (editingItem.value.social_share_pinterest = 1)
                    : (editingItem.value.social_share_pinterest = 0);
                editingItem.value.social_share_instagram === "1"
                    ? (editingItem.value.social_share_instagram = 1)
                    : (editingItem.value.social_share_instagram = 0);
                editingItem.value.social_share_tumblr === "1"
                    ? (editingItem.value.social_share_tumblr = 1)
                    : (editingItem.value.social_share_tumblr = 0);
                editingItem.value.social_share_email === "1"
                    ? (editingItem.value.social_share_email = 1)
                    : (editingItem.value.social_share_email = 0);

                // Watermark TYPE
                switch (editingItem.value.watermark_type) {
                    case "1":
                        editingItem.value.watermark_type = watermarktype_options.value[1];
                        break;
                    case "2":
                        editingItem.value.watermark_type = watermarktype_options.value[2];
                        break;
                    default:
                        editingItem.value.watermark_type = watermarktype_options.value[0];
                        break;
                }

                editingItem.value.watermark_fontsize = parseInt(editingItem.value.watermark_fontsize);
                editingItem.value.watermark_image_size = parseInt(editingItem.value.watermark_image_size);
                editingItem.value.watermark_opacity = parseInt(editingItem.value.watermark_opacity);
                editingItem.value.watermark_position = parseInt(editingItem.value.watermark_position);

                // Advertisement TYPE
                switch (editingItem.value.advertisment_type) {
                    case 1:
                        editingItem.value.advertisment_type = advertisementtype_options.value[1];
                        break;
                    case 2:
                        editingItem.value.advertisment_type = advertisementtype_options.value[2];
                        break;
                    default:
                        editingItem.value.advertisment_type = advertisementtype_options.value[0];
                        break;
                }
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
        };

        const SaveEditingClicked = () => {
            $q.loading.show();

            try {
                let _REST_URL = "http://localhost/";
                let _NONCENAME = "updategrid";
                if (window["REST_URL"]) {
                    _REST_URL = window["REST_URL"].url;
                    _NONCENAME = "addgrid";
                }

                let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/updategrid`;
                if (createMode.value) {
                    finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/addgrid`;
                }

                if (typeof editingItem.value.layout === "object") {
                    editingItem.value.layout = editingItem.value.layout.id;
                }

                if (typeof editingItem.value.sortby === "object") {
                    editingItem.value.sortby = editingItem.value.sortby.value;
                }

                if (typeof editingItem.value.sortbydir === "object") {
                    editingItem.value.sortbydir = editingItem.value.sortbydir.value;
                }

                if (typeof editingItem.value.theme === "object") {
                    editingItem.value.theme = editingItem.value.theme.id;
                }

                if (typeof editingItem.value.orientation === "object") {
                    editingItem.value.orientation = editingItem.value.orientation.value;
                }

                if (typeof editingItem.value.type === "object") {
                    editingItem.value.type = editingItem.value.type.value;
                }

                if (typeof editingItem.value.imagegallery === "object") {
                    editingItem.value.imagegallery_id = parseInt(editingItem.value.imagegallery.value);
                }

                if (typeof editingItem.value.watermark_type === "object") {
                    editingItem.value.watermark_type = parseInt(editingItem.value.watermark_type.value);
                }

                var height_numeric = editingItem.value.height;
                var height_unit = editingItem.value.heightunit;

                if (typeof height_unit === "undefined") {
                    height_unit = "";
                }

                editingItem.value.height = String(height_numeric) + height_unit;
                if (editingItem.value.categories) {
                    var _final = [];
                    if (editingItem.value.categories.length > 0) {
                        for (var k = 0; k < editingItem.value.categories.length; k++) {
                            _final.push({
                                id: editingItem.value.categories[k].value,
                                title: editingItem.value.categories[k].label
                            });
                        }
                    }

                    editingItem.value.categories = _final;
                }

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
                        .then((response) => {
                            var return_response = response.json();
                            return return_response;
                        })
                        .then((data) => {
                            editMode.value = false;
                            $q.loading.hide();
                            GetGrids();
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            } catch (ex) {
                console.log(ex);
            }
        };

        const CreateGridClicked = () => {
            if (licenseActive.value === true) {
                createMode.value = true;
                editingItem.value = {
                    published: true,
                    id: "Created on Save",
                    imagegallery_id: -1,
                    cellsize: 200,
                    cellsquared: false,
                    created_at: "",
                    isinfinite: true,
                    themeonhover: true,
                    showreadmore: true,
                    articleinlightbox: false,
                    height: "400px",
                    layout: "grid",
                    type: "posts",
                    theme: "theme_a",
                    numcolumns: 3,
                    numcolumns_mid: 2,
                    numcolumns_low: 1,
                    numrows: 2,
                    sidegap: 0,
                    cellgap: 0,
                    marginX: 0,
                    marginY: 0,
                    usemousewheel: true,
                    orientation: "vertical",
                    scrollbar: true,
                    scrollspeed: 6,
                    shortcode: "test1",
                    title: "New Grid",
                    updated_at: "",
                    categories: [],
                    color_bg: "#ffffff00",
                    color_theme_a: "#2d2f31ff",
                    color_theme_title: "#ffffffff",
                    color_theme_desc: "#bfbfbfff",
                    color_cell_bg: "#dbdfe5ff",
                    sortby: MatchValue("id", sortby_options_imagegal.value),
                    sortbydir: MatchValue("ASC", sortbydir_options.value),
                    social_share_facebook: 0,
                    social_share_twitter: 0,
                    social_share_pinterest: 0,
                    social_share_instagram: 0,
                    social_share_tumblr: 0,
                    social_share_email: 0,
                    watermark_type: 0,
                    watermark_text: "",
                    watermark_fontsize: 16,
                    watermark_color: "",
                    watermark_opacity: 30,
                    watermark_position: 7,
                    watermark_image_url: "",
                    watermark_image_size: 30,
                    advertisment_type: 0,
                    advertisment_text: "",
                    advertisment_link: "",
                    advertisment_fontsize: 16,
                    advertisment_color: "",
                    advertisment_opacity: 30,
                    advertisment_position: 7,
                    advertisment_image_url: "",
                    advertisment_image_size: 30,
                    poweredbyactive: 0
                };
                editMode.value = true;
            }
        };

        function MatchValue(val, _options) {
            var found = val;
            for (var k = 0; k < _options.length; k++) {
                if (_options[k].value === val) {
                    found = _options[k];
                    break;
                }
            }
            return found;
        }

        function GetGrids() {
            loading.value = true;

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }

            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/getgrids`;

            let _APEX = window["APEX"];
            if (_APEX) {
                fetch(finalurl, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        // eslint-disable-next-line
                        "X-WP-Nonce": _APEX.getgrids.nonce
                    }
                })
                    .then((response) => {
                        return response.json();
                    })
                    .then((result) => {
                        tablerows.value = [];

                        let data = result.grids;
                        let allcategories = result.allcategories;

                        // Set All categories
                        if (allcategories) {
                            var fcats = [];
                            Object.keys(allcategories).forEach((key) => {
                                fcats.push({
                                    value: allcategories[key].term_id,
                                    label: allcategories[key].name
                                });
                            });

                            categories_options.value = fcats;

                            // THEN Get the Image Galleries
                            // ----------------------------
                            GetGalleries(() => {
                                tablerows.value = data;
                                loading.value = false;
                            });
                        }

                        data.forEach((element) => {
                            element["usemousewheel"] === 1 || element["usemousewheel"] === "1"
                                ? (element["usemousewheel"] = true)
                                : (element["usemousewheel"] = false);
                            element["themeonhover"] === 1 || element["themeonhover"] === "1"
                                ? (element["themeonhover"] = true)
                                : (element["themeonhover"] = false);
                            element["showreadmore"] === 1 || element["showreadmore"] === "1"
                                ? (element["showreadmore"] = true)
                                : (element["showreadmore"] = false);
                            element["articleinlightbox"] === 1 || element["articleinlightbox"] === "1"
                                ? (element["articleinlightbox"] = true)
                                : (element["articleinlightbox"] = false);
                            element["autoscroll"] === 1 || element["autoscroll"] === "1"
                                ? (element["autoscroll"] = true)
                                : (element["autoscroll"] = false);
                            element["isinfinite"] === 1 || element["isinfinite"] === "1"
                                ? (element["isinfinite"] = true)
                                : (element["isinfinite"] = false);
                            element["cellsquared"] === 1 || element["cellsquared"] === "1"
                                ? (element["cellsquared"] = true)
                                : (element["cellsquared"] = false);
                            element["scrollbar"] === 1 || element["scrollbar"] === "1"
                                ? (element["scrollbar"] = true)
                                : (element["scrollbar"] = false);
                            element["published"] === 1 || element["published"] === "1"
                                ? (element["published"] = true)
                                : (element["published"] = false);
                            element["poweredbyactive"] === 1 || element["poweredbyactive"] === "1"
                                ? (element["poweredbyactive"] = true)
                                : (element["poweredbyactive"] = false);

                            if (element["categories"].length > 0) {
                                var _cats = element["categories"];
                                var _final = [];
                                for (var k = 0; k < _cats.length; k++) {
                                    let _cat = _cats[k];
                                    _final.push({
                                        label: _cat["title"],
                                        value: _cat["id"]
                                    });
                                }

                                element["categories"] = _final;
                            }
                        });
                    });
            }
        }

        function GetGalleries(callback) {
            imagegalleries_options.value = [];

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

                        for (var k = 0; k < data.length; k++) {
                            imagegalleries_options.value.push({
                                value: parseInt(data[k].id),
                                label: data[k].title
                            });
                        }

                        callback();
                    });
            }
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

        function RefreshTable() {
            if (licenseActive.value === true) {
                GetGrids();
            }
        }

        function ClickedShortCode(row) {
            row.shortcode_clicked = true;

            setTimeout(() => {
                row.shortcode_clicked = false;
            }, 2000);

            navigator.clipboard.writeText("[aeroscroll_" + row.shortcode + "]");
        }

        function ResetColor(target) {
            if (target === "bg") {
                editingItem.value.color_bg = "#ffffff00";
            } else if (target === "theme_a") {
                editingItem.value.color_theme_a = "#2d2f31ff";
            } else if (target === "theme_title") {
                editingItem.value.color_theme_title = "#ffffffff";
            } else if (target === "theme_desc") {
                editingItem.value.color_theme_desc = "#bfbfbfff";
            } else if (target === "cell_bg") {
                editingItem.value.color_cell_bg = "#dbdfe5ff";
            } else if (target === "watermark_color") {
                editingItem.value.watermark_color = "#000000";
            }
        }

        function PaginationUpdated(newPagination) {
            if (newPagination && !justLoaded) {
                $q.sessionStorage.set("ig_table_rowsperpage", newPagination.rowsPerPage);
                initialPagination.value.rowsPerPage = newPagination.rowsPerPage;
            }
        }

        function GetWaterMarkPosition(_pos) {
            var _final = "0px";
            if (editingItem.value.watermark_position > 6) {
                if (_pos === "top") _final = "unset";
                if (_pos === "bottom") _final = "0px";
            } else if (editingItem.value.watermark_position > 3) {
                if (_pos === "top") _final = "50%";
                if (_pos === "bottom") _final = "unset";
            } else {
                if (_pos === "top") _final = "0px";
                if (_pos === "bottom") _final = "unset";
            }

            if (editingItem.value.watermark_position % 3 === 1) {
                if (_pos === "left") _final = "0px";
                if (_pos === "right") _final = "unset";
            } else if (editingItem.value.watermark_position % 3 === 2) {
                if (_pos === "left") _final = "50%";
                if (_pos === "right") _final = "unset";
            } else {
                if (_pos === "left") _final = "unset";
                if (_pos === "right") _final = "0px";
            }

            return _final;
        }

        function GetWaterMarkTranslate() {
            var _X = 0;
            var _Y = 0;

            if (editingItem.value.watermark_position % 3 === 2) {
                _X = -50;
            }

            if (
                editingItem.value.watermark_position === 5 ||
                editingItem.value.watermark_position === 6 ||
                editingItem.value.watermark_position === 4
            ) {
                _Y = -50;
            }

            var _final = "translate(" + _X + "%, " + _Y + "%)";
            return _final;
        }

        function OnImagesSelected(images) {
            if (Object.keys(images).length > 0) {
                editingItem.value.watermark_image_url = images[Object.keys(images)[0]].image;
            }
        }

        function BrowseImage() {
            filemanager_visible.value = true;
        }

        function GetImageBaseURL(img) {
            return window["MEDIA_URL"] + "images/" + img;
        }

        function GetProFeaturesStyle() {
            return {
                width: "100%",
                color: "#fff",
                backgroundImage: "url(" + GetImageBaseURL("bg_blue_features2.jpg") + ")"
            };
        }

        function layoutValueSelected(val) {
            if (ispro.value === false) {
                if (val === "masonry") {
                    editingItem.value.layout = "grid";
                }
            }
        }

        function PoweredByClicked(value) {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"].url;
            }
            let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/activateonempremium`;
            let _APEX = window["APEX"];

            var finalval = 0;
            value === true ? (finalval = 1) : (finalval = 0);

            var body_data = JSON.stringify({
                serviceactive: finalval
            });


            // Make API Call
            fetch(finalurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-WP-Nonce": _APEX.activateonempremium.nonce
                },
                body: body_data
            })
                .then((response) => {
                    return response.json();
                })
                .then((result) => {
                    let data = result;
                    if (data.result.result === 3 && finalval === 1) {
                        showregisterbutton.value = true;
                    }
                });
        }

        function RegisterClicked() {
            //
            var _url = "https://www.aeroscroll.com/wp-login.php?action=register";
            window.open(_url, '_blank').focus();
        }

        function UpgradeToProClicked() {
            var _url = "https://www.aeroscroll.com";
            window.open(_url, '_blank').focus();
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
                loading.value = true;
                var pro_func = document.defaultView.window["pro_func"];

                if (IS_PRO === true) {
                    let _APEX = window["APEX"];

                    // 1. First check License
                    pro_func.CheckLicense((license_result) => {
                        if (license_result.active === true || license_result.code === "key_expired") {
                            licenseActive.value = true;

                            // 2. Then Get Grids
                            GetGrids();
                        } else {
                            licenseNotActive.value = true;
                            licenseActive.value = false;
                        }

                        loading.value = false;
                    }, _APEX.manageserial.nonce);                    
                } else {
                    GetGrids();
                    licenseActive.value = true;
                    loading.value = false;
                }
            });

            categories_options.value = [];
            imagegalleries_options.value = [];

            if ($q.sessionStorage.getItem("ig_table_rowsperpage")) {
                if ($q.sessionStorage.getItem("ig_table_rowsperpage") !== "undefined") {
                    initialPagination.value.rowsPerPage = $q.sessionStorage.getItem("ig_table_rowsperpage");
                }
            }
        });

        return {
            t,
            settingstab,
            layout_options,
            type_options,
            theme_options,
            watermarktype_options,
            watermark_position_options,
            advertisementtype_options,
            initialPagination,
            loading,
            tablecolumns,
            tablerows,
            searchField,
            searchValue,
            editMode,
            editingItem,
            notification_success,
            createMode,
            categories_options,
            imagegalleries_options,
            sortby_options_posts,
            sortby_options_imagegal,
            sortbydir_options,
            licenseActive,
            licenseNotActive,
            filemanager_visible,
            ispro,showregisterbutton,
            watermark_bgimage,
            EditGridClicked,
            DeleteGridClicked,
            CancelEditingClicked,
            SaveEditingClicked,
            CreateGridClicked,
            TogglePublished,
            SearchMethod,
            RefreshTable,
            ClickedShortCode,
            ResetColor,
            PaginationUpdated,
            GetWaterMarkPosition,
            GetWaterMarkTranslate,
            BrowseImage,
            OnImagesSelected,
            GetImageBaseURL,
            GetProFeaturesStyle,
            layoutValueSelected,
            PoweredByClicked,
            RegisterClicked,
            UpgradeToProClicked
        };
    }
});
</script>

<style scoped>
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

label {
    cursor: default !important;
}
</style>
