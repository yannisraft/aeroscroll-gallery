let {
    ASScrollerJustified
} = await import("./as-scroller-justified.js?v=" + window["AEROSCROLL_GALLERY_ITERATION"]);
let {
    ASScrollerGrid
} = await import("./as-scroller-grid.js?v=" + window["AEROSCROLL_GALLERY_ITERATION"]);

const {
    ref
} = Vue;

export class ASScroller {
    constructor() {
        this.props = null;
        this.container = null;
        this.viewport = null;
        this.overlay_visible = false;
        this.translatePositionString = "";
        this.translatePosition = 0;
        this.translatePositionPrevious = 0;
        this.velocityCurrent = 0;
        this.totalDataAvailable = 0;
        this.totalPagesAvailable = 0;
        this.previousscrolldirection = 1;
        this.animation_BounceBackBackward = false;
        this.cellsdata_cache = ref([]);
        this.grid = new ASScrollerGrid(this);

        this.justified = new ASScrollerJustified(this);

        this.indexCacheForward = 0;
        this.indexCacheBackward = 0;
        this.numOfRows = 0;
    }

    DefineParameters(
        _props,
        _container,
        _viewport,
        _overlay_visible,
        _translatePositionString,
        _translatePosition,
        _translatePositionPrevious,
        _velocityCurrent,
        _previousscrolldirection
    ) {
        this.props = _props;
        this.container = _container;
        this.viewport = _viewport;
        this.overlay_visible = _overlay_visible;
        this.translatePositionString = _translatePositionString;
        this.translatePosition = _translatePosition;
        this.translatePositionPrevious = _translatePositionPrevious;
        this.velocityCurrent = _velocityCurrent;
        this.previousscrolldirection = _previousscrolldirection;

        if (this.props.layout === "grid") {
            this.grid.DefineParameters(_props);
        } else if (this.props.layout === "justified") {
            this.justified.DefineParameters(_props);
        }
    }

    /**
     * LINK UpdateDummyCells
     * @param {*} direction The direction of the scroller is scrolling
     */

    UpdateDummyCells(direction) {
        // Update dummy/loading cells
        if (this.props.layout === "grid") {
            for (var f = 0; f < this.GetTotalColumns(); f++) {
                var griddataCol = this.grid.data.value[f];
                if (typeof griddataCol.cells !== "undefined") {

                    // Loop through each cell
                    Object.keys(griddataCol.cells).forEach((cellkey) => {
                        /** order example for cellkey:
                         *  0 3 6 9 1 4 7 10 2 5 8 11
                         */
                        if (griddataCol.cells[cellkey].loading === true) {
                            // The Cache data key is selected based on
                            var searchCacheKey_order = -1;
                            if (cellkey < 0) {
                                searchCacheKey_order =
                                    this.totalDataAvailable - Math.floor(Math.abs(parseInt(cellkey)) % this.totalDataAvailable) + 1;
                            } else {
                                searchCacheKey_order = Math.floor(Math.abs(parseInt(cellkey)) % this.totalDataAvailable) + 1;
                            }
                            if (searchCacheKey_order > this.totalDataAvailable) searchCacheKey_order = this.totalDataAvailable;

                            var cellsdata_cacheElement_index = this.cellsdata_cache.value
                                .map(function(x) {
                                    return x.order;
                                })
                                .indexOf(parseInt(searchCacheKey_order));

                            if (typeof griddataCol.cells[cellkey] !== "undefined" && cellsdata_cacheElement_index !== -1) {
                                var cellsdata_cache_item = this.cellsdata_cache.value[cellsdata_cacheElement_index];
                                griddataCol.cells[cellkey].loading = false;
                                var cellsdata_cache_cell = cellsdata_cache_item;
                                griddataCol.cells[cellkey].id = cellsdata_cache_cell.id;
                                griddataCol.cells[cellkey].order = cellsdata_cache_cell.order;
                                griddataCol.cells[cellkey].imageexists = cellsdata_cache_cell.imageexists;
                                griddataCol.cells[cellkey].dt = cellsdata_cache_cell.dt;
                            }
                        }
                    });
                }
            }
        } else if (this.props.layout === "justified") {
            for (var f = 0; f < this.justified.data.value.rows.length; f++) {
                var _row = this.justified.data.value.rows[f];
                if (!_row.allCellsLoaded) {
                    if (typeof _row.cells !== "undefined") {
                        for (var k = 0; k < _row.cells.length; k++) {
                            var cell = _row.cells[k];



                            if (cell.loading === true) {
                                var final_index = _row.rowIndex * this.GetTotalColumns() + cell.index;
                                var searchCacheKey_order = -1;

                                if (final_index < 0) {
                                    searchCacheKey_order =
                                        this.totalDataAvailable - Math.floor(Math.abs(parseInt(final_index)) % this.totalDataAvailable) + 1;
                                } else {
                                    searchCacheKey_order = Math.floor(Math.abs(parseInt(final_index)) % this.totalDataAvailable) + 1;
                                }

                                if (searchCacheKey_order > this.totalDataAvailable) searchCacheKey_order = 1;
                                var cellsdata_cacheElement_index = this.cellsdata_cache.value
                                    .map(function(x) {
                                        return x.order;
                                    })
                                    .indexOf(parseInt(searchCacheKey_order));

                                if (typeof cell !== "undefined" && cellsdata_cacheElement_index !== -1) {
                                    cell.loading = false;


                                    var cellsdata_cache_item = this.cellsdata_cache.value[cellsdata_cacheElement_index];
                                    var cellsdata_cache_cell = cellsdata_cache_item;
                                    cell.id = cellsdata_cache_cell.id;
                                    cell.dt.image_visible = true;
                                    cell.order = cellsdata_cache_cell.order;
                                    cell.imageexists = cellsdata_cache_cell.imageexists;
                                    cell.dt = cellsdata_cache_cell.dt;
                                    _row.numberOfCellsLoaded++;
                                    if (_row.numberOfCellsLoaded === this.GetTotalColumns()) _row.allCellsLoaded = true;
                                }
                            }
                        }
                    }
                } // if !row.allCellsLoaded
            }
        }
    }

    /**
     *
     * @param {*} direction The direction of the scroller is scrolling
     * @param {*} newdata The new data to update
     */
    UpdateCellsCache(direction, newdata, context) {
        Object.keys(newdata).forEach((key) => {
            var test_exists_value = newdata[key].order;

            var cellsdata_cacheElement_index = this.cellsdata_cache.value
                .map(function(x) {
                    if (x) return x.order;
                    return -1;
                })
                .indexOf(test_exists_value);

            if (cellsdata_cacheElement_index === -1) {
                if (direction === 1) {
                    this.cellsdata_cache.value.push(newdata[key]);
                } else {
                    this.cellsdata_cache.value.unshift(newdata[key]);
                }
            }
        });

        context.emit("on-cache-updated", this.cellsdata_cache.value);

        if (direction === 1) {
            if (this.cellsdata_cache.value[this.cellsdata_cache.value.length - 1]) {
                this.indexCacheForward = this.cellsdata_cache.value[this.cellsdata_cache.value.length - 1].index;
            }
        } else {
            if (this.cellsdata_cache.value[0]) {
                this.indexCacheBackward = this.cellsdata_cache.value[0].index;
            }
        }
    }

    /**
     * * In order to have visible cells at the back of the scroller set Viewport Position at the half of the (total cells) * (cell height)
     */
    ChangeViewportPositionAtInitialization(cellSizeOriented) {
        // In order have cells visible at the back of the scroller
        // set Position at the half of the cells size

        if (this.props.layout === "grid") {
            let maxColumnHeight = 0;
            for (var f = 0; f < this.GetTotalColumns(); f++) {
                var currenColH = this.grid.data.value[f].forwardPosition;
                if (maxColumnHeight < currenColH) maxColumnHeight = currenColH;
            }

            let viewportSize = this.viewport.clientHeight;



            let startPos = maxColumnHeight / 2 - viewportSize / 2;
            this.translatePosition.value -= startPos;
            this.container.style.transform = "translate" + this.translatePositionString + "(" + this.translatePosition.value + "px) ";
        } else if (this.props.layout === "justified") {
            let viewportSize = this.viewport.clientHeight;



            let startPos = (this.numOfRows * cellSizeOriented) / 2 - viewportSize / 2;
            this.translatePosition.value -= startPos;
            this.container.style.transform = "translate" + this.translatePositionString + "(" + this.translatePosition.value + "px) ";
        }
    }

    /**
     *
     * @returns The Total number of Columns
     */
    GetTotalColumns() {
        return this.props.numcolumns;
    }

    /**
     *
     * @returns The Total number of Columns
     */
    GetTotalJustifiedRows() {
        return this.numOfRows;
    }

    /**
     *
     * @param {*} cellSizeOriented  The size of the cell based on orientation. Height if the orientation is Vertical and Width if Horizontal
     * @param {*} cellsNumberOriented The number of cells based on the orientation (in "masonry" layout its the total number of columns)
     * @returns The minimum number of cells required in order to fill the viewport
     */
    GetViewportNumberOfFittingCells(cellSizeOriented, cellsNumberOriented) {
        let viewportSize = this.viewport.clientHeight;



        let totalEntries = Math.ceil(viewportSize / cellSizeOriented);
        let _minCellsRequired = totalEntries * cellsNumberOriented;
        return _minCellsRequired;
    }

    /**
     *
     * @param {number} cellSizeOriented The size of the cell based on orientation. Height if the orientation is Vertical and Width if Horizontal
     * @param {number} cellsNumberOriented The number of cells based on the orientation (in "masonry" layout its the total number of columns)
     * @returns The GetViewportNumberOfFittingCells() * 2
     */
    GetInfiniteMinimumRequiredCells(cellSizeOriented, cellsNumberOriented) {
        let _minCellsRequired = this.GetViewportNumberOfFittingCells(cellSizeOriented, cellsNumberOriented);
        _minCellsRequired = Math.ceil(_minCellsRequired * 2);
        return _minCellsRequired;
    }

    /**
     *
     * @param {*} cellSizeOriented  The size of the cell based on orientation. Height if the orientation is Vertical and Width if Horizontal
     * @param {*} cellsNumberOriented The number of cells based on the orientation (in "masonry" layout its the total number of columns)
     * @returns The total number of cells to load at Initialization
     */
    GetPerPageRequireCells(cellSizeOriented, cellsNumberOriented) {
        var minCellRequired = this.GetViewportNumberOfFittingCells(cellSizeOriented, cellsNumberOriented) * 2;
        return minCellRequired;
    }

    UpdateIndexCache(isForward, newLength) {
        this.indexCacheForward += newLength;
    }
}