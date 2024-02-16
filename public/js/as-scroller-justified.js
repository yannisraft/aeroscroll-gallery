import {
    GetObjectMinKeyByIndex,
    GetObjectMaxKeyByIndex,
    GetArrayMaxKeyByIndex,
    GetArrayMinKeyByIndex,
    SplitNumberIntoParts
} from "./utils/objectutils.js";

const {
    ref
} = Vue;

export class JustifiedCell {
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
            thumbnail_image: "",
            image_visible: false
        };
        this.height = 0;
        this.width = 0;
        this.top = 0;
        this.percent = 100;
        this.loading = true;
        this.imageexists = true;
    }
}

export class JustifiedRow {
    constructor() {
        this.cells = []; // Cells inside each column
        this.rowIndex = 0;
        this.allCellsLoaded = false;
        this.numberOfCellsLoaded = 0;
    }
}

export class JustifiedRowsList {
    constructor(margintop) {
        this.rows = []; // Cells inside each column
        this.backwardPosition = margintop;
        this.forwardPosition = margintop;
        this.backwardRowIndex = -1; // index only fom this column
        this.forwardRowIndex = 0; // index only fom this column
    }
}

export class ASScrollerJustified {
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

    // LINK > GenerateInitialDummyCells
    GenerateInitialDummyCells(first, cellsPerPage, cellSizeOriented) {
        var rowindex = 0;
        // First Calculate initial Rows Required
        var numOfColumns = this.asScroller.GetTotalColumns();
        this.asScroller.numOfRows = cellsPerPage / numOfColumns;

        this.data.value = new JustifiedRowsList(0);

        var first_index = 0;
        for (var f = 0; f < this.asScroller.numOfRows; f++) {
            var parts = SplitNumberIntoParts(numOfColumns, 100);

            // Create new Row and add new index
            var newrow = new JustifiedRow();
            newrow.rowIndex = this.data.value.forwardRowIndex;
            newrow.top = this.data.value.forwardPosition;
            newrow.uid = String(newrow.rowIndex) + "_" + String(Math.floor(Math.random() * 1000000));
            this.data.value.forwardRowIndex++;
            this.data.value.forwardPosition += cellSizeOriented;
            this.data.value.backwardPosition = -cellSizeOriented;

            var part_index = 0;
            parts.forEach((col) => {
                var newcell = new JustifiedCell(first_index);
                newcell.top = 0;
                newcell.width = col;
                newcell.loading = true;
                newcell.imageexists = true;
                newcell.order = 1;
                newcell.height = cellSizeOriented;
                newcell.percent = parts[part_index];
                newcell.index = part_index;
                newrow.cells.push(newcell);

                this.celldataindex_forward = first_index;

                first_index++;
                part_index++;
            });

            this.data.value.rows.push(newrow);
        }

        console.log("JUSTIFIED DATA: ", this.data.value);
    }

    // LINK > AddNextCells
    AddNextCells(context, cellSizeOriented) {
        var viewportSize = this.asScroller.viewport.clientHeight;



        // Find latest Row Index and then Add next Row
        var forwardrow = this.data.value.rows[this.data.value.rows.length - 1];
        var forward_element = document.getElementById("row" + forwardrow.uid);
        if (forward_element) {
            var forward_elementBounds = forward_element.getBoundingClientRect();

            var testPosition_forward = forward_elementBounds.y;



            var testPosition = this.asScroller.viewport.getBoundingClientRect().y + viewportSize;
            if (testPosition_forward < testPosition) {
                this.AddRow(1, cellSizeOriented);
            }
        }

        // Delete Last Row Backwards
        var backwardrow = this.data.value.rows[0];
        var backwardrow_element = document.getElementById("row" + backwardrow.uid);
        if (backwardrow_element) {
            var backwards_elementBounds = backwardrow_element.getBoundingClientRect();

            // Delete Backwards
            var testPosition_backwards = backwards_elementBounds.y;



            var testPosition = this.asScroller.viewport.getBoundingClientRect().y - viewportSize; // NEW
            //var testPosition = -viewportSize; // OLD
            if (testPosition_backwards < testPosition) {
                var rowIndex = 0;
                this.DeleteRow(1, rowIndex, cellSizeOriented);
            }
        }

        // Get Newest Cache Cell
        var lastcache_cell = GetArrayMaxKeyByIndex(this.asScroller.cellsdata_cache.value);
        if (typeof lastcache_cell !== "undefined") {
            if (parseInt(lastcache_cell.index) < this.celldataindex_forward) {
                if (this.canRerunUpdateNext === true) {
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

                                //console.log("on-update-data-next: ", postdataobj);

                                this.asScroller.UpdateCellsCache(1, postdataobj, context);
                                this.asScroller.UpdateDummyCells(1);
                            }
                        },
                        this.celldataindex_backward,
                        this.asScroller.indexCacheForward
                    );
                }
            }
        }
    }

    // LINK > AddPreviousCells
    AddPreviousCells(context, cellSizeOriented) {
        var viewportSize = this.asScroller.viewport.clientHeight;



        if (this.data.value.rows.length > 0) {
            // Find first Row Index and then Add next Row Backwards
            var backwardrow = this.data.value.rows[0];
            var backward_element = document.getElementById("row" + backwardrow.uid);
            if (backward_element) {
                var backward_elementBounds = backward_element.getBoundingClientRect();

                var testPosition_backward = backward_elementBounds.y;



                var testPosition = this.asScroller.viewport.getBoundingClientRect().y;
                if (testPosition_backward > testPosition) {
                    this.AddRow(-1, cellSizeOriented);
                }
            }

            // Delete Last Row Forward
            var forwardrow = this.data.value.rows[this.data.value.rows.length - 1];
            var forward_element = document.getElementById("row" + forwardrow.uid);

            if (forward_element) {
                var forward_elementBounds = forward_element.getBoundingClientRect();

                var testPosition = this.asScroller.viewport.getBoundingClientRect().y + 3 * viewportSize;

                var testPosition_forward = forward_elementBounds.y;



                if (testPosition_forward > testPosition) {
                    var rowIndex = this.data.value.rows.length - 1;
                    this.DeleteRow(-1, rowIndex, cellSizeOriented);
                }
            }

            var firstcache_cell = GetArrayMinKeyByIndex(this.asScroller.cellsdata_cache.value);
            var backwardrow = this.data.value.rows[0];

            if (typeof firstcache_cell !== "undefined") {

                if (parseInt(firstcache_cell.index) > this.celldataindex_backward) {
                    if (this.canRerunUpdateNext === true) {
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

                                    this.asScroller.UpdateCellsCache(1, postdataobj, context);
                                    this.asScroller.UpdateDummyCells(1);
                                }
                            },
                            this.celldataindex_forward,
                            this.asScroller.indexCacheBackward
                        );
                    }
                }
            }
        }
    }

    // LINK > AddRow
    AddRow(direction, cellSizeOriented) {
        var numOfColumns = this.asScroller.GetTotalColumns();
        var parts = SplitNumberIntoParts(numOfColumns, 100);

        // Create new Row and add new index
        var newrow = new JustifiedRow();
        if (direction === 1) {
            newrow.rowIndex = this.data.value.forwardRowIndex;
            newrow.top = this.data.value.forwardPosition;
            this.data.value.forwardRowIndex++;
            this.data.value.forwardPosition += cellSizeOriented;
        } else {
            newrow.rowIndex = this.data.value.backwardRowIndex;
            newrow.top = this.data.value.backwardPosition;
            this.data.value.backwardRowIndex--;
            this.data.value.backwardPosition -= cellSizeOriented;
        }
        newrow.uid = String(newrow.rowIndex) + "_" + String(Math.floor(Math.random() * 1000000));

        var part_index = 0;
        var searchCacheKey_order = -1;

        parts.forEach((col) => {
            var final_index = newrow.rowIndex * numOfColumns + part_index;
            var newcell = new JustifiedCell(final_index);
            newcell.top = 0;
            newcell.width = col;
            newcell.loading = true;

            if (final_index < 0) {
                searchCacheKey_order =
                    this.asScroller.totalDataAvailable - Math.floor(Math.abs(parseInt(final_index)) % this.asScroller.totalDataAvailable);

            } else {
                searchCacheKey_order = Math.floor(Math.abs(parseInt(final_index)) % this.asScroller.totalDataAvailable);
            }

            newcell.order = searchCacheKey_order + 1;
            newcell.height = cellSizeOriented;
            newcell.percent = parts[part_index];
            newcell.index = part_index;
            newcell.imageexists = true;

            newrow.cells.push(newcell);

            if (direction === 1) this.celldataindex_forward++;
            if (direction === -1) this.celldataindex_backward--;
            part_index++;
        });

        var addedrow = null;
        if (direction === 1) {
            this.data.value.rows.push(newrow);
            addedrow = this.data.value.rows[this.data.value.rows.length - 1];
        } else {
            this.data.value.rows.unshift(newrow);
            addedrow = this.data.value.rows[0];
        }

        // Check if Cells Exists in cache and load it
        addedrow.cells.forEach((addedcell) => {
            if (addedcell.loading === true) {
                // if cache for key already exists add data
                try {
                    var _searchCacheKey_order = addedcell.order;
                    if (_searchCacheKey_order > this.asScroller.totalDataAvailable) _searchCacheKey_order = 1;
                    var cellsdata_cacheElement_index = this.asScroller.cellsdata_cache.value
                        .map(function(x) {
                            return x.order;
                        })
                        .indexOf(_searchCacheKey_order);


                    if (_searchCacheKey_order !== -1) {
                        let cellsdata_cacheElement = this.asScroller.cellsdata_cache.value[cellsdata_cacheElement_index];
                        if (cellsdata_cacheElement) {
                            setTimeout(() => {
                                if (addedcell.loading === true && addedcell.dt.thumbnail_image === "") {
                                    addedcell.loading = false;
                                    addedcell.imageexists = cellsdata_cacheElement.imageexists;
                                    addedcell.id = cellsdata_cacheElement.id;
                                    addedcell.dt = cellsdata_cacheElement.dt;
                                    addedcell.dt.image_visible = true;
                                    addedcell.order = cellsdata_cacheElement.order;
                                    addedrow.numberOfCellsLoaded++;
                                    if (addedrow.numberOfCellsLoaded === this.asScroller.GetTotalColumns()) addedrow.allCellsLoaded = true;
                                }
                            }, 100);
                        }
                    }
                } catch (ex) {
                    console.log(ex);
                }
            }
        });
    }

    // LINK > DeleteRow
    DeleteRow(direction, rowIndex, cellSizeOriented) {
        this.data.value.rows.splice(rowIndex, 1);

        if (direction === 1) {
            this.data.value.backwardRowIndex++;
            this.data.value.backwardPosition += cellSizeOriented;
            this.celldataindex_forward++;
        } else {
            this.data.value.forwardRowIndex--;
            this.data.value.forwardPosition -= cellSizeOriented;
            this.celldataindex_backward--;
        }
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