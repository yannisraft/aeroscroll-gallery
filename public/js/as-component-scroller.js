let VScrollerRow = await import("./aeroscroll-scrollerrow-component.js?v=" + window["AEROSCROLL_GALLERY_ITERATION"]).then(
    (module) => module?.default
);

let VScrollerGridColumn = await import("./aeroscroll-scrollergridcolumn-component.js?v=" + window["AEROSCROLL_GALLERY_ITERATION"]).then(
    (module) => module?.default
);




let VScrollerJustifiedRow = await import("./aeroscroll-scrollerjustifiedrow-component.js?v=" + window["AEROSCROLL_GALLERY_ITERATION"]).then(
    (module) => module?.default
);
let VScrollerScrollBar = await import("./aeroscroll-scrollerscrollbar-component.js?v=" + window["AEROSCROLL_GALLERY_ITERATION"]).then(
    (module) => module?.default
);

const {
    ref,
    watch,
    onMounted,
    onBeforeUpdate,
    computed
} = Vue;

import {
    ASScroller
} from "./as-scroller.js";
import {
    GetObjectMinKeyByIndex,
    GetObjectMaxKeyByIndex
} from "./utils/objectutils.js";

import {
    ASCredit
} from "./as-scroller-tm.js";

export default {
    template: `
        <div :id="scrollerId" class="scroller noselect">
            <div :id="viewportId" class="scroller-viewport" :style="{ 'background-color': color_bg, 'padding-right': sidegap + 'px' }">
                <div :id="containerId" v-if="layout === 'grid'" :key="renderKey" class="scroller-container" :style="GetContainerStyle()">
                    <VScrollerGridColumn
                        v-for="(col, colkey) in data"
                        :key="colkey"
                        :col="col"
                        :cellgap="cellgap"
                        :theme="theme"
                        :containerheight="GetContainerHeight()"
                        :numcolumns="numcolumns"                        
                        :orientation="orientation">
                        <template v-slot:cell="slotProps">
                            <slot name="cell" :data="slotProps.data">
                                <span>{{ slotPropss.data.index }}</span>
                            </slot>
                        </template>
                    </VScrollerGridColumn>
                </div>
                 
                <div :id="containerId" v-if="layout === 'justified'" :key="renderKey" class="scroller-container" :style="GetContainerStyle()">
                    <VScrollerJustifiedRow
                        v-for="(row, rowkey) in data"
                        :key="rowkey"
                        :row="row"
                        :rowheight="cellSize"
                        :cellgap="cellgap"
                        :theme="theme"
                        :containerheight="GetContainerHeight()"
                        :numcolumns="numcolumns"
                        >
                        <template v-slot:cell="slotProps">
                            <slot name="cell" :data="slotProps.data">
                                <span>{{ slotPropss.data.index }}</span>
                            </slot>
                        </template>
                    </VScrollerJustifiedRow>
                </div>
                <as-credit v-if="poweredbyactive === 1 || poweredbyactive === '1' || poweredbyactive === true"></as-credit>
            </div>
            <div v-if="overlay_visible" class="aeroscroll-overlay-notification">            
                <img :src="GetEmbarassedImageUrl()" class="aeroscroll-overlay-icon"/>
                <div class="aeroscroll-overlay-message">{{ overlay_message }}</div>
            </div>
            <div v-if="!isready" class="aeroscroll-overlay">      
                <div class="spinner"></div><span class="isready-loading-label">Loading...</span>
            </div>
            <VScrollerScrollBar
                :active="hasScrollbar"
                
                :mode="isInfinite ? 'infinite' : 'normal'"
                @onChange="OnScrollBarChanged"
                @onBackwardsClicked="onBackwardsClicked"
                @onForwardClicked="onForwardClicked"
                :viewportId="viewportId"
            />           
        </div>
    `,
    name: "VScroller",
    components: {
        VScrollerRow,
        VScrollerScrollBar,
        VScrollerGridColumn,

        VScrollerJustifiedRow
    },
    props: {
        modelValue: {
            type: Object,
            default: () => {
                return {};
            }
        },
        height: {
            type: Number,
            default: 400
        },

        theme: {
            type: String,
            default: "image"
        },
        color_bg: {
            type: String,
            default: ""
        },
        layout: {
            type: String,
            default: ""
        },
        numcolumns: {
            type: Number,
            default: 3
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
        mousewheelenabled: {
            type: Boolean,
            default: true
        },
        cellSize: {
            type: Number,
            default: 50
        },
        cellSquared: {
            type: Boolean,
            default: false
        },
        scrollSpeed: {
            type: Number,
            default: 6
        },
        isInfinite: {
            type: Boolean,
            default: false
        },
        hasScrollbar: {
            type: Boolean,
            default: true
        },
        rootID: {
            type: String,
            default: ""
        },
        hoveranimation: {
            type: String,
            default: "none"
        },
        isready: {
            type: Boolean,
            default: false
        },
        totalDataAvailable: {
            type: Number,
            default: 0
        },
        type: {
            type: String,
            default: ""
        },

        poweredbyactive: {
            type: Boolean,
            default: true
        }
    },
    setup(props, context) {
        const asScroller = new ASScroller();

        let IDGenerated = Math.floor(Math.random() * 999999) + 1000000;

        // ---- Reactive Attributes
        const scrollerId = ref("scroller_" + IDGenerated);
        const containerId = ref("container_" + IDGenerated);
        const viewportId = ref("viewport_" + IDGenerated);
        let cellWidthVal = ref(props.cellSize);
        let cellHeightVal = ref(props.cellSize);
        let translatePosition = ref(0);
        let scrollbarPosition = ref(0);
        let calculatedRowSize = ref(props.cellSize);

        const cellsdata = ref({});
        const cells = ref([]);
        const divs = ref([]);
        const alldata = ref({});

        let overlay_visible = ref(false);
        let overlay_message = ref("");

        let celldataindex_backward = 0;
        let celldataindex_forward = 0;

        // ---- Attributes
        let renderKey = 0;
        let translatePositionPrevious = translatePosition.value;
        const indexInitial = 1;
        let indexForward = indexInitial;
        let indexBackward = indexInitial - 1;
        let indexRowForward = 0;
        let indexRowBackward = 0;
        let indexLeftForward = 0;
        let indexLeftBackward = 0;
        let indexCacheRowForward = 0;
        let indexCacheRowBackward = 0;

        let momentumID = "";
        let velocityCurrent = 0;
        let maxVelocity = 30;
        let velocityMomentum = 0.95;
        let timeSinceLastScroll = 0;
        let container = null;
        let viewport = null;
        let translatePositionString = "Y";
        let mouseDown = false;
        let mouseMoving = false;
        let dragScrollStartPosition = translatePosition.value;
        let dragScrollPreviousPosition = translatePosition.value;
        let mouseDownStartPosition = 0;
        let scrollInterval = null;
        let scrolldirection = 0;
        let previousscrolldirection = 0;
        let finalScrollSpeed = props.scrollSpeed;
        let scrollercache = {};
        var cellSizeOriented = cellHeightVal.value;
        let cellsNumberOriented = 0;
        let updateTime = 5;
        let resizeWindowTimeout = null;
        let resizeWindowData = {};
        let currentRowElement = null;
        let shouldBounceBackBackward = false;
        let viewportSize = 0;
        let canUpdateCache = true;
        let MINCELLSREQUIRED = 1;
        let cellsPerPage = 0;
        let previousUpdateDirection = 0; // 0: None, 1: Forward, -1: Backward
        let previouslastElement = null;
        let previousPercent = 0;

        let canRerun = true;
        let canRerunUpdateNext = true;
        let canRerunUpdatePrevious = true;





        // ---- Methods Public
        //
        function GetContainerHeight() {
            let finalHeight = props.height + "px";
            if (props.height === -1) {
                finalHeight = "100%";
            }
            return finalHeight;
        }

        function OnScrollBarChanged(newvalue) {
            finalScrollSpeed = props.scrollSpeed;
            if (newvalue <= 40 || newvalue > 60) {
                finalScrollSpeed = props.scrollSpeed * 3;
            }

            if (newvalue <= 20 || newvalue > 80) {
                finalScrollSpeed = props.scrollSpeed * 10;
            }

            scrolldirection = 0;
            if (newvalue < 50) {
                // Move Backwards
                scrolldirection = -1;
                previousscrolldirection = -1;


            } else if (newvalue > 50) {
                // Move Forward
                scrolldirection = 1;
                previousscrolldirection = 1;


            }

            if (scrolldirection !== 0) {

                if (scrollInterval === null) {
                    scrollInterval = setInterval(() => {
                        UpdatePosition();
                    }, 10);
                }
            } else {
                clearInterval(scrollInterval);
                scrollInterval = null;


            }
        }

        // ==========================================================================================================
        // ==========================================================================================================
        // ==========================================================================================================

        /**
         * * LINK ----- A N I M A T I O N
         **/

        // LINK: Animate Scroller
        function AnimateScroller() {
            if (container && !overlay_visible.value) {
                container.style.transform = "translate" + translatePositionString + "(" + translatePosition.value + "px)";

                if (translatePosition.value == translatePositionPrevious) {
                    previousscrolldirection = 1;
                } else if (translatePosition.value > translatePositionPrevious) {
                    previousscrolldirection = -1;
                }

                if (velocityCurrent !== 0) scrolldirection = -Math.sign(velocityCurrent);

                DetectEdges(scrolldirection);

                translatePositionPrevious = translatePosition.value;

                // Test Animation Func
                // NEW
                var list = document.getElementsByClassName("scroller-row-v");
                var lastElement = list[list.length - 1];
                var last = list[list.length - 1];

                if (last) {
                    last = last.getBoundingClientRect();
                    let cellSizeWithGap = calculatedRowSize.value;
                    var difff = -indexRowBackward * cellSizeWithGap - translatePosition.value - viewportSize;
                    var percent = Math.abs(difff / (calculatedRowSize.value - (0 * calculatedRowSize.value) / 100));
                    percent = 1 - percent + 0.7;
                    percent = Math.min(Math.max(percent, 0.6), 1);
                }
            }
        }



        function CancelMomentumTracking() {
            cancelAnimationFrame(momentumID);
        }

        function BeginMomentumTracking() {
            CancelMomentumTracking();
            momentumID = requestAnimationFrame(MomentumLoop);
        }

        function MomentumLoop() {
            translatePosition.value += velocityCurrent;



            velocityCurrent *= velocityMomentum;
            if (Math.abs(velocityCurrent) > 0.3) {
                momentumID = requestAnimationFrame(MomentumLoop);
            } else {
                velocityCurrent = 0;
                scrolldirection = 0;


            }
        }

        function onBackwardsClicked() {
            scrolldirection = -1;
            previousscrolldirection = -1;
            finalScrollSpeed = props.scrollSpeed * 3;

            UpdatePosition();
            BeginMomentumTracking();

            scrolldirection = 0;
            finalScrollSpeed = props.scrollSpeed;
        }

        function onForwardClicked() {
            scrolldirection = 1;
            previousscrolldirection = 1;
            finalScrollSpeed = props.scrollSpeed * 3;

            UpdatePosition();
            BeginMomentumTracking();

            finalScrollSpeed = props.scrollSpeed;
        }

        function UpdatePosition() {
            const walk = scrolldirection * finalScrollSpeed;
            const _translatePositionPrevious = translatePosition.value;
            translatePosition.value += -walk;
            velocityCurrent = translatePosition.value - _translatePositionPrevious;
        }

        // ==========================================================================================================
        // ==========================================================================================================
        // ==========================================================================================================

        /**
         * * LINK ----- C E L L S   G E N E R A T I O N
         **/

        // Forward is added like this
        /*
        1 2 3
        4 5 6
        7 8 ....
        
        so Backwards should be
            ...-3
        -2 -1 0
        1  2  3
        */
        // LINK DetectEdges
        function DetectEdges(direction) {
            if (viewport) {
                let cellSizeWithGap = 0;
                cellSizeWithGap = calculatedRowSize.value;

                var diff = 0;
                let diffcache = 0;

                if (direction === -1) {
                    // Backwards

                    if (props.layout === "grid") {
                        asScroller.grid.AddPreviousCells(context, celldataindex_forward);
                    } else if (props.layout === "justified") {
                        asScroller.justified.AddPreviousCells(context, cellSizeOriented);
                    }
                } else if (direction === 1) {
                    // Forward
                    if (props.layout === "grid") {
                        asScroller.grid.AddNextCells(context, celldataindex_backward);
                    } else if (props.layout === "justified") {
                        asScroller.justified.AddNextCells(context, cellSizeOriented);
                    }
                }
            }
        }

        /**
         *
         * @param {*} first The initial data for cells generation
         */
        // LINK GenerateInitialCells
        function GenerateInitialCells(first) {
            if (props.layout === "grid") {
                asScroller.grid.CreateColumns();
                asScroller.grid.GenerateInitialDummyCells(first);
                asScroller.ChangeViewportPositionAtInitialization();
            } else if (props.layout === "justified") {
                asScroller.justified.GenerateInitialDummyCells(first, cellsPerPage, cellSizeOriented);
                asScroller.ChangeViewportPositionAtInitialization(cellSizeOriented);
            }
        }

        // ==========================================================================================================
        // ==========================================================================================================
        // ==========================================================================================================

        /**
         * * ----- LINK  E V E N T S
         **/

        // ---- Events
        var app = document.getElementById(props.rootID);
        app.addEventListener("wheel", (e) => {
            if (props.usemousewheel && props.mousewheelenabled && props.isready) {


                e.preventDefault();
                e.stopPropagation();

                var timediff = Date.now() - timeSinceLastScroll;
                finalScrollSpeed = props.scrollSpeed * 2;
                if (timediff < 10) {
                    finalScrollSpeed = props.scrollSpeed * 6;
                }

                const deltaY = Math.sign(e.deltaY);
                const walk = deltaY * finalScrollSpeed;

                const _translatePositionPrevious = translatePosition.value;

                if (!shouldBounceBackBackward) {
                    translatePosition.value += -walk;
                    velocityCurrent = translatePosition.value - _translatePositionPrevious;
                    scrolldirection = -Math.sign(velocityCurrent);
                    BeginMomentumTracking();
                }

                if (!props.isInfinite) {
                    if (translatePosition.value > 0) {
                        shouldBounceBackBackward = true;
                    }
                }

                timeSinceLastScroll = Date.now();

                return false;
            }
        });

        window.addEventListener("resize", (e) => {
            console.log("RESIZE");
            if (props.cellSquared) {
                if (Object.keys(alldata.value).length > 0) resizeWindowData = Object.assign({}, alldata.value);
                alldata.value = {};
                calculatedRowSize.value = viewport.clientWidth / cellsNumberOriented;



                clearTimeout(resizeWindowTimeout);

                resizeWindowTimeout = setTimeout(() => {
                    alldata.value = resizeWindowData;
                }, 500);
            }
        });

        function AddMouseEvents() {
            // Add Container Events after its creation
            viewport.addEventListener("mousedown", (e) => {
                mouseDown = true;
                dragScrollStartPosition = translatePosition.value;
                dragScrollPreviousPosition = e.pageY;
                mouseDownStartPosition = e.pageY;


            });

            document.addEventListener("mouseup", (e) => {
                if (!mouseMoving) context.emit("on-stop-dragging");

                if (!shouldBounceBackBackward && mouseMoving) {
                    if (Math.abs(velocityCurrent) > maxVelocity) velocityCurrent = maxVelocity * Math.sign(velocityCurrent);
                    BeginMomentumTracking();
                }

                mouseDown = false;
                mouseMoving = false;
            });

            document.addEventListener("mousemove", (e) => {
                if (!mouseDown) return;

                e.preventDefault();
                mouseMoving = true;


                let moveSpeed = 1;
                var walk = e.pageY - dragScrollPreviousPosition;


                dragScrollPreviousPosition = e.pageY;


                velocityCurrent = walk;
                translatePosition.value += walk * moveSpeed;

                if (!props.isInfinite) {
                    if (translatePosition.value > 0 && !shouldBounceBackBackward) {
                        shouldBounceBackBackward = true;
                    }
                }

                context.emit("on-dragging");
            });

            viewport.addEventListener("mouseleave", (e) => {
                mouseDown = false;
            });
        }

        function AddTouchEvents() {
            console.log("AddTouchEvents");

            // Add Container Events after its creation
            viewport.addEventListener(
                "touchstart",
                (e) => {
                    console.log("ontouchstart");
                    mouseDown = true;

                    var touch = e.touches[0];

                    dragScrollStartPosition = translatePosition.value;
                    dragScrollPreviousPosition = touch.pageY;
                    mouseDownStartPosition = touch.pageY;


                }, {
                    passive: false
                }
            );

            document.addEventListener(
                "touchend",
                (e) => {
                    console.log("touchend");
                    if (!mouseMoving) context.emit("on-stop-dragging");
                    mouseDown = false;
                    mouseMoving = false;


                    if (!shouldBounceBackBackward) {
                        if (Math.abs(velocityCurrent) > maxVelocity) velocityCurrent = maxVelocity * Math.sign(velocityCurrent);
                        BeginMomentumTracking();
                    }
                }, {
                    passive: false
                }
            );

            document.addEventListener(
                "touchmove",
                (e) => {
                    console.log("touchmove: ", e);
                    if (!mouseDown) return;

                    e.preventDefault();
                    mouseMoving = true;


                    var touch = e.touches[0];

                    let moveSpeed = 1;
                    var walk = touch.pageY - dragScrollPreviousPosition;


                    dragScrollPreviousPosition = touch.pageY;


                    console.log("walk: ", walk);

                    velocityCurrent = walk;
                    translatePosition.value += walk * moveSpeed;

                    if (!props.isInfinite) {
                        if (translatePosition.value > 0 && !shouldBounceBackBackward) {
                            shouldBounceBackBackward = true;
                        }
                    }

                    context.emit("on-dragging");
                }, {
                    passive: false
                }
            );

            viewport.addEventListener("touchcancel", (e) => {
                mouseDown = false;
            });
        }

        // ==========================================================================================================
        // ==========================================================================================================
        // ==========================================================================================================

        /**
         * * LINK ----- E X T R A   F U N C I O N S
         **/
        function BlockRerun() {
            canRerun = false;
            setTimeout(() => {
                canRerun = true;
            }, 0);
        }

        function GetContainerStyle() {
            var finalcontainerstyle = [{
                height: GetContainerHeight(),
                display: "flex",
                "flex-direction": "row"
            }];
            if (props.layout === "justified") {
                finalcontainerstyle = [{
                    height: GetContainerHeight(),
                    display: "block",
                    "flex-direction": "column"
                }];
            }



            return finalcontainerstyle;
        }

        function GetEmbarassedImageUrl() {
            var baseUrl = window["BASE_URL"];
            return baseUrl + "/wp-content/plugins/aeroscroll-gallery/public/images/icon-embarrassed.png";
        }

        function AddMadebyLogo() {
            var ascredit_element = document.getElementsByTagName('as-credit');

            if (ascredit_element.length > 0) {
                console.log("ascredit_element: ", ascredit_element[0]);
                ascredit_element[0].style.display = 'block !important;';
            }

        }

        const data = computed(() => {
            var finalvalue = null;
            if (props.layout === "grid") {
                finalvalue = asScroller.grid.data.value;
            } else if (props.layout === "justified") {
                finalvalue = asScroller.justified.data.value.rows;
            }
            return finalvalue;
        });
        /**
         * * LINK ----- O N   M O U N T E D
         **/
        onMounted(() => {
            console.log("onMounted");

            container = document.getElementById(containerId.value);
            viewport = document.getElementById(viewportId.value);

            asScroller.DefineParameters(
                props,
                container,
                viewport,
                overlay_visible.value,
                translatePositionString,
                translatePosition,
                velocityCurrent,
                previousscrolldirection
            );

            viewportSize = viewport.clientHeight;
            cellsNumberOriented = asScroller.GetTotalColumns();



            AddMouseEvents();
            AddTouchEvents();

            setInterval(() => {
                AnimateScroller();
            }, updateTime);



            MINCELLSREQUIRED = asScroller.GetInfiniteMinimumRequiredCells(cellSizeOriented, cellsNumberOriented);
            cellsPerPage = asScroller.GetPerPageRequireCells(cellSizeOriented, cellsNumberOriented);

            // To Avoid multiple requests for small MINCELLSREQUIRED enter a minimum of 20 items
            if (cellsPerPage < 20) {
                cellsPerPage = 20;
            }

            context.emit("on-initilized", {
                minCellsRequired: MINCELLSREQUIRED,
                cellsPerPage: cellsPerPage
            });



            console.log("poweredbyactive: ", props.poweredbyactive);
            if (props.poweredbyactive === 1) {
                AddMadebyLogo();
            }
        });

        // Make sure to reset the refs before each update.
        onBeforeUpdate(() => {
            divs.value = [];
        });

        // ---- Watchers
        /**
         * * LINK ----- W A T C H
         **/
        watch(
            () => props.totalDataAvailable,
            (first, second) => {
                asScroller.totalDataAvailable = props.totalDataAvailable;
            }
        );
        watch(
            () => props.modelValue,
            (first, second) => {
                if (first["state"]) {
                    if (first["state"] === "init") {
                        delete first["state"];

                        celldataindex_backward = 0;
                        celldataindex_forward = Object.keys(first).length - 1;

                        GenerateInitialCells(first);
                    } else if (first["state"] === "update") {
                        if (canRerun) {
                            BlockRerun();

                            delete first["state"];
                            let _indexFirst = first.indexFirst;
                            let _indexLast = first.indexLast;

                            let currentIndex = _indexFirst;
                            delete first["indexFirst"];
                            delete first["indexLast"];

                            asScroller.totalDataAvailable = first["totalposts"];
                            asScroller.totalPagesAvailable = first["totalpages"];
                            delete first["totalposts"];
                            delete first["totalpages"];

                            // Add new data to cellsdata_cache
                            asScroller.UpdateCellsCache(1, first, context);

                            // Add data to dummy cells from cache
                            asScroller.UpdateDummyCells(1);
                        }
                    } else if (first["state"] === "updatenextinit") {
                        delete first["state"];
                        let _indexFirst = first.indexFirst;
                        let _indexLast = first.indexLast;

                        delete first["indexFirst"];
                        delete first["indexLast"];

                        asScroller.UpdateIndexCache(true, Object.keys(first).length);
                        indexCacheRowForward += parseInt(Object.keys(first).length / cellsNumberOriented);

                        indexCacheBackward = indexBackward;
                        indexCacheRowBackward = indexRowBackward;

                    } else if (first["state"] === "updatenext") {} else if (first["state"] === "nodata") {
                        overlay_visible.value = true;
                        overlay_message.value = "Oops! No Data found! Please add some data to load!";
                        alldata.value = {};
                    }
                }
            }, {
                deep: true
            }
        );

        return {
            // Attributes
            data,
            scrollerId,
            containerId,
            viewportId,
            cellWidthVal,
            cellHeightVal,
            translatePosition,
            cellsdata,
            alldata,
            cells,
            divs,
            scrollbarPosition,
            calculatedRowSize,
            renderKey,
            overlay_visible,
            overlay_message,

            // Methods Public
            GetContainerHeight,
            OnScrollBarChanged,
            onBackwardsClicked,
            onForwardClicked,
            GetContainerStyle,
            GetEmbarassedImageUrl
        };
    }
};