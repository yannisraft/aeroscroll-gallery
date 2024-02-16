console.log("Webpackworks");


console.log("FREE Version");




let VScroller = await import("./as-component-scroller.js?v=" + window.AEROSCROLL_GALLERY_ITERATION).then((module) => module?.default);



let AeroscrollLightbox = await import("./aeroscroll-lighbox-component.js?v=" + window.AEROSCROLL_GALLERY_ITERATION).then((module) => module?.default);

export default {
    template: `
        <div class="aeroscroll-container" :id="'aeroscroll-container-'+gridid">        
            <VScroller
                v-model="scrollerdata"
                :isInfinite="true"
                :scrollSpeed="scrollSpeed"                
                :cellSize="cellSize"
                :cellSquared="cellSquared"
                :height="height"
                :hasScrollbar="hasScrollbar"
                :layout="layout"
                
                :numcolumns="finalnumcolumns"                
                :rootID="rootID"
                :theme="theme"
                :type="type"
                :sidegap="sidegap"
                :cellgap="cellgap"
                :marginX="marginX"
                :marginY="marginY"
                :color_bg="color_bg"
                :usemousewheel="usemousewheel"
                :mousewheelenabled="mousewheelenabled"
                :hoveranimation="hoveranimation"
                :isready="isready"
                :totalDataAvailable="totalDataAvailable"
                
                :poweredbyactive="poweredbyactive"
                @on-cache-updated="onCacheUpdated"
                @on-dragging="onDragging"
                @on-stop-dragging="onStopDragging"
                @on-initilized="onInitialized"
                @on-update-data-next="onUpdateDataNext"
                @on-update-data-previous="onUpdateDataPrevious">            

                <template v-slot:cell="slotProps">
                    <div :class="['aeroscroll-postcell' ]" @click="() => ShowLightBox(slotProps.data, theme)" :style="{ '--cellgap': '20px', 'background-color': color_cell_bg }">
                    <div :class="['aeroscroll-postcell-image', slotProps.data.loading ? '' : 'imagevisible' ]" :style="{ 'background-image': (typeof slotProps.data.dt !== 'undefined' && slotProps.data.imageexists === true ) ? 'url('+slotProps.data.dt.thumbnail_image+')' : 'url('+GetNotFoundImageUrl()+')'}">
                    <div :class="['aeroscroll-content-container', 'container-theme-title', GetOnHoverStyle()]">
                            <!-- Theme A -->
                            <!-- ...On hover aeroscroll-postcell-image -->
                            <div v-if="(DEBUG === true && theme === 'theme_a')" class="aeroscroll-cell-header-title"><span style="color: #000; background-color: #fff;" @click.stop="HeaderTitleClicked(slotProps.data.dt, $event)">O: {{ slotProps.data.order }} -- I: {{ slotProps.data.globalindex }} | E: {{ slotProps.data.id }}  </span></div>

                            <!-- Theme B -->
                            <div v-if="(theme === 'theme_b')" class="container-theme-b">
                                <div  class="bg-theme-b"></div>
                                <div class="text-container text-container-b" v-if="!slotProps.data.loading">
                                    <div class="theme-title-b" :style="{ 'color': color_theme_title }">{{ slotProps.data.dt.title }}</div>
                                    <div class="theme-desc-b" :style="{ 'color': color_theme_desc }" v-html="slotProps.data.dt.content"></div>
                                           
                                    <span class="theme-datetime theme-datetime-b" v-if="slotProps.data.dt.timestamp">{{ FormatPostDateTime(slotProps.data.dt.timestamp) }}</span>                             
                                    <a class="theme-readmore-b" v-if="type === 'posts' && (showreadmore === true || showreadmore === 'true')" href="javascript:void(0)" @click.stop="NavigateToPost(slotProps.data.dt.permalink)" :style="{ 'color': color_theme_desc }">Read More</a>
                                </div>
                            </div>

                            <!-- Theme C -->
                            <div v-if="(theme === 'theme_c')" class="container-theme-c">
                                <div  class="bg-theme-c"></div>
                                <div class="text-container text-container-c"  v-if="!slotProps.data.loading">
                                    <div class="theme-title-c" :style="{ 'color': color_theme_title }">{{ slotProps.data.dt.title }}</div>
                                    <div class="theme-desc-c" :style="{ 'color': color_theme_desc }" v-html="slotProps.data.dt.content"></div>
                                    
                                    <span class="theme-datetime theme-datetime-c" v-if="slotProps.data.dt.timestamp">{{ FormatPostDateTime(slotProps.data.dt.timestamp) }}</span>
                                    <a class="theme-readmore-c" v-if="type === 'posts' && (showreadmore === true || showreadmore === 'true')" href="javascript:void(0)" @click.stop="NavigateToPost(slotProps.data.dt.permalink)" :style="{ 'color': color_theme_desc }">Read More</a>
                                </div>
                            </div>

                            <!-- Theme D -->
                            <div v-if="(theme === 'theme_d')" class="container-theme-d">
                                <div  class="bg-theme-d"></div>
                                <div class="text-container text-container-d"  v-if="!slotProps.data.loading" :style="{ 'background-color': color_theme_a }">
                                    <div class="theme-title-d" :style="{ 'color': color_theme_title }">{{ slotProps.data.dt.title }}</div>
                                    <div class="theme-desc-d" :style="{ 'color': color_theme_desc }" v-html="slotProps.data.dt.content"></div>
                                    

                                    <span class="theme-datetime theme-datetime-d" v-if="slotProps.data.dt.timestamp">{{ FormatPostDateTime(slotProps.data.dt.timestamp) }}</span>
                                    <a class="theme-readmore-d" v-if="(type === 'posts') && (showreadmore === true || showreadmore === 'true')" href="javascript:void(0)" @click.stop="NavigateToPost(slotProps.data.dt.permalink)" :style="{ 'color': color_theme_desc }">Read More</a>
                                </div>
                            </div>

                            <!-- Theme E -->
                            <div v-if="(theme === 'theme_e')" class="container-theme-e">
                                <div  class="bg-theme-e"></div>
                                <div class="text-container text-container-e"  v-if="!slotProps.data.loading">
                                    <div class="theme-title-e" :style="{ 'color': color_theme_title }">{{ slotProps.data.dt.title }}</div>
                                    <div class="theme-desc-e" :style="{ 'color': color_theme_desc }" v-html="slotProps.data.dt.content"></div>
                                    
                                    <span class="theme-datetime theme-datetime-e" v-if="slotProps.data.dt.timestamp">{{ FormatPostDateTime(slotProps.data.dt.timestamp) }}</span>
                                    <a class="theme-readmore-e" v-if="type === 'posts' && (showreadmore === true || showreadmore === 'true')" href="javascript:void(0)" @click.stop="NavigateToPost(slotProps.data.dt.permalink)" :style="{ 'color': color_theme_desc }">Read More</a>
                                </div>
                            </div>

                            <!-- Theme F -->
                            <div v-if="(theme === 'theme_f')" class="container-theme-f" :style="{ '-webkit-box-shadow': 'inset 0px 0px 0px 10px '+color_theme_a, '-moz-box-shadow': 'inset 0px 0px 0px 10px '+color_theme_a, 'box-shadow': 'inset 0px 0px 0px 10px '+color_theme_a  }">
                                <div  class="bg-theme-f"></div>
                                <div class="text-container text-container-f"  v-if="!slotProps.data.loading" :style="{ 'background-color': color_theme_a }">
                                    <div class="theme-title-f" :style="{ 'color': color_theme_title }">{{ slotProps.data.dt.title }}</div>
                                    <div class="theme-desc-f" :style="{ 'color': color_theme_desc }" v-html="slotProps.data.dt.content"></div>
                                    
                                    <span class="theme-datetime theme-datetime-f" v-if="slotProps.data.dt.timestamp">{{ FormatPostDateTime(slotProps.data.dt.timestamp) }}</span>
                                    <a class="theme-readmore-f" v-if="type === 'posts' && (showreadmore === true || showreadmore === 'true')" href="javascript:void(0)" @click.stop="NavigateToPost(slotProps.data.dt.permalink)" :style="{ 'color': color_theme_desc }">Read More</a>
                                </div>
                            </div>
                        </div> 
                    </div>
                    
                        <div v-if="slotProps.data.loading" class="aeroscroll-preloader-container">
                            <img width="30" :src="publicfolder + '/images/bars_white.svg'" />
                        </div>                        
                    </div>
                </template>
            </VScroller>      
            <AeroscrollLightbox :items="lightbox_imgs" :index="lightbox_index" @close="HideLightBox" :showLightbox="lightboxVisible" :fullScreen="true" :effect="'fade'"/>
        </div>
    `,
    name: "MainGrid",
    components: {
        VScroller,

        AeroscrollLightbox
    },
    data: function() {
        return {
            MINCELLSREQUIRED: 1,
            DEBUG: false,

            scrollerdata: {},
            finitescrollerdata: [],
            firstindex: 0,
            lastindex: 0,
            total: 0,
            newcellslength: 0,
            scrolltoposition: 0,
            postData: [],
            currentPage: 1,
            cellsPerPage: 1,
            isready: false,
            mousewheelenabled: true,
            finalnumcolumns: this.GetFinalNumberOfColumns(),

            totalDataAvailable: 0, // For caching purposes store the total posts available
            totalPagesAvailable: 0, // For caching purposes store the total pages available
            pagesLoaded: [], // For caching purposes store the pages that has been already loaded

            nextPosts: 0,
            previousPosts: 0,
            getPostsNumber: 0,
            perPage: 0,
            direction: 0,
            is_rowMasonry: false,
            isDragging: false,

            cachereference: [],
            lightbox_array: [],

            // Lightbox
            lightboxVisible: false,
            lightbox_index: 0,
            lightbox_imgs: [],
            lightbox_currentcell: {
                src: "",
                title: "",
                description: ""
            },

            share_link: window.location.href,
            share_title: "",
            share_description: ""
        };
    },
    props: {
        rootID: {
            type: String,
            default: ""
        },
        height: {
            type: Number,
            default: 400
        },
        gridid: {
            type: Number,
            default: -1
        },
        imagegallery_id: {
            type: Number,
            default: -1
        },

        numcolumns: {
            type: Number,
            default: 3
        },
        numcolumns_mid: {
            type: Number,
            default: 1
        },
        numcolumns_low: {
            type: Number,
            default: 1
        },
        cellSquared: {
            type: Boolean,
            default: false
        },
        scrollSpeed: {
            type: Number,
            default: 6
        },
        cellSize: {
            type: Number,
            default: 250
        },
        sidegap: {
            type: Number,
            default: 5
        },
        cellgap: {
            type: Number,
            default: 0
        },
        marginX: {
            type: Number,
            default: 0
        },
        marginY: {
            type: Number,
            default: 0
        },
        usemousewheel: {
            type: Boolean,
            default: true
        },
        hasScrollbar: {
            type: Boolean,
            default: true
        },
        layout: {
            type: String,
            default: ""
        },
        type: {
            type: String,
            default: ""
        },
        theme: {
            type: String,
            default: ""
        },
        themeonhover: {
            type: Boolean,
            default: true
        },
        showreadmore: {
            type: Boolean,
            default: true
        },

        poweredbyactive: {
            type: Boolean,
            default: true
        },

        color_bg: {
            type: String,
            default: ""
        },
        color_theme_a: {
            type: String,
            default: ""
        },
        color_theme_title: {
            type: String,
            default: ""
        },
        color_theme_desc: {
            type: String,
            default: ""
        },
        color_cell_bg: {
            type: String,
            default: ""
        },
        hoveranimation: {
            type: String,
            default: "none"
        },
        social_share_facebook: {
            type: Number,
            default: 0
        },
        social_share_twitter: {
            type: Number,
            default: 0
        },
        social_share_pinterest: {
            type: Number,
            default: 0
        },
        social_share_instagram: {
            type: Number,
            default: 0
        },
        social_share_tumblr: {
            type: Number,
            default: 0
        },
        social_share_email: {
            type: Number,
            default: 0
        },

        publicfolder: {
            type: String,
            default: ""
        },
        AEROSCROLL_GALLERY_ITERATION: {
            type: String,
            default: "1"
        }
    },
    methods: {
        GetFinalNumberOfColumns() {
            var cols = 1;

            if (window.innerWidth <= 480) {
                cols = this.numcolumns_low;
            } else if (window.innerWidth <= 1024) {
                cols = this.numcolumns_mid;
            } else {
                cols = this.numcolumns;
            }

            return cols;
        },
        OnScroll() {
            //
        },
        // LINK onInitialized
        onInitialized(data) {
            if (this.cellsPerPage > 100) this.cellsPerPage = 100; /// TODO Check if needed
            this.MINCELLSREQUIRED = data.minCellsRequired;
            this.cellsPerPage = data.cellsPerPage;
            let indexFirst = this.lastindex;
            this.lastindex += this.MINCELLSREQUIRED;
            this.getPostsNumber = this.MINCELLSREQUIRED;
            this.perPage = this.cellsPerPage;
            this.lastindex = this.perPage;

            this.scrollerdata["state"] = "init";
            for (var f = indexFirst; f < this.lastindex; f++) {
                this.scrollerdata[f] = {
                    id: 0,
                    index: f,
                    order: 1,
                    dt: {
                        title: "",
                        subtitle: "",
                        content: "",
                        feature_image: "",
                        thumbnail_image: ""
                    },
                    loading: true,
                    imageexists: true
                };
            }

            if (this.type === "posts") {
                this.GetPosts(this.perPage, "asc", indexFirst, this.lastindex, async (returned) => {
                    let _indexFirst = returned.indexFirst;
                    let _indexLast = returned.indexLast;
                    let _dataposts = returned.data.posts;

                    // If the Posts lesss than the number required then repeat the posts several times
                    let currentIndex = _indexFirst;
                    this.pagesLoaded.push(this.currentPage);

                    var databj = {};
                    databj["state"] = "update";
                    databj["indexFirst"] = _indexFirst;
                    databj["indexLast"] = _indexLast;

                    this.totalDataAvailable = returned.data.totalposts;
                    this.totalPagesAvailable = returned.data.totalpages;
                    databj["totalposts"] = returned.data.totalposts;
                    databj["totalpages"] = returned.data.totalpages;

                    this.isready = true;

                    if (_dataposts.length === 0) {
                        this.scrollerdata["state"] = "nodata";
                    } else {
                        for (let i = 0; i < _dataposts.length; i++) {
                            var returnobj = await this.GetPostObjectAsync(i, currentIndex, _dataposts[i], null);
                            databj[returnobj.index] = returnobj.result;

                            if (databj[returnobj.index].dt.feature_image === "" || typeof databj[returnobj.index].dt.feature_image === "undefined") {
                                databj[returnobj.index].dt.feature_image = this.GetNotFoundImageUrl();
                                databj[returnobj.index].dt.thumbnail_image = this.GetNotFoundImageUrl();
                            }
                            currentIndex++;
                        }
                        console.log("databj: : : ", databj);

                        this.scrollerdata = databj;
                    }
                });
            } else if (this.type === "imagegallery") {
                this.GetImageGalleryData(this.perPage, "asc", indexFirst, this.lastindex, async (returned) => {
                    let _indexFirst = returned.indexFirst;
                    let _indexLast = returned.indexLast;
                    let _datagalleryimages = returned.data.galleryimages;

                    // If the Images lesss than the number required then repeat the images several times
                    let currentIndex = _indexFirst;
                    this.pagesLoaded.push(this.currentPage);

                    var databj = {};
                    databj["state"] = "update";
                    databj["indexFirst"] = _indexFirst;
                    databj["indexLast"] = _indexLast;

                    this.totalDataAvailable = returned.data.totalimages;
                    this.totalPagesAvailable = returned.data.totalpages;
                    databj["totalposts"] = returned.data.totalimages;
                    databj["totalpages"] = returned.data.totalpages;

                    this.isready = true;

                    if (_datagalleryimages.length === 0) {
                        this.scrollerdata["state"] = "nodata";
                    } else {
                        for (let i = 0; i < _datagalleryimages.length; i++) {
                            var returnobj = _datagalleryimages[i];

                            var _imageexists = true;
                            if (returnobj.imageexists === false) _imageexists = returnobj.imageexists;

                            if (
                                returnobj.thumbnail_image === "" ||
                                typeof returnobj.thumbnail_image === "undefined" ||
                                returnobj.imageexists === false
                            ) {
                                returnobj.thumbnail_image = this.GetNotFoundImageUrl();
                                returnobj.featured_image = this.GetNotFoundImageUrl();
                                returnobj.url = this.GetNotFoundImageUrl();
                            }

                            databj[currentIndex] = {
                                id: returnobj.id,
                                index: currentIndex,
                                dt: {
                                    title: returnobj.title,
                                    subtitle: "",
                                    content: returnobj.description,
                                    feature_image: returnobj.thumbnail_image,
                                    thumbnail_image: returnobj.thumbnail_image,
                                    image_visible: true
                                },
                                order: returnobj.order,
                                loading: false,
                                imageexists: _imageexists
                            };

                            currentIndex++;
                        }
                        this.scrollerdata = databj;
                    }
                });
            }
        },

        onUpdateDataNext(done, postdone, _firstindex, _lastindex) {
            this.currentPage++;
            if (this.currentPage > this.totalPagesAvailable) this.currentPage = 1;
            this.direction = 1;

            // NEW
            let _new_firstindex = _lastindex;
            let _new_lastindex = _new_firstindex + this.getPostsNumber;

            // GET CELLS: We will not wait for the posts to load
            if (this.type === "posts") {
                // Check if this page is already loaded
                if (!this.pagesLoaded.includes(this.currentPage)) {
                    if (this.totalDataAvailable > Object.keys(this.cachereference).length) {
                        this.GetPosts(this.perPage, "asc", _new_firstindex, _new_lastindex, async (returned) => {
                            let _indexFirst = returned.indexFirst;
                            let _indexLast = returned.indexLast;
                            let _dataposts = returned.data.posts;

                            this.pagesLoaded.push(this.currentPage);

                            let postdonedata = {};
                            postdonedata["indexFirst"] = _indexFirst;
                            postdonedata["indexLast"] = _indexLast;

                            let currentIndex = _indexFirst;
                            for (let i = 0; i < _dataposts.length; i++) {
                                var returnobj = await this.GetPostObjectAsync(i, currentIndex, _dataposts[i], null);
                                postdonedata[currentIndex] = returnobj.result;
                                currentIndex++;
                            }

                            var postsLessThenPredicted = this.getPostsNumber - (Object.keys(postdonedata).length - 2);
                            postdone(postdonedata, postsLessThenPredicted);
                        });
                    }
                } else {
                    done(null);
                }
            } else if (this.type === "imagegallery") {
                if (this.totalDataAvailable > Object.keys(this.cachereference).length) {
                    this.GetImageGalleryData(this.perPage, "asc", _new_firstindex, _new_lastindex, async (returned) => {
                        let _indexFirst = returned.indexFirst; // FIXME add +1 ??
                        let _indexLast = returned.indexLast;
                        let _datagalleryimages = returned.data.galleryimages;

                        this.pagesLoaded.push(this.currentPage);

                        let postdonedata = {};
                        postdonedata["indexFirst"] = _indexFirst;
                        postdonedata["indexLast"] = _indexLast;

                        // If the Images lesss than the number required then repeat the images several times
                        let currentIndex = _indexFirst;
                        for (let i = 0; i < _datagalleryimages.length; i++) {
                            var returnobj = _datagalleryimages[i];
                            let feature_img = "";
                            let thumbnail_image = "";

                            var _imageexists = true;
                            if (returnobj.imageexists) _imageexists = returnobj.imageexists;

                            postdonedata[currentIndex] = {
                                id: returnobj.id,
                                index: currentIndex,
                                dt: {
                                    title: returnobj.title,
                                    subtitle: "",
                                    content: returnobj.description,
                                    feature_image: returnobj.thumbnail_image,
                                    thumbnail_image: returnobj.thumbnail_image,
                                    image_visible: true
                                },
                                order: returnobj.order,
                                loading: false,
                                imageexists: _imageexists
                            };

                            currentIndex++;
                        }

                        postdone(postdonedata, this.getPostsNumber - (Object.keys(postdonedata).length - 2));
                    });
                }
            }
        },

        onUpdateDataPrevious(done, postdone, _firstindex, _lastindex) {
            this.currentPage--;
            if (this.currentPage < 1) this.currentPage = this.totalPagesAvailable;

            this.direction = -1;

            let _new_firstindex = _firstindex - 1;
            let _new_lastindex = _new_firstindex - this.getPostsNumber;

            if (this.type === "posts") {
                // Check if this page is already loaded
                if (!this.pagesLoaded.includes(this.currentPage)) {
                    // GET CELLS: We will not wait for the posts to load
                    if (this.totalDataAvailable > Object.keys(this.cachereference).length) {
                        this.GetPosts(this.perPage, "asc", _new_firstindex, _new_lastindex, async (returned) => {
                            let _indexFirst = returned.indexFirst;
                            let _indexLast = returned.indexLast;
                            let _dataposts = returned.data.posts;

                            this.pagesLoaded.push(this.currentPage);

                            let postdonedata = {};
                            postdonedata["indexFirst"] = _indexFirst;
                            postdonedata["indexLast"] = _indexLast;

                            let currentIndex = _indexFirst;
                            for (let i = 0; i < _dataposts.length; i++) {
                                var returnobj = await this.GetPostObjectAsync(i, currentIndex, _dataposts[i], null);
                                postdonedata[currentIndex] = returnobj.result;
                                currentIndex--;
                            }

                            var postsLessThenPredicted = this.getPostsNumber - (Object.keys(postdonedata).length - 2);
                            postdone(postdonedata, postsLessThenPredicted);
                        });
                    }
                } else {
                    done(null);
                }
            } else if (this.type === "imagegallery") {
                if (this.totalDataAvailable > Object.keys(this.cachereference).length) {
                    this.GetImageGalleryData(this.perPage, "asc", _new_firstindex, _new_lastindex, async (returned) => {
                        let _indexFirst = returned.indexFirst;
                        let _indexLast = returned.indexLast;
                        let _datagalleryimages = returned.data.galleryimages;

                        this.pagesLoaded.push(this.currentPage);

                        let postdonedata = {};
                        postdonedata["indexFirst"] = _indexFirst;
                        postdonedata["indexLast"] = _indexLast;

                        // If the Images lesss than the number required then repeat the images several times
                        let currentIndex = _indexFirst;
                        for (let i = 0; i < _datagalleryimages.length; i++) {
                            var returnobj = _datagalleryimages[i];

                            var _imageexists = true;
                            if (returnobj.imageexists) _imageexists = returnobj.imageexists;

                            postdonedata[currentIndex] = {
                                id: returnobj.id,
                                index: currentIndex,
                                dt: {
                                    title: returnobj.title,
                                    subtitle: "",
                                    content: returnobj.description,
                                    feature_image: returnobj.thumbnail_image,
                                    thumbnail_image: returnobj.thumbnail_image,
                                    image_visible: true
                                },
                                order: returnobj.order,
                                loading: false,
                                imageexists: _imageexists
                            };

                            currentIndex--;
                        }

                        var postsLessThenPredicted = this.getPostsNumber - (Object.keys(postdonedata).length - 2);
                        postdone(postdonedata, postsLessThenPredicted);
                    });
                }
            }
        },
        onDragging() {
            this.isDragging = true;
        },
        onStopDragging() {
            this.isDragging = false;
        },
        UpdateScroller() {
            if (this.postData.length > 0) {
                this.lastindex += this.postData.length;
                for (var f = 0; f < this.postData.length; f++) {}
            }
        },
        GetTotalPostsAvailable(cellsPerPage, callback) {
            var rest_url = "http://localhost/wp_playground/wp-json";
            if (typeof wpData !== "undefined") {
                rest_url = wpData.rest_url;
            } else {}

            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"];
            }

            let total = 0;
            let totalpages = 0;
            let finalurl = `${_REST_URL}/wp-json/wp/v2/posts?per_page=` + cellsPerPage;
            fetch(finalurl).then((response) => {
                if (response) {
                    if (response.headers) {
                        total = response.headers.get("X-WP-Total");
                        totalpages = response.headers.get("X-WP-TotalPages");

                        this.totalPagesAvailable = totalpages;
                    }
                }

                callback(total);
            });
        },

        // LINK GetPosts
        GetPosts(cellsPerPage, order, indexFirst, indexLast, callback) {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"];
            }

            let finalurl =
                `${_REST_URL}/wp-json/aeroscroll/v1/getposts?page=` +
                this.currentPage +
                `&perpage=` +
                cellsPerPage +
                "&orderby=ID&order=" +
                order +
                "&gridid=" +
                this.gridid;
            fetch(finalurl)
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    callback({
                        indexFirst: indexFirst,
                        indexLast: indexLast,
                        order: order,
                        data: data
                    });
                });
        },
        GetImageGalleryData(cellsPerPage, order, indexFirst, indexLast, callback) {
            let _REST_URL = "http://localhost/";
            if (window["REST_URL"]) {
                _REST_URL = window["REST_URL"];
            }
            let finalurl =
                `${_REST_URL}/wp-json/aeroscroll/v1/getimagegallerydata?page=` +
                this.currentPage +
                `&perpage=` +
                cellsPerPage +
                "&imagegallery_id=" +
                this.imagegallery_id +
                "&gallery_id=" +
                this.gridid;

            fetch(finalurl, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    //console.log("imagegallery data ::: ", data);
                    callback({
                        indexFirst: indexFirst,
                        indexLast: indexLast,
                        order: order,
                        data: data
                    });
                });
        },
        GetNextPosts(MINCELLSREQUIRED, cellsPerPage) {},
        async GetPostObjectAsync(arrayindex, _index, _postData, callback) {
            let finalObj = {};
            let feature_img = "";
            let thumbnail_image = "";

            if (_postData) {
                if (_postData.featured_image) {
                    let found_img = _postData.featured_image;

                    // Here we should preload the image if there is one
                    feature_img = await this.PreloadImage(found_img);
                }

                if (_postData.thumbnail_image) {
                    let found_img = _postData.thumbnail_image;

                    // Here we should preload the image if there is one
                    thumbnail_image = await this.PreloadImage(found_img);
                }

                if (!feature_img) feature_img = "";
                if (!thumbnail_image) thumbnail_image = "";

                var _order = 1;
                if (_postData.order) _order = _postData.order;

                var _imageexists = true;
                if (_postData.imageexists) _imageexists = _postData.imageexists;

                finalObj = {
                    id: _postData.ID,
                    uid: _postData.uid,
                    index: _index,
                    order: _order,
                    dt: {
                        post_id: _postData.ID,
                        permalink: _postData.permalink,
                        timestamp: _postData.timestamp,
                        title: _postData.post_title,
                        subtitle: _postData.post_subtitle,
                        content: _postData.post_content,
                        feature_image: feature_img,
                        thumbnail_image: thumbnail_image,
                        image_visible: true
                    },
                    loading: false,
                    imageexists: _imageexists
                };
            }

            return {
                index: _index,
                result: finalObj
            };
        },

        async GetImageGalleryObjectAsync(arrayindex, _index, _postData, callback) {},

        async GetPostImage(found_img) {
            const img = await this.PreloadImage(found_img);
            return img;
        },
        PreloadImage(src) {
            return new Promise((resolve, reject) => {
                let img = new Image();
                img.onload = () => resolve(img.src);
                img.onerror = reject;
                img.src = src;
            });
        },
        GetTitle(slotProps) {
            let final = "";
            if (slotProps) {
                if (slotProps.data) {
                    if (slotProps.data.dt) {
                        final = slotProps.data.dt.title;
                    }
                }
            }
            return final;
        },
        GetImg(slotProps) {
            let final = "";
            if (slotProps) {
                if (slotProps.data) {
                    if (slotProps.data.dt) {
                        final = slotProps.data.dt.title;
                    }
                }
            }
            return final;
        },
        // Lightbox Functions
        ShowLightBox(_data, theme) {
            var celldata = _data.dt;
            if (_data.id) celldata.id = _data.id;
            this.lightbox_array = [];

            if (!this.isDragging) {
                if (celldata) {
                    this.lightbox_currentcell = {
                        src: celldata.feature_image,
                        title: celldata.title,
                        description: celldata.content
                    };

                    // extract dt data from celldata as array

                    var _temp_lightbox_index = 0;
                    for (var k = 0; k < this.cachereference.length; k++) {
                        this.cachereference[k].dt.src = this.cachereference[k].dt.feature_image;

                        var image_data = {
                            title: this.cachereference[k].dt.title,
                            description: this.cachereference[k].dt.subtitle,
                            src: this.cachereference[k].dt.src
                        };

                        if (_data.dt.title) image_data.title = _data.dt.title;

                        var meta_desc = _data.dt.content;
                        var meta_desc_size = 0;
                        if (_data.dt.content) meta_desc_size = _data.dt.content.length;

                        if (meta_desc_size > 160) meta_desc = meta_desc.substr(0, 160);
                        image_data.description = meta_desc;

                        if (this.type === "posts") {

                            image_data.src = this.cachereference[k].dt.thumbnail_image;


                            if (this.cachereference[k].id === celldata.post_id) {
                                var post_content = "";
                                var post_permalink = "";
                                var post_timestamp = "";
                                var post_thumbnal = "";

                                try {
                                    post_content = this.cachereference[k].dt.content;
                                    post_permalink = this.cachereference[k].dt.permalink;
                                    post_timestamp = this.cachereference[k].dt.timestamp;
                                    post_thumbnal = this.cachereference[k].dt.thumbnail_image;
                                } catch (ex) {}

                                _temp_lightbox_index = k;
                            }
                        } else {
                            if (this.cachereference[k].id === celldata.id) {
                                _temp_lightbox_index = k;
                            }
                        }

                        this.lightbox_array.push(image_data);
                    }

                    this.lightbox_imgs = this.lightbox_array;
                    this.lightbox_index = _temp_lightbox_index;

                    this.lightboxVisible = true;
                    this.mousewheelenabled = false;
                }
            }
        },
        HideLightBox() {
            this.lightboxVisible = false;
            this.mousewheelenabled = true;
            this.lightbox_index = null;
        },
        HeaderTitleClicked(data, e) {
            e.preventDefault();
            if (data.permalink) window.location.assign(data.permalink);
        },
        onCacheUpdated(updatedcache) {
            this.cachereference = updatedcache;
        },
        GetSpinnerStyle() {
            var _width = 56 + "px";
            var _height = 56 + "px";

            let containerelements = document.getElementsByClassName("aeroscroll-container");
            if (containerelements.length > 0) {
                if (containerelements[0].clientHeight / this.numcolumns < 100) {
                    _width = 32 + "px";
                    _height = 32 + "px";
                }
            }

            return {
                width: _width,
                height: _height
            };
        },
        GetOnHoverStyle() {
            var finalclass = "";
            if (this.themeonhover === true || this.themeonhover === "true") {
                finalclass = "containeronhover";
            } else {
                finalclass = "containerstatictheme";
            }

            return finalclass;
        },
        NavigateToPost(link) {
            window.location.assign(link);
        },
        GetNotFoundImageUrl() {
            var getUrl = window.location;
            var baseUrl = window["BASE_URL"];
            return baseUrl + "/wp-content/plugins/aeroscroll-gallery/public/images/notfound_image.png";
        },
        FormatPostDateTime(tmstmp) {
            var __date = new Date(tmstmp * 1000)
            return __date.toLocaleString();
        }
    },
    mounted() {}
};