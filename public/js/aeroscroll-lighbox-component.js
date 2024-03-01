const {
    ref,
    onMounted
} = Vue;

let LazyLoadDirective = await import("./utils/LazyLoad.js?v=" + window.AEROSCROLL_GALLERY_ITERATION).then((module) => module?.default);
let AutoplayObserver = await import("./utils/AutoplayObserver.js?v=" + window.AEROSCROLL_GALLERY_ITERATION).then((module) => module?.default);

//import LazyLoadDirective from "./utils/LazyLoad";
//import AutoplayObserver from "./utils/AutoplayObserver";
import {
    disableBodyScroll,
    enableBodyScroll
} from "./libs/bodyScrollLock.esm.js";

export default {
    template: `
        <transition name="aero-lightbox-modal">
            <div class="aero-lightbox"
            v-bind:class="lightboxClasses"
            v-if="isVisible"
            ref="aeroLightbox"
            @click="closeModal"
            v-bind:style="lightboxStyles">

            <div v-if="gallery" class="aero-lightbox-thumbs">
                <div class="aero-lightbox-thumbs__list">
                <button
                    type="button"
                    v-for="(item, itemIndex) in items"
                    :key="itemIndex"
                    :class="{
                    active: itemIndex === imgIndex,
                    'is-video': getMediaType(itemIndex) === 'video'
                    }"
                    @click="imgIndex = itemIndex"
                    class="aero-lightbox__thumb">

                    <svg class="aero-lightbox__thumb__icon" xmlns="http://www.w3.org/2000/svg" v-if="getMediaType(itemIndex) === 'video'" viewBox="0 0 24 24">
                    <path d="M6.5 5.4v13.2l11-6.6z"></path>
                    </svg>

                    <img v-if="(getMediaType(imgIndex) !== 'link')" :src="itemThumb(getItemSrc(itemIndex), itemIndex)" alt="" />
                    <img v-if="(getMediaType(imgIndex) === 'link')" :src="itemThumbPost(getItemSrc(itemIndex), itemIndex)" alt="" />
                </button>
                </div>
            </div>
            <!--/aero-lightbox-thumbs-->

            <div
                class="aero-lightbox__inner"
                :style="innerStyles"

                @mousedown="startSwipe"
                @mousemove="continueSwipe"
                @mouseup="endSwipe"
                @touchstart="startSwipe"
                @touchmove="continueSwipe"
                @touchend="endSwipe"
                >
                <div class="aero-lightbox__progressbar" :style="stylesInterval"></div>

                <div class="aero-lightbox__navigation">
                <button type="button" class="aero-lightbox-button aero-lightbox-button--prev" :title="translations.previous" :class="buttonsClasses" v-show="(hasPreviousButton || loopData) && items.length > 1" @click="onPrevClick">
                    <slot name="icon-previous">
                    <div class="aero-lightbox-button__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.28 15.7l-1.34 1.37L5 12l4.94-5.07 1.34 1.38-2.68 2.72H19v1.94H8.6z"></path></svg>
                    </div>
                    </slot>
                </button>

                <button type="button" class="aero-lightbox-button aero-lightbox-button--next" :title="translations.next" :class="buttonsClasses" v-show="(hasNextButton || loopData) && items.length > 1" @click="onNextClick(false)">
                    <slot name="icon-next">
                    <div class="aero-lightbox-button__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.4 12.97l-2.68 2.72 1.34 1.38L19 12l-4.94-5.07-1.34 1.38 2.68 2.72H5v1.94z"></path></svg>
                    </div>
                    </slot>
                </button>
                </div>
                <!--/aero-lightbox__navigation-->

                <div v-if="effect === 'swipe'"
                class="aero-lightbox__wrapper aero-lightbox__wrapper--swipe"
                :style="{
                    transform: 'translate3d('+xSwipeWrapper+'px, '+ySwipeWrapper+'px, 0)',
                    transition: swipeAnimation
                }"
                >
                <div
                    v-for="(item, itemIndex) in items"
                    :key="itemIndex"
                    ref="items"
                    class="aero-lightbox__slide"
                    :class="{ 'aero-lightbox__slide--current': itemIndex === imgIndex }"
                >
                    <div
                        v-lazyload
                        v-if="getMediaType(itemIndex) === 'image'" key="image" :style="imgWrapperStyle" class="aero-lightbox__slide__img">
                    <img v-if="!isItemPicture(itemIndex)"
                        :data-src="getItemSrc(itemIndex)"
                        :data-srcset="getItemSrcSet(itemIndex)"
                        :data-sizes="getItemSizes(itemIndex)"
                        :key="itemIndex"
                        draggable="false"
                        :alt="getItemAlt(itemIndex)"

                        @load="imageLoaded"
                        @click="zoomImage(itemIndex)"
                        @mousedown="handleMouseDown($event)"
                        @mouseup="handleMouseUp($event)"
                        @mouseleave="handleMouseLeave($event)"
                        @mousemove="handleMouseMove($event)"

                        @touchstart="handleMouseDown($event)"
                        @touchmove="handleMouseMove($event)"
                        @touchend="handleMouseUp($event)"
                        />
                    <picture :key="itemIndex" v-else>
                        <source
                            v-for="(source, sourceIndex) in getPictureSources(itemIndex)"
                            :data-srcset="source.srcset"
                            :data-media="source.media"
                            :type="source.type"
                            :data-sizes="source.sizes || getItemSizes(imgIndex)"
                            :key="source-imgIndex-sourceIndex"
                        >
                        <img
                            :data-src="getItemSrc(itemIndex)"
                            :data-srcset="getItemSrcSet(itemIndex)"
                            :data-sizes="getItemSizes(itemIndex)"
                            draggable="false"
                            :alt="getItemAlt(itemIndex)"

                            @load="imageLoaded"
                            @click="zoomImage(itemIndex)"
                            @mousedown="handleMouseDown($event)"
                            @mouseup="handleMouseUp($event)"
                            @mouseleave="handleMouseLeave($event)"
                            @mousemove="handleMouseMove($event)"

                            @touchstart="handleMouseDown($event)"
                            @touchmove="handleMouseMove($event)"
                            @touchend="handleMouseUp($event)"
                        />
                    </picture>

                    <div v-show="imageLoading" class="aero-lightbox-loading-wrapper">
                        <slot name="loading">
                        <div class="aero-lightbox-loading"></div>
                        </slot>
                    </div>
                    <!--/loading-wrapper-->
                    </div>
                    <!--/imgs-slide-->

                    <div v-else key="video" class="aero-lightbox__iframe">
                    <iframe
                        class="aero-lightbox-video"
                        v-autoplayObserver
                        :data-autoplay="setAutoplay(itemIndex)"
                        :src="getVideoUrl(getItemSrc(itemIndex))"
                        v-if="(!checkIsMp4(getItemSrc(itemIndex)) && getMediaType(itemIndex) === 'video')"
                        :style="aspectRatioVideo"
                        :key="itemIndex"
                        frameborder="0"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>

                    <iframe
                        class="aero-lightbox-pdf"
                        :src="getItemSrc(itemIndex)"
                        v-if="(getMediaType(itemIndex) === 'iframe') || (getPDFurl(getItemSrc(itemIndex)))"
                        :key="itemIndex"
                        frameborder="0"
                        allowfullscreen>
                    </iframe>

                    <video
                        v-autoplayObserver
                        :data-autoplay="setAutoplay(itemIndex)"
                        class="aero-lightbox-video"
                        v-if="checkIsMp4(getItemSrc(itemIndex)) || getMediaType(itemIndex) === 'webVideo'"
                        :style="aspectRatioVideo" :key="checkIsMp4(getItemSrc(itemIndex))"
                        controls=""
                        controlslist="nodownload" l
                        poster="">
                        <source :src="checkIsMp4(getItemSrc(itemIndex))" :type="'video/'+(getVideoExt(getItemSrc(itemIndex)) ? getVideoExt(getItemSrc(itemIndex)) : getExtFromItem(itemIndex))">
                        Sorry, your browser doesn't support embedded videos
                    </video>
                    </div>
                    <!--/aero-lightbox__iframe-->
                </div>
                <!--/aero-lightbox__slide-->
                </div>
                <!--/aero-lightbox-wrapper-->

                <div v-if="effect === 'fade'" class="aero-lightbox__wrapper">
                <div
                    ref="items"
                    class="aero-lightbox__slide aero-lightbox__slide--current"
                >
                    <transition name="aero-lightbox-slide-change" mode="out-in">
                    <div v-if="getMediaType(imgIndex) === 'image'" key="image" :style="imgWrapperStyle" class="aero-lightbox__slide__img">
                        <transition name="aero-lightbox-slide-change" mode="out-in" v-if="!isItemPicture(imgIndex)">
                        <img
                            :src="getItemSrc(imgIndex)"
                            :srcset="getItemSrcSet(imgIndex)"
                            :sizes="getItemSizes(imgIndex)"
                            :key="imgIndex"
                            draggable="false"
                            :alt="getItemAlt(imgIndex)"

                            @load="imageLoaded"
                            @click="zoomImage"
                            @mousedown="handleMouseDown($event)"
                            @mouseup="handleMouseUp($event)"
                            @mouseleave="handleMouseLeave($event)"
                            @mousemove="handleMouseMove($event)"
                        />
                        </transition>
                        <transition v-else name="aero-lightbox-slide-change" mode="out-in">
                        <picture :key="imgIndex">
                            <source
                                v-for="(source, sourceIndex) in getPictureSources(imgIndex)"
                                :srcset="source.srcset"
                                :type="source.type"
                                :media="source.media"
                                :sizes="source.sizes || getItemSizes(imgIndex)"
                                :key="source-imgIndex-sourceIndex"
                            >
                            <img
                                :src="getItemSrc(imgIndex)"
                                :srcset="getItemSrcSet(imgIndex)"
                                :sizes="getItemSizes(imgIndex)"
                                draggable="false"
                                :alt="getItemAlt(imgIndex)"

                                @load="imageLoaded"
                                @click="zoomImage(imgIndex)"
                                @mousedown="handleMouseDown($event)"
                                @mouseup="handleMouseUp($event)"
                                @mouseleave="handleMouseLeave($event)"
                                @mousemove="handleMouseMove($event)"
                            />
                        </picture>
                        </transition>


                        <div v-show="imageLoading" class="aero-lightbox-loading-wrapper">
                        <slot name="loading">
                            <div class="aero-lightbox-loading"></div>
                        </slot>
                        </div>
                        <!--/loading-wrapper-->
                    </div>
                    <!--/imgs-slide-->

                    <div v-else key="video" class="aero-lightbox__iframe">
                        <transition name="aero-lightbox-slide-change" mode="out-in">
                        <iframe
                            class="aero-lightbox-video"
                            v-autoplayObserver
                            :data-autoplay="setAutoplay(imgIndex)"
                            :src="getVideoUrl(getItemSrc(imgIndex))"
                            v-if="(!checkIsMp4(getItemSrc(imgIndex)) && getMediaType(imgIndex) === 'video')"
                            :style="aspectRatioVideo"
                            :key="getVideoUrl(getItemSrc(imgIndex))"
                            frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>

                        <iframe
                            class="aero-lightbox-pdf"
                            :src="getItemSrc(imgIndex)"
                            v-if="(getMediaType(imgIndex) === 'iframe') || (getPDFurl(getItemSrc(imgIndex)))"
                            :key="imgIndex"
                            frameborder="0"
                            allowfullscreen>
                        </iframe>

                        <iframe
                            class="aero-lightbox-pdf"
                            :src="getItemLinkSrc(imgIndex)"
                            v-if="(getMediaType(imgIndex) === 'link') || (getPDFurl(getItemSrc(imgIndex)))"
                            :key="imgIndex"
                            frameborder="0"
                            allowfullscreen>
                        </iframe>

                        <video class="aero-lightbox-video"
                            v-autoplayObserver
                            :data-autoplay="setAutoplay(imgIndex)"
                            v-if="checkIsMp4(getItemSrc(imgIndex)) || getMediaType(imgIndex) === 'webVideo'"
                            :style="aspectRatioVideo" :key="checkIsMp4(getItemSrc(imgIndex))"
                            controls=""
                            controlslist="nodownload"
                            poster="">
                            <source :src="checkIsMp4(getItemSrc(imgIndex))" :type="'video/'+(getVideoExt(getItemSrc(imgIndex)) ? getVideoExt(getItemSrc(imgIndex)) : getExtFromItem(imgIndex))">
                            Sorry, your browser doesn't support embedded videos
                        </video>
                        </transition>
                    </div>
                    <!--/aero-lightbox__iframe-->

                    </transition>
                </div>
                <!--/aero-lightbox__slide-->
                </div>
                <!--/aero-lightbox__wrapper-->

                <transition name="aero-lightbox-modal">
                <div v-show="checkIfIsObject(imgIndex) && (items[imgIndex].title || items[imgIndex].description)" key="caption-block" class="aero-lightbox-caption">
                    <transition name="aero-lightbox-slide-change" mode="out-in">
                    <h6 key="title" v-html="items[imgIndex].title" v-if="checkIfIsObject(imgIndex) && items[imgIndex].title"></h6>
                    </transition>

                    <transition name="aero-lightbox-slide-change" mode="out-in">
                    <p key="description" v-html="items[imgIndex].description" v-if="checkIfIsObject(imgIndex) && items[imgIndex].description"></p>
                    </transition>
                </div>
                <!--/aero-lightbox-caption-->
                </transition>

                <div class="aero-lightbox-toolbar" :class="buttonsClasses">

                <button type="button" v-if="this.slideshow && items.length > 1" :title="translations.playSlideShow" class="aero-lightbox-toolbar__btn" @click="togglePlaySlideshow">
                    <svg xmlns="http://www.w3.org/2000/svg" v-if="!isPlayingSlideShow" viewBox="0 0 24 24">
                    <path d="M6.5 5.4v13.2l11-6.6z"></path>
                    </svg>

                    <svg v-else xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <rect id="svg_4" height="11.97529" width="11.728392" y="6.030873" x="6.259265" stroke-width="1.5" stroke="#000" fill="#000000"/>
                    </g>
                    </svg>
                </button>

                <button type="button" @click="showThumbs = !showThumbs" :title="translations.showThumbNails" v-if="items.length > 1 && gallery" class="aero-lightbox-toolbar__btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M14.59 14.59h3.76v3.76h-3.76v-3.76zm-4.47
                    0h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76H5.65v-3.76zm8.94-4.47h3.76v3.76h-3.76v-3.76zm-4.47
                    0h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76H5.65v-3.76zm8.94-4.47h3.76v3.76h-3.76V5.65zm-4.47
                    0h3.76v3.76h-3.76V5.65zm-4.47 0h3.76v3.76H5.65V5.65z">
                    </path>
                    </svg>
                </button>

                <button type="button" v-if="fullScreen" @click="toggleFullScreenMode" class="aero-lightbox-toolbar__btn" :title="translations.fullScreen">
                    <svg width="20px" height="20px" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.5 11H3v4h4v-1.5H4.5V11zM3 7h1.5V4.5H7V3H3v4zm10.5 6.5H11V15h4v-4h-1.5v2.5zM11 3v1.5h2.5V7H15V3h-4z"></path>
                    </svg>
                </button>

                <button type="button" v-if="showCloseButton" class="aero-lightbox-toolbar__btn" :title="translations.close" @click="close">
                    <slot name="close">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 10.6L6.6 5.2 5.2 6.6l5.4 5.4-5.4 5.4 1.4 1.4 5.4-5.4 5.4 5.4 1.4-1.4-5.4-5.4 5.4-5.4-1.4-1.4-5.4 5.4z"></path>
                    </svg>
                    </slot>
                </button>
                </div>
                <!--/aero-lightbox--toolbar-->
            </div>
            <!--/aero-lightbox-inner-->


            <transition name="aero-lightbox-modal">
                <div v-if="isZooming && useZoomBar" class="aero-lightbox-zoom">
                <svg height="469pt" class="aero-lightbox-zoom__icon" viewBox="0 -192 469.33333 469" width="469pt"
                    xmlns="http://www.w3.org/2000/svg"><path d="m437.332031.167969h-405.332031c-17.664062
                    0-32 14.335937-32 32v21.332031c0 17.664062 14.335938 32 32 32h405.332031c17.664063 0 32-14.335938
                    32-32v-21.332031c0-17.664063-14.335937-32-32-32zm0 0"/>
                </svg>
                <input type="range" v-model="zoomBar" name="points" min="0" max="50" />
                <svg height="426.66667pt" class="aero-lightbox-zoom__icon" viewBox="0 0 426.66667 426.66667" width="426.66667pt" xmlns="http://www.w3.org/2000/svg">
                    <path d="m405.332031 192h-170.664062v-170.667969c0-11.773437-9.558594-21.332031-21.335938-21.332031-11.773437 0-21.332031
                    9.558594-21.332031 21.332031v170.667969h-170.667969c-11.773437 0-21.332031 9.558594-21.332031 21.332031 0
                    11.777344 9.558594 21.335938 21.332031 21.335938h170.667969v170.664062c0 11.777344 9.558594 21.335938 21.332031
                    21.335938 11.777344 0 21.335938-9.558594 21.335938-21.335938v-170.664062h170.664062c11.777344 0 21.335938-9.558594
                    21.335938-21.335938 0-11.773437-9.558594-21.332031-21.335938-21.332031zm0 0"/>
                </svg>
                </div>
            </transition>

            </div>
            <!--/aero-lightbox-->
        </transition>
        <!--/transition-->
    `,
    name: "AeroscrollLightbox",
    directives: {
        lazyload: LazyLoadDirective,
        autoplayObserver: AutoplayObserver
    },
    props: {
        index: {
            required: true
        },

        effect: {
            type: String,
            default: "swipe"
        },

        items: {
            type: Array,
            required: true
        },

        loop: {
            type: Boolean,
            default: true
        },

        slideshow: {
            type: Boolean,
            default: true
        },

        slideshowColorBar: {
            type: String,
            default: "#fa4242"
        },

        slideshowDuration: {
            type: Number,
            default: 3000
        },

        useZoomBar: {
            type: Boolean,
            default: false
        },

        closeOnClickOutsideMobile: {
            type: Boolean,
            default: false
        },

        srcName: {
            type: String,
            default: "src"
        },

        srcSetName: {
            type: String,
            default: "srcset"
        },

        srcThumb: {
            type: String,
            default: "thumb"
        },

        srcMediaType: {
            type: String,
            default: "mediaType"
        },

        overlayColor: {
            type: String,
            default: "rgba(30, 30, 30, .9)"
        },

        zIndex: {
            type: Number,
            default: 999999
        },

        gallery: {
            type: Boolean,
            default: true
        },

        fullScreen: {
            type: Boolean,
            default: false
        },

        thumbsPosition: {
            type: String,
            default: "right"
        },

        youtubeCookies: {
            type: Boolean,
            default: true
        },

        enableWheelEvent: {
            type: Boolean,
            default: false
        },

        showCloseButton: {
            type: Boolean,
            default: true
        },

        disableZoom: {
            type: Boolean,
            default: false
        },

        dir: {
            type: String,
            default: "ltr"
        },

        enableScrollLock: {
            type: Boolean,
            default: true
        },
        translations: {
            type: Object,
            default () {
                return {
                    previous: "Previous",
                    next: "Next",
                    showThumbNails: "Show thumbnails",
                    playSlideShow: "Play slideshow",
                    fullScreen: "Fullscreen",
                    close: "Close"
                };
            }
        },
        showLightbox: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            // swipe data
            initialMouseX: 0,
            initialMouseY: 0,
            endMouseX: 0,
            endMouseY: 0,
            swipeType: null,
            IsSwipping: false,
            isDraggingSwipe: false,

            // use for mouse wheel
            prevTime: 0,

            // swipe effect
            xSwipeWrapper: 0,
            ySwipeWrapper: 0,
            swipeAnimation: null,
            swipeInterval: null,
            lightboxInnerWidth: null,

            // styles data
            imgIndex: this.index,
            isVisible: false,
            paddingBottom: false,
            imageLoading: false,
            showThumbs: false,
            isFullScreenMode: false,

            // aspect ratio videos
            aspectRatioVideo: {
                width: "auto",
                height: "auto"
            },

            // props to bind styles
            buttonsVisible: true,
            scale: 1,
            top: 0,
            left: 0,
            lastX: 0,
            lastY: 0,
            isDraging: false,
            canZoom: true,
            isZooming: false,
            transition: "all .3s ease",
            zoomBar: 0,

            // slideshow playing data
            isPlayingSlideShow: false,
            intervalProgress: null,
            loopData: false,
            stylesInterval: {
                display: "block"
            }
        };
    },
    watch: {
        showLightbox(newVal, prevVal) {
            this.isVisible = newVal;
        },

        zoomBar(newVal, prevVal) {
            let item;
            if (this.isZooming) {
                if (this.effect == "swipe") {
                    item = this.$refs.items[this.imgIndex].childNodes[0];
                } else {
                    item = this.$refs.items.childNodes[0];
                }

                const newZoom = 1.6 + newVal / 10;
                item.style.transform =
                    "translate3d(calc(-50% + " +
                    this.left +
                    "px), calc(-50% + " +
                    this.top +
                    "px), 0px) scale3d(" +
                    newZoom +
                    ", " +
                    newZoom +
                    ", " +
                    newZoom +
                    ")";
            }
        },

        showThumbs(prev, val) {
            let widthGalleryBlock = 212;
            let swipeAnimation = "all .3s ease";
            if (window.innerWidth < 767) {
                widthGalleryBlock = 102;
                swipeAnimation = null;
            }

            if (this.thumbsPosition === "bottom") {
                widthGalleryBlock = 0;
            }

            const self = this;
            this.swipeAnimation = swipeAnimation;

            if (prev) {
                if (this.dir === "rtl") {
                    this.xSwipeWrapper = this.imgIndex * (window.innerWidth - widthGalleryBlock) + 30 * this.imgIndex;
                } else {
                    this.xSwipeWrapper = -this.imgIndex * (window.innerWidth - widthGalleryBlock) - 30 * this.imgIndex;
                }
            } else {
                if (this.dir === "rtl") {
                    this.xSwipeWrapper = this.imgIndex * window.innerWidth + 30 * this.imgIndex;
                } else {
                    this.xSwipeWrapper = -this.imgIndex * window.innerWidth - 30 * this.imgIndex;
                }
            }

            setTimeout(function() {
                self.swipeAnimation = null;
            }, 300);
        },

        index(prev, val) {
            const self = this;

            // body scroll lock
            const $body = document.querySelector("body");

            if (prev !== null) {
                // swipe type
                this.swipeType = null;
                this.initialMouseY = 0;
                this.ySwipeWrapper = 0;

                // set loop from data
                this.loopData = this.loop;

                // swipe effect case remove loop
                if (this.effect === "swipe") {
                    this.loopData = false;
                    window.addEventListener("resize", this.xPositionOnResize);
                }

                // add img index
                this.imgIndex = prev;
                //this.isVisible = true;

                // add events listener
                window.addEventListener("keydown", this.eventListener);

                // add wheel event
                if (this.enableWheelEvent) {
                    window.addEventListener("wheel", this.wheelEvent);
                }

                // add click event
                setTimeout(function() {
                    window.addEventListener("click", self.showButtons);
                }, 200);

                if (this.enableScrollLock) {
                    setTimeout(function() {
                        self.setCompensateForScrollbar();
                        disableBodyScroll(self.$refs.aeroLightbox);
                    }, 50);
                }
            } else {
                // hide and stop slideshow
                //this.isVisible = false;
                this.stopSlideShow();

                // set starts X to 0
                this.startsX = 0;
                this.initialMouseY = 0;
                this.swipeType = null;

                // clear interval
                clearInterval(this.swipeInterval);
                this.swipeAnimation = null;

                // finish swipe
                this.isDraggingSwipe = false;
                this.isZooming = true;

                // remove events listener
                window.removeEventListener("keydown", this.eventListener);

                if (this.enableScrollLock) {
                    self.removeCompensateForScrollbar();
                    enableBodyScroll(self.$refs.aeroLightbox);
                }

                // remove click event
                window.removeEventListener("click", this.showButtons);

                // remove resize event
                window.removeEventListener("resize", this.xPositionOnResize);

                // remove wheel event
                if (this.enableWheelEvent) {
                    window.removeEventListener("wheel", this.wheelEvent);
                }
            }
        },

        imgIndex(prev, val) {
            const thisContext = this;

            // when animation is loaded
            this.$nextTick(() => {
                if (this.effect === "swipe") {
                    this.setLightboxInnerWidth();
                    this.setXPosition(prev);
                }

                if ((prev !== null) & (val === null)) {
                    this.$emit("on-open", prev);
                }

                if (prev !== null) {
                    if (prev !== val) {
                        if (!this.getYoutubeUrl(this.getItemSrc(prev)) && !this.getVimeoUrl(this.getItemSrc(prev))) {
                            this.stopVideos();
                        }
                    }

                    // if is an image change imageLoading to true
                    if (!this.getVideoUrl(this.getItemSrc(prev))) {
                        if (!this.is_cached(this.getItemSrc(prev))) {
                            this.imageLoading = true;
                        }
                    }

                    // add caption padding to Lightbox wrapper
                    //this.addCaptionPadding(); //TODO

                    // setAspectRatioVideo when is swipe
                    if (this.effect === "swipe") {
                        this.setAspectRatioVideo();
                    } else {
                        if (this.getVideoUrl(this.getItemSrc(prev))) {
                            this.setAspectRatioVideo();
                        }
                    }
                }

                // reset zoom
                this.resetZoom();

                // reset swipe type
                this.swipeType = null;
                this.ySwipeWrapper = 0;
            });
        }
    },

    beforeDestroy() {
        if (this.enableScrollLock) {
            this.removeCompensateForScrollbar();
            if (this.$refs.aeroLightbox) {
                enableBodyScroll(this.$refs.aeroLightbox);
            }
        }
    },

    methods: {
        getExtFromItem(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            if (this.checkIfIsObject(imgIndex)) {
                const item = this.items[imgIndex];

                //item extension is specified, so return it
                if (item["ext"]) {
                    return item["ext"];
                }

                return "mp4";
            }
        },

        stopVideos() {
            const videos = document.getElementsByClassName("aero-lightbox-video");
            const isVideoPlaying = (video) => !!(video.currentTime > 0 && !video.paused && !video.ended && video.readyState > 2);
            if (videos.length > 0) {
                Array.prototype.forEach.call(videos, (video) => {
                    const type = video.tagName;

                    if (type === "IFRAME") {
                        var iframeSrc = video.src;
                        return (video.src = iframeSrc);
                    }

                    if (isVideoPlaying(video)) {
                        return video.pause();
                    }
                });
            }
        },

        removeCompensateForScrollbar() {
            document.body.classList.remove("compensate-for-scrollbar");
            const noscrollStyle = document.getElementById("aerolightbox-style-noscroll");
            if (noscrollStyle !== null) {
                document.getElementById("aerolightbox-style-noscroll").remove();
            }
        },

        setCompensateForScrollbar() {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (!isMobile && document.body.scrollHeight > window.innerHeight) {
                document
                    .getElementsByTagName("head")[0]
                    .insertAdjacentHTML(
                        "beforeend",
                        '<style id="aerolightbox-style-noscroll" type="text/css">.compensate-for-scrollbar{margin-right:' +
                        (window.innerWidth - document.documentElement.clientWidth) +
                        "px;}</style>"
                    );

                document.body.classList.add("compensate-for-scrollbar");
            }
        },

        setAutoplay(itemIndex) {
            if (this.checkIfIsObject(itemIndex) && this.items[itemIndex].hasOwnProperty("autoplay") && this.items[itemIndex].autoplay) {
                return true;
            }

            return false;
        },

        toggleFullScreenMode() {
            if (this.isFullScreenMode) {
                this.closeFullscreen();
            } else {
                this.fullScreenMode();
            }

            this.isFullScreenMode = !this.isFullScreenMode;
        },

        closeFullscreen() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                /* Firefox */
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                /* Chrome, Safari and Opera */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE/Edge */
                document.msExitFullscreen();
            }
        },

        fullScreenMode() {
            /* Get the documentElement (<html>) to display the page in fullscreen */
            var elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) {
                /* Firefox */
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Chrome, Safari and Opera */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE/Edge */
                elem.msRequestFullscreen();
            }
        },

        // check if event is arrow button or toolbar button
        checkIfIsButton(event) {
            var elements =
                ".aero-lightbox__iframe *, .aero-lightbox-button, .aero-lightbox-button *, .aero-lightbox-toolbar__btn, .aero-lightbox-toolbar__btn *, .aero-lightbox-caption h6, .aero-lightbox-caption p, .aero-lightbox-caption a";
            if (event.target.matches(elements)) {
                return true;
            }

            return false;
        },

        // start swipe event
        startSwipe(event) {
            if (this.isZooming) {
                return false;
            }

            // check if is some button
            if (this.checkIfIsButton(event)) {
                return false;
            }

            // clear interval
            clearInterval(this.swipeInterval);
            this.swipeAnimation = null;

            // starts swipe
            this.isDraggingSwipe = true;
            this.initialMouseX = this.getMouseXPosFromEvent(event);
            this.initialMouseY = this.getMouseYPosFromEvent(event);
        },

        // continue swipe event
        continueSwipe(event) {
            if (this.isDraggingSwipe) {
                this.IsSwipping = true;
                const currentPosX = this.getMouseXPosFromEvent(event);
                const currentPosY = this.getMouseYPosFromEvent(event);
                const windowWidth = this.lightboxInnerWidth;

                // diffs
                const diffX = Math.abs(currentPosX - this.initialMouseX);
                const diffY = Math.abs(currentPosY - this.initialMouseY);

                // swipe type
                if (this.swipeType == null) {
                    if (diffY > 5 || diffX > 5) {
                        if (diffY > diffX) {
                            this.swipeType = "v";
                        } else {
                            this.swipeType = "h";
                        }
                    }
                }

                // swipe
                if (this.swipeType == "h") {
                    // swipe wrapper
                    if (this.dir === "rtl") {
                        this.xSwipeWrapper = windowWidth * this.imgIndex + currentPosX - this.initialMouseX + 30 * this.imgIndex;
                    } else {
                        this.xSwipeWrapper = -(windowWidth * this.imgIndex) + currentPosX - this.initialMouseX - 30 * this.imgIndex;
                    }
                } else {
                    this.ySwipeWrapper = currentPosY - this.initialMouseY;
                }

                // mobile caseS
                if (event.type === "touchmove") {
                    this.endMouseX = this.getMouseXPosFromEvent(event);
                    this.endMouseY = this.getMouseYPosFromEvent(event);
                }
            }
        },

        // end swipe event
        endSwipe(event) {
            if (this.checkIfIsButton(event) && this.initialMouseX === 0) {
                return false;
            }

            // event check is dragging and swipe
            const self = this;
            const swipeType = this.swipeType;
            this.isDraggingSwipe = false;

            // horizontal swipe type
            if (this.initialMouseX === 0 && swipeType == "h") {
                return false;
            }

            // touch end fixes
            if (event.type !== "touchend") {
                this.endMouseX = this.getMouseXPosFromEvent(event);
                this.endMouseY = this.getMouseYPosFromEvent(event);
            } else {
                if (this.endMouseX === 0) {
                    return;
                }
            }

            // check if is dragged
            if (
                (this.endMouseX - this.initialMouseX === 0 && swipeType == "h") ||
                this.isZooming ||
                (this.endMouseY - this.initialMouseY === 0 && swipeType == "v")
            ) {
                return;
            }

            // set swipe animation
            this.setSwipeAnimation();

            // reset swipe data
            setTimeout(function() {
                self.IsSwipping = false;
                self.initialMouseX = 0;
                self.endMouseX = 0;
            }, 10);

            // type of swipe
            if (this.swipeType === "h") {
                // if the swipe is to the right
                if (this.endMouseX - this.initialMouseX < -40) {
                    if (this.dir === "rtl") {
                        return this.swipeToLeft();
                    }
                    return this.swipeToRight();
                }

                // if the swipe is to the left
                if (this.endMouseX - this.initialMouseX > 40) {
                    if (this.dir === "rtl") {
                        return this.swipeToRight();
                    }
                    return this.swipeToLeft();
                }
            }

            if (this.swipeType === "v") {
                const diffY = Math.abs(this.endMouseY - this.initialMouseY);

                // diff Y
                if (diffY >= 90) {
                    this.close();
                } else {
                    this.ySwipeWrapper = 0;
                }
            }

            this.swipeType = null;
            const windowWidth = this.lightboxInnerWidth;

            if (this.dir === "rtl") {
                this.xSwipeWrapper = this.imgIndex * windowWidth + 30 * this.imgIndex;
                return;
            }

            this.xSwipeWrapper = -this.imgIndex * windowWidth - 30 * this.imgIndex;
        },

        // swipe to left effect
        swipeToLeft() {
            if (!this.hasPrevious && this.effect === "swipe") {
                if (this.dir === "rtl") {
                    return (this.xSwipeWrapper = this.imgIndex * this.lightboxInnerWidth + 30 * this.imgIndex);
                }

                return (this.xSwipeWrapper = -this.imgIndex * this.lightboxInnerWidth - 30 * this.imgIndex);
            }

            this.changeIndexToPrev();
        },

        // swipe to right effect
        swipeToRight() {
            if (!this.hasNext && this.effect === "swipe") {
                if (this.dir === "rtl") {
                    return (this.xSwipeWrapper = this.imgIndex * this.lightboxInnerWidth + 30 * this.imgIndex);
                }

                return (this.xSwipeWrapper = -this.imgIndex * this.lightboxInnerWidth - 30 * this.imgIndex);
            }

            this.changeIndexToNext();
        },

        // function that return x position from event
        getMouseXPosFromEvent(event) {
            if (event.type.indexOf("mouse") !== -1) {
                return event.clientX;
            }
            return event.touches[0].clientX;
        },

        // function that return Y position from event
        getMouseYPosFromEvent(event) {
            if (event.type.indexOf("mouse") !== -1) {
                return event.clientY;
            }
            return event.touches[0].clientY;
        },

        // check if the image is cached
        is_cached(src) {
            var image = new Image();
            image.src = src;

            return image.complete;
        },

        // image loaded event
        imageLoaded() {
            this.imageLoading = false;
        },

        // get video url
        itemThumb(itemUrl, itemIndex) {
            var thumb = this.getItemThumb(itemIndex);
            if (thumb) {
                return thumb;
            }

            var youtubeID = this.getYoutubeID(itemUrl);
            if (youtubeID) {
                return "https://img.youtube.com/vi/" + youtubeID + "/mqdefault.jpg";
            }

            var vimeoID = this.getVimeoID(itemUrl);
            if (vimeoID) {
                return false;
            }

            return itemUrl;
        },

        itemThumbPost(itemUrl, itemIndex) {
            var thumb = this.getItemThumbPost(itemIndex);
            if (thumb) {
                return thumb.replace("link:", "");
            }

            return itemUrl;
        },

        isItemPicture(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                return item.picture;
            }

            return false;
        },

        getPictureSources(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const picture = this.items[imgIndex].picture;

            return picture.sources ? picture.sources : [];
        },

        // get item src
        getItemSrc(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                var final = item[this.srcName];
                return final;
            }

            return item;
        },

        getItemLinkSrc(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                var final = item[this.srcName];
                var _tmp = final.replace("link:", "");
                return _tmp;
            }

            return item;
        },

        getItemSrcSet(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                return item[this.srcSetName];
            }

            return null;
        },

        getItemSizes(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                return item.sizes;
            }

            return null;
        },

        getItemAlt(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                return item.alt;
            }

            return null;
        },

        getItemThumb(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];
            if (this.checkIfIsObject(imgIndex)) {
                return item[this.srcThumb];
            }

            if (this.getVideoUrl(item)) {
                return false;
            }

            return item;
        },

        getItemThumbPost(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            const item = this.items[imgIndex];

            let itemthumb = "";
            try {
                itemthumb = item.thumbnail;
            } catch (ex) {

            }

            return itemthumb;
        },


        // get item media type
        getMediaType(imgIndex) {
            if (imgIndex === null) {
                return false;
            }

            if (this.checkIfIsObject(imgIndex)) {
                const item = this.items[imgIndex];

                //item type is specified, so return it
                if (item[this.srcMediaType]) {
                    return item[this.srcMediaType];
                }
            }

            if (this.getVideoUrl(this.getItemSrc(imgIndex))) {
                return "video";
            } else if (this.getPDFurl(this.getItemSrc(imgIndex))) {
                return "iframe";
            } else if (this.getPageurl(this.getItemSrc(imgIndex))) {
                return "link";
            } else {
                return "image";
            }

            return item;
        },

        // toggle play slideshow event
        togglePlaySlideshow() {
            if (!this.slideshow) {
                return false;
            }

            if (!this.hasNext && !this.loopData) {
                return false;
            }
            this.isPlayingSlideShow = !this.isPlayingSlideShow;

            // if is playing move if not stop it
            if (this.isPlayingSlideShow) {
                this.move();
            } else {
                this.stopSlideShow();
            }
        },

        // stop slideshow
        stopSlideShow() {
            this.isPlayingSlideShow = false;
            clearInterval(this.intervalProgress);
            this.stylesInterval = {
                transform: "scaleX(0)",
                transition: "none"
            };
        },

        // move event in zoom
        move() {
            const self = this;
            this.progressWidth = 100;
            this.intervalProgress = setInterval(frame, this.slideshowDuration + 90);

            self.stylesInterval = {
                transform: "scaleX(1)",
                background: this.slideshowColorBar,
                "transition-duration": this.slideshowDuration + "ms"
            };

            function frame() {
                self.stylesInterval = {
                    transform: "scaleX(0)",
                    transition: "none"
                };

                if (self.dir === "rtl") {
                    self.onPrevClick(true);
                } else {
                    self.onNextClick(true);
                }

                if (!self.hasNext && !self.loopData) {
                    self.stopSlideShow();
                } else {
                    setTimeout(function() {
                        self.stylesInterval = {
                            transform: "scaleX(1)",
                            background: self.slideshowColorBar,
                            "transition-duration": self.slideshowDuration + "ms"
                        };
                    }, 50);
                }
            }
        },

        // show buttons event
        showButtons(event) {
            if (this.disableZoom && !this.checkIfIsButton(event)) {
                const self = this;
                setTimeout(function() {
                    self.buttonsVisible = !self.buttonsVisible;
                }, 100);
            }
        },

        // check if is allowed to drag
        checkMouseEventPropButton(button) {
            if (!this.isZooming) return false;
            // mouse left btn click
            return button === 0;
        },

        // handle mouse down event
        handleMouseDown(e) {
            if (!((e.type === "touchstart" && this.isZooming) || (e.type === "mousedown" && this.checkMouseEventPropButton(e.button)))) {
                return;
            }
            this.lastX = (e.type === "touchstart" ? e.touches[0] : e).clientX;
            this.lastY = (e.type === "touchstart" ? e.touches[0] : e).clientY;

            if (this.isZooming) {
                this.isDraging = true;
            }

            e.stopPropagation();
        },

        // handle mouse up event
        handleMouseUp(e) {
            if (!((e.type === "touchend" && this.isZooming) || (e.type === "mouseup" && this.checkMouseEventPropButton(e.button)))) {
                return;
            }
            this.isDraging = false;
            this.lastX = this.lastY = 0;

            // Fix drag zoom out
            const thisContext = this;
            setTimeout(function() {
                thisContext.canZoom = true;
            }, 100);
        },

        // handle mouse leave event
        handleMouseLeave(e) {
            if (!(e.type === "mouseleave" && this.isZooming)) {
                return;
            }
            this.isDraging = false;
            this.lastX = this.lastY = 0;

            // Fix drag zoom out
            const thisContext = this;
            setTimeout(function() {
                thisContext.canZoom = true;
            }, 100);
        },

        // handle mouse move event
        handleMouseMove(e) {
            if (!((e.type === "touchmove" && this.isZooming) || (e.type === "mousemove" && this.checkMouseEventPropButton(e.button)))) {
                return;
            }
            if (this.isDraging) {
                const clientX = (e.type === "touchmove" ? e.touches[0] : e).clientX;
                const clientY = (e.type === "touchmove" ? e.touches[0] : e).clientY;
                this.top = this.top - this.lastY + clientY;
                this.left = this.left - this.lastX + clientX;
                this.lastX = clientX;
                this.lastY = clientY;
                this.canZoom = false;

                const item = e.target.parentNode.nodeName === "PICTURE" ? e.target.parentNode.parentNode : e.target.parentNode;
                const newZoom = 1.6 + this.zoomBar / 10;
                item.style.transform =
                    "translate3d(calc(-50% + " +
                    this.left +
                    "px), calc(-50% + " +
                    this.top +
                    "px), 0px) scale3d(" +
                    newZoom +
                    ", " +
                    newZoom +
                    ", " +
                    newZoom +
                    ")";
            }
            e.stopPropagation();
        },

        // zoom image event
        zoomImage(indexImage) {
            if (this.disableZoom) {
                return false;
            }

            if (!this.canZoom) {
                return false;
            }

            if (this.IsSwipping) {
                return false;
            }

            // item zoom
            let item;
            if (this.effect == "swipe") {
                item = this.$refs.items[this.imgIndex].childNodes[0];
            } else {
                item = this.$refs.items.childNodes[0];
            }

            // zoom variables
            const isZooming = this.isZooming;
            const thisContext = this;

            // Is zooming check
            if (isZooming) {
                if (!this.isDraging) {
                    this.isZooming = false;
                    this.zoomBar = 0;
                }
            } else {
                this.isZooming = true;
            }

            // check if is zooming
            if (this.isZooming) {
                this.stopSlideShow();

                // add scale
                item.style.transform = "translate3d(calc(-50%), calc(-50%), 0px) scale3d(1.6, 1.6, 1.6)";

                // hide buttons
                this.buttonsVisible = false;

                // fix drag transition problems
                setTimeout(function() {
                    thisContext.transition = "all .0s ease";
                }, 100);
            } else {
                // show buttons
                this.buttonsVisible = true;
                this.resetZoom();
            }
        },

        // Reset zoom data
        resetZoom() {
            this.scale = 1;
            this.left = 0;
            this.top = 0;
            this.zoomBar = 0;
            this.isZooming = false;
            this.swipeType = null;
            this.transition = "all .3s ease";

            // only if index is not null
            if (this.imgIndex != null) {
                let item;
                if (this.effect == "swipe") {
                    item = this.$refs.items[this.imgIndex].childNodes[0];
                } else {
                    item = this.$refs.items.childNodes[0];
                }

                // reset styles
                if (this.disableZoom) {
                    item.style.transform = "translate3d(calc(-50% + " + this.left + "px), calc(-50% + " + this.top + "px), 0px)";
                } else {
                    item.style.transform = "translate3d(calc(-50% + " + this.left + "px), calc(-50% + " + this.top + "px), 0px) scale3d(1, 1, 1)";
                }

                this.initialMouseX = 0;
                if (window.innerWidth >= 700) {
                    this.buttonsVisible = true;
                }
            }
        },

        // Aspect Ratio responsive video
        setAspectRatioVideo() {
            const thisContext = this;
            let el = document.getElementsByClassName("aero-lightbox__inner");
            el = el[0];

            let computedStyle = getComputedStyle(el);
            if (window.innerWidth < 1440) {
                let width = el.clientWidth;
                let height = Math.round((width / 16) * 9);

                this.aspectRatioVideo.height = height + "px";
                this.aspectRatioVideo.width = width + "px";
            } else {
                setTimeout(function() {
                    let height = el.clientHeight;
                    height -= parseFloat(computedStyle.paddingTop) + parseFloat(computedStyle.paddingBottom);

                    let width = (height / 9) * 16;

                    thisContext.aspectRatioVideo.height = height + "px";
                    thisContext.aspectRatioVideo.width = width + "px";
                }, 150);
            }
        },

        // close event
        close() {
            this.stopSlideShow();
            this.swipeType = null;
            this.$emit("close", this.imgIndex);
            this.showThumbs = false;
            this.imgIndex = null;
        },

        wheelEvent(event) {
            const delay = 350;
            const currentTime = new Date().getTime();
            let direction = event.deltaY > 0 ? "top" : "down";

            if (currentTime - this.prevTime < delay) return;

            this.prevTime = currentTime;

            switch (direction) {
                case "top":
                    return this.changeIndexToPrev();
                    break;
                case "down":
                    return this.changeIndexToNext();
            }
        },

        // close event click outside
        closeModal(event) {
            if (!this.closeOnClickOutsideMobile) {
                if (window.innerWidth < 700) {
                    return false;
                }
            }

            if (this.IsSwipping) {
                return false;
            }

            var elements =
                ".aero-lightbox__iframe, .aero-lightbox__iframe *, .aero-lightbox-zoom, .aero-lightbox-zoom *, .aero-lightbox-thumbs, svg, path, rect, .aero-lightbox-thumbs *, .aero-lightbox-button, .aero-lightbox-toolbar__btn, .aero-lightbox-toolbar__btn *, .aero-lightbox-button *, .aero-lightbox__slide__img *, .aero-lightbox-video, .aero-lightbox-caption h6, .aero-lightbox-caption p, .aero-lightbox-caption a";
            if (!event.target.matches(elements)) {
                this.close();
            }
        },

        // set swipe animation
        setSwipeAnimation() {
            const self = this;
            clearInterval(this.swipeInterval);
            this.swipeAnimation = null;

            // animation swipe
            this.swipeAnimation = "all .3s ease";
            this.swipeInterval = setInterval(interval, 330);

            function interval() {
                self.swipeAnimation = null;
            }
        },

        // next slide event
        onNextClick(isFromSlideshow = false) {
            if (this.isZooming) {
                return false;
            }

            if (!isFromSlideshow) {
                this.stopSlideShow();
            }

            this.setSwipeAnimation();

            if (this.dir === "rtl") {
                return this.changeIndexToPrev();
            }

            this.changeIndexToNext();
        },

        // prev slide event
        onPrevClick(isFromSlideshow = false) {
            if (this.isZooming) {
                return false;
            }

            if (!isFromSlideshow) {
                this.stopSlideShow();
            }

            this.setSwipeAnimation();

            if (this.dir === "rtl") {
                return this.changeIndexToNext();
            }

            this.changeIndexToPrev();
        },

        // change to next index
        changeIndexToNext() {
            if (this.hasNext) {
                this.onIndexChange(this.imgIndex + 1);
            } else {
                // only if has loop prop
                if (this.loopData) {
                    this.onIndexChange(0);
                }
            }
        },

        // change to prev index
        changeIndexToPrev() {
            if (this.hasPrevious) {
                this.onIndexChange(this.imgIndex - 1);
            } else {
                // only if has loop prop
                if (this.loopData) {
                    this.onIndexChange(this.items.length - 1);
                }
            }
        },

        // set lightbox inner width
        setLightboxInnerWidth() {
            let el = document.getElementsByClassName("aero-lightbox__inner");
            let width = el[0].clientWidth;
            this.lightboxInnerWidth = width;
        },

        // x position on resize event
        xPositionOnResize() {
            this.setLightboxInnerWidth();
            const index = this.imgIndex;

            if (this.dir === "rtl") {
                this.xSwipeWrapper = index * this.lightboxInnerWidth + 30 * index;
                return;
            }

            // set x position
            this.xSwipeWrapper = -index * this.lightboxInnerWidth - 30 * index;
        },

        // set x position by img index
        setXPosition(index) {
            if (this.dir === "rtl") {
                this.xSwipeWrapper = index * this.lightboxInnerWidth + 30 * index;
                return;
            }

            // set x position
            this.xSwipeWrapper = -index * this.lightboxInnerWidth - 30 * index;
            return;
        },

        // index change
        onIndexChange(index) {
            const self = this;
            this.imgIndex = index;
            this.$emit("on-change", index);

            setTimeout(function() {
                self.$emit("on-change-end", index);
            }, 400);
        },

        // caption size
        addCaptionPadding() {
            if (this.checkIfIsObject(this.imgIndex) && (this.items[this.imgIndex].title || this.items[this.imgIndex].description)) {
                const el = document.getElementsByClassName("aero-lightbox-caption");
                if (el.length > 0) {
                    this.paddingBottom = el[0].offsetHeight;
                }
            } else {
                this.paddingBottom = 60;
            }
        },

        getPDFurl(url) {
            if (this.imgIndex === null) {
                return false;
            }

            const str = new String(url);
            if (str.endsWith(".pdf")) {
                return url;
            }

            return false;
        },

        getPageurl(url) {
            if (this.imgIndex === null) {
                return false;
            }

            const str = new String(url);
            if (str.startsWith("link:")) {
                var cleanlink = str.replace("link:", "");
                return cleanlink;
            }

            return false;
        },

        // check if is video
        getVideoUrl(itemSrc) {
            const youtubeUrl = this.getYoutubeUrl(itemSrc);
            const vimeoUrl = this.getVimeoUrl(itemSrc);
            const mp4Url = this.checkIsMp4(itemSrc);

            if (youtubeUrl) {
                return youtubeUrl;
            }

            if (vimeoUrl) {
                return vimeoUrl;
            }

            if (mp4Url) {
                return mp4Url;
            }

            return false;
        },

        // getYoutube ID
        getYoutubeID(url) {
            // youtube data
            const youtubeRegex = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            const ytId = url.match(youtubeRegex) ? RegExp.$1 : false;

            if (ytId) {
                return ytId;
            }

            return false;
        },

        // get youtube url
        getYoutubeUrl(url) {
            // youtube data
            const ytId = this.getYoutubeID(url);

            // if is youtube video
            if (ytId) {
                // check if allows youtube cookies
                if (this.youtubeCookies) {
                    return "https://www.youtube.com/embed/" + ytId;
                }

                return "https://www.youtube-nocookie.com/embed/" + ytId;
            }

            return false;
        },

        // vimeo ID
        getVimeoID(url) {
            // if is vimeo video
            const result = url.match(
                /(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/i
            );
            if (result !== null) {
                return result[1];
            }

            return false;
        },

        // get vimeo url
        getVimeoUrl(url) {
            // if is vimeo video
            const result = url.match(
                /(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/i
            );
            if (result !== null) {
                return "//player.vimeo.com/video/" + result[1] + "?hd=1&show_title=1&show_byline=1&show_portrait=0&fullscreen=1";
            }

            return false;
        },

        // check if video is mp4
        checkIsMp4(url) {
            if (this.imgIndex === null) {
                return false;
            }

            const str = new String(url);
            const videoExtensions = [".mp4", ".mov", ".webm", ".ogg", ".avi"];
            const isValidExtension =
                videoExtensions.filter((videoExtension) => {
                    return str.indexOf(videoExtension) !== -1 || str.indexOf(videoExtension.toUpperCase()) !== -1;
                }).length > 0;
            if (isValidExtension) {
                return url;
            }

            return false;
        },

        // if is video get extension
        getVideoExt(url) {
            if (this.imgIndex === null) {
                return false;
            }

            const str = new String(url);
            if (str.indexOf(".mp4") !== -1 || str.indexOf(".mov") !== -1) {
                return "mp4";
            }

            if (str.indexOf(".webm") !== -1) {
                return "webm";
            }

            if (str.indexOf(".ogg") !== -1) {
                return "ogg";
            }

            if (str.indexOf(".avi") !== -1) {
                return "avi";
            }

            return false;
        },

        // check if item is object
        checkIfIsObject(itemIndex) {
            const item = this.items[itemIndex];
            if (typeof item === "object" && item !== null) {
                return true;
            }
            return false;
        },

        // arrows and escape events
        eventListener(e) {
            switch (e.keyCode) {
                case 39:
                    return this.onNextClick();
                case 37:
                    return this.onPrevClick();
                case 38:
                case 40:
                case " ":
                    return e.preventDefault();
                case 27:
                    return this.close();
            }
        }
    },

    computed: {
        // Images wrapper styles to use drag and zoom
        imgWrapperStyle() {
            return {
                top: "50%",
                left: "50%",
                transition: this.transition
            };
        },

        // lightbox styles
        lightboxStyles() {
            return {
                "z-index": this.zIndex,
                "background-color": this.overlayColor
            };
        },

        innerStyles() {
            return {
                "padding-bottom": this.paddingBottom + "px"
            };
        },

        // get item src
        itemSrc() {
            if (this.imgIndex === null) {
                return false;
            }

            const item = this.items[this.imgIndex];
            if (this.checkIfIsObject(this.imgIndex)) {
                return item[this.srcName];
            }

            return item;
        },

        // Lightbox classes
        lightboxClasses() {
            let classesReturn = [{
                    "aero-lightbox--can-zoom": this.canZoom && !this.disableZoom
                },
                {
                    "aero-lightbox--zoom-disabled": this.disableZoom
                },
                {
                    "aero-lightbox--is-zooming": this.isZooming
                },
                {
                    "aero-lightbox--show-thumbs": this.showThumbs
                },
                {
                    "aero-lightbox--is-swipping": this.isDraggingSwipe
                }
            ];

            let classString = "aero-lightbox--thumbs-" + this.thumbsPosition;
            classesReturn.push(classString);

            return classesReturn;
        },

        // Buttons classes
        buttonsClasses() {
            return {
                hidden: !this.buttonsVisible
            };
        },

        // check if the slide has next element
        hasNextButton() {
            if (this.dir === "rtl") {
                return this.imgIndex - 1 >= 0;
            }

            return this.imgIndex + 1 < this.items.length;
        },

        // check if the slide has previous element
        hasPreviousButton() {
            if (this.dir === "rtl") {
                return this.imgIndex + 1 < this.items.length;
            }

            return this.imgIndex - 1 >= 0;
        },

        // check if the slide has next element
        hasNext() {
            return this.imgIndex + 1 < this.items.length;
        },

        // check if the slide has previous element
        hasPrevious() {
            return this.imgIndex - 1 >= 0;
        }
    }
};