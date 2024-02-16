import {
    GetObjectMinKeyByIndex,
    GetObjectMaxKeyByIndex,
    GetArrayMaxKeyByIndex,
    GetArrayMinKeyByIndex
} from "./utils/objectutils.js";

const {
    ref
} = Vue;

export class GridCell {
    constructor(newkey) {
        this.id = 0;
        this.uid = String(newkey) + "_" + String(Math.floor(Math.random() * 1000000));
        this.index = parseInt(newkey);
        this.globalindex = parseInt(newkey);
        this.order = 1;
        this.dt = {
            title: "",
            subtitle: "",
            content: "",
            feature_image: "",
            thumbnail_image: ""
        };
        this.height = 0;
        this.top = 0;
        this.loading = true;
        this.imageexists = true;
    }
}

export class GridColumn {
    constructor(margintop) {
        this.cells = {}; // Cells inside each column
        this.backwardPosition = margintop;
        this.forwardPosition = margintop;
        this.backwardColumnIndex = 0; // index only fom this column
        this.forwardColumnIndex = 0; // index only fom this column
    }
}

export class ASScrollerGrid {
    constructor(_asScroller) {
        this.asScroller = _asScroller;
        this.props = null;
        this.data = ref({});
        this.canRerunUpdateNext = true;
        this.celldataindex_forward = 0;
        this.celldataindex_backward = 0;
    }

    Setup() {}

    DefineParameters(_props) {
        this.props = _props;
    }

    CreateColumns() {
        // Create grid data columns
        // EXTRA: For grid Layout create a margin TOP for the first Cells
        for (var f = 0; f < this.asScroller.GetTotalColumns(); f++) {
            var newMarginTop = 0;
            this.data.value[f] = new GridColumn(newMarginTop);
        }
    }

    GenerateInitialDummyCells(first) {
        var mancolindex = 0;

        for (let key in first) {
            // Random height
            var newHeight = this.props.cellSize;
            var newcell = new GridCell(key);
            newcell.top = this.data.value[mancolindex].forwardPosition;
            newcell.height = newHeight;

            newcell.order = first[key].order;

            this.data.value[mancolindex].forwardPosition += newHeight;
            this.data.value[mancolindex].cells[key] = newcell;

            // if column filled
            this.data.value[mancolindex].forwardColumnIndex++;

            mancolindex++;
            if (mancolindex > this.asScroller.GetTotalColumns() - 1) mancolindex = 0;
            this.celldataindex_forward++;
        }
    }

    AddNextCells(context, celldataindex_backward) {
        let addedNewCells = false;

        var viewportSize = this.asScroller.viewport.clientHeight;



        for (var f = 0; f < this.asScroller.GetTotalColumns(); f++) {
            var griddataCol = this.data.value[f];

            if (griddataCol) {
                if (Object.keys(griddataCol.cells).length > 0) {
                    // Get most Backwards Cell
                    var backwardscellkey = GetObjectMinKeyByIndex(griddataCol.cells);
                    var backwardscell = griddataCol.cells[backwardscellkey];
                    var backwards_element = document.getElementById("item" + backwardscell.uid);

                    if (backwards_element) {
                        var backwards_elementBounds = backwards_element.getBoundingClientRect();

                        // Delete Backwards
                        var testPosition_backwards = backwards_elementBounds.y;




                        var testPosition = this.asScroller.viewport.getBoundingClientRect().y - viewportSize;
                        if (testPosition_backwards < testPosition) {
                            this.DeleteGridCell(1, f, backwardscellkey, backwards_element, this.celldataindex_forward);
                        }
                    }

                    // Get Most Forward Cell
                    var mostforwardcellkey = GetObjectMaxKeyByIndex(griddataCol.cells);
                    if (mostforwardcellkey) {
                        var forwardcell = griddataCol.cells[mostforwardcellkey];

                        var forward_element = document.getElementById("item" + forwardcell.uid);
                        if (forward_element) {
                            var forward_elementBounds = forward_element.getBoundingClientRect();

                            // Get Newest Cache Cell
                            var lastcache_cell = this.asScroller.cellsdata_cache.value[this.asScroller.cellsdata_cache.value.length - 1];

                            if (typeof lastcache_cell !== "undefined") {
                                if (parseInt(lastcache_cell.index) < this.celldataindex_forward) {
                                    if (this.canRerunUpdateNext) {
                                        this.BlockRerunUpdateNext();

                                        context.emit(
                                            "on-update-data-next",
                                            (newdataobj) => {
                                                this.asScroller.UpdateDummyCells(1);
                                            },
                                            (postdataobj, postsLessThenPredicted) => {
                                                if (postdataobj !== null) {
                                                    delete postdataobj["indexFirst"];
                                                    delete postdataobj["indexLast"];

                                                    this.asScroller.UpdateCellsCache(1, postdataobj, context);
                                                    this.asScroller.UpdateDummyCells(1);
                                                }
                                            },
                                            celldataindex_backward,
                                            this.asScroller.indexCacheForward
                                        );
                                    }
                                }
                            }

                            // Add Forward Cell
                            var testPosition_forward = forward_elementBounds.y;



                            // The "getBoundingClientRect" returns the position of the element relatively to the Viewport so if
                            // user has scrolled the page, this changes
                            var testPosition = this.asScroller.viewport.getBoundingClientRect().y + viewportSize;

                            if (testPosition_forward < testPosition) {
                                this.AddGridCell(f, 1);
                            }
                        }
                    }
                }
            }
        }
    }

    AddPreviousCells(context, celldataindex_forward) {
        var viewportSize = this.asScroller.viewport.clientHeight;



        for (var f = this.asScroller.GetTotalColumns() - 1; f >= 0; f--) {
            var griddataCol = this.data.value[f];
            if (griddataCol) {
                if (Object.keys(griddataCol.cells).length > 0) {

                    // DELETE Backward cells                
                    var forwardcellkey = GetObjectMaxKeyByIndex(griddataCol.cells);
                    var forwardcell = griddataCol.cells[forwardcellkey];
                    var forward_element = document.getElementById("item" + forwardcell.uid);

                    if (forward_element) {
                        var forward_elementBounds = forward_element.getBoundingClientRect();


                        var testPosition = this.asScroller.viewport.getBoundingClientRect().y + 3 * viewportSize;

                        var testPosition_forward = forward_elementBounds.y;



                        if (testPosition_forward > testPosition) {
                            this.DeleteGridCell(-1, f, forwardcellkey, forward_element, this.celldataindex_backward);
                        }
                    }


                    // Get Most Bacward Cell
                    var mostbackwardcellkey = GetObjectMinKeyByIndex(griddataCol.cells);
                    if (mostbackwardcellkey) {
                        var backwardcell = griddataCol.cells[mostbackwardcellkey];
                        var backward_element = document.getElementById("item" + backwardcell.uid);
                        if (backward_element) {
                            var backward_elementBounds = backward_element.getBoundingClientRect();

                            // Get Newest Cache Cell
                            var firstcache_cell = GetArrayMinKeyByIndex(this.asScroller.cellsdata_cache.value);
                            if (typeof firstcache_cell !== "undefined") {
                                if (parseInt(firstcache_cell.index) > this.celldataindex_backward) {

                                    if (this.canRerunUpdateNext) {
                                        this.BlockRerunUpdateNext();

                                        context.emit(
                                            "on-update-data-previous",
                                            (newdataobj) => {
                                                this.asScroller.UpdateDummyCells(1);
                                            },
                                            (postdataobj, postsLessThenPredicted) => {
                                                if (postdataobj !== null) {
                                                    delete postdataobj["indexFirst"];
                                                    delete postdataobj["indexLast"];

                                                    this.asScroller.UpdateCellsCache(-1, postdataobj, context);
                                                    this.asScroller.UpdateDummyCells(-1);
                                                }
                                            },
                                            celldataindex_forward,
                                            this.asScroller.indexCacheForward
                                        );
                                    }
                                }
                            }

                            var testPosition_backward = backward_elementBounds.y;



                            // The "getBoundingClientRect" returns the position of the element relatively to the Viewport so if
                            // user has scrolled the page, this changes
                            var testPosition = this.asScroller.viewport.getBoundingClientRect().y;
                            if (testPosition_backward > testPosition) {
                                this.AddGridCell(f, -1);
                            }
                        }
                    }



                }
            }
        }
    }

    // LINK > AddGridCell
    AddGridCell(colindex, direction) {
        var newHeight = this.props.cellSize;
        var _gridcolumntop = 0;

        var newkey = 0;
        if (direction === 1) {
            _gridcolumntop = this.data.value[colindex].forwardPosition;
            this.data.value[colindex].forwardPosition += newHeight;


            newkey = this.data.value[colindex].forwardColumnIndex * this.asScroller.GetTotalColumns() + colindex;

            this.data.value[colindex].forwardColumnIndex++;
        } else {
            this.data.value[colindex].backwardPosition -= newHeight;
            _gridcolumntop = this.data.value[colindex].backwardPosition;
            this.data.value[colindex].backwardColumnIndex--;

            newkey = this.data.value[colindex].backwardColumnIndex * this.asScroller.GetTotalColumns() + colindex;
        }

        var newcell = new GridCell(newkey);
        newcell.height = newHeight;
        newcell.top = _gridcolumntop;

        if (direction === 1) {
            this.data.value[colindex].cells[newkey] = newcell;
            this.celldataindex_forward++;
        } else {
            var newobj = {};
            newobj[newkey] = newcell;
            this.data.value[colindex].cells = Object.assign(newobj, this.data.value[colindex].cells);
            this.celldataindex_backward--;
        }

        // if cache for key already exists add data
        try {
            var searchCacheKey_order = -1;
            if (newkey < 0) {
                searchCacheKey_order = this.asScroller.totalDataAvailable - Math.floor(Math.abs(parseInt(newkey)) % this.asScroller.totalDataAvailable) + 1;
            } else {
                searchCacheKey_order = Math.floor(Math.abs(parseInt(newkey)) % this.asScroller.totalDataAvailable) + 1;
            }
            if (searchCacheKey_order > this.asScroller.totalDataAvailable) searchCacheKey_order = 1;

            var cellsdata_cacheElement_index = this.asScroller.cellsdata_cache.value
                .map(function(x) {
                    return x.order;
                })
                .indexOf(searchCacheKey_order);

            if (searchCacheKey_order !== -1) {
                this.data.value[colindex].cells[newkey].order = searchCacheKey_order;

                let cellsdata_cacheElement = this.asScroller.cellsdata_cache.value[cellsdata_cacheElement_index];
                if (cellsdata_cacheElement && this.data.value[colindex].cells[newkey]) {
                    this.data.value[colindex].cells[newkey].id = cellsdata_cacheElement.id;
                    this.data.value[colindex].cells[newkey].dt = cellsdata_cacheElement.dt;
                    this.data.value[colindex].cells[newkey].loading = false;
                    this.data.value[colindex].cells[newkey].imageexists = cellsdata_cacheElement.imageexists;
                }
            }
        } catch (ex) {
            console.log(ex);
        }
    }

    // LINK > DeleteGridCell
    DeleteGridCell(direction, colindex, cellkey, element) {
        if (direction === 1) {
            this.data.value[colindex].backwardColumnIndex++;
            this.data.value[colindex].backwardPosition += this.data.value[colindex].cells[cellkey].height;
            this.celldataindex_backward++;
        } else {
            this.data.value[colindex].forwardColumnIndex--;
            this.data.value[colindex].forwardPosition -= this.data.value[colindex].cells[cellkey].height;
            this.celldataindex_forward--;
        }
        delete this.data.value[colindex].cells[cellkey];
    }

    BlockRerunUpdateNext() {
        this.canRerunUpdateNext = false;
        setTimeout(() => {
            this.canRerunUpdateNext = true;
        }, 800);
    }

    BlockRerunUpdatePrevious() {
        canRerunUpdatePrevious = false;
        setTimeout(() => {
            canRerunUpdatePrevious = true;
        }, 800);
    }
}