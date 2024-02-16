const {
    ref,
    watch,
    onMounted,
    onBeforeUpdate,
    computed
} = Vue;
export default {
    template: `
        <div class="scroller-justifiedrow-v" :style="GetRowStyle()" :id="GetRowID()">
            <slot name="row" :data="rowData">
                <div v-for="(cell, cellkey) in rowSorted" :class="[{ 'aeroscroll-cell-scale-animation': (GetHoverAnimation() === 'scale')} ,'scroller-cell-v']" :key="cellkey" :id="'item'+cell.uid" :style="GetCellStyle(cell)">
                    <slot name="cell" :data="cell">
                        <span>{{ cell.index }}</span>
                    </slot>
                </div>
            </slot>
        </div>   
    `,
    name: "VScrollerJustifiedRow",
    props: {
        cellgap: {
            type: Number,
            default: 0
        },

        containerheight: {
            type: Number,
            default: 0
        },
        theme: {
            type: String,
            default: ""
        },
        rowheight: {
            type: Number,
            default: 0
        },
        numcolumns: {
            type: Number,
            default: 3
        },
        row: {
            type: Object,
            default: () => {
                return {
                    cells: [],
                    row: 0,
                    rowIndex: 0
                };
            }
        },
        hoveranimation: {
            type: String,
            default: "none"
        }
    },
    components: {},
    setup(props, context) {
        let IDGenerated = Math.floor(Math.random() * 999999) + 1000000;

        // ---- Reactive Attributes
        if (props.layout === "justified") {
            var percentLeft = 100;
            let index = 0;
        }

        function GetHoverAnimation() {
            let hoveranimationType = "none";
            hoveranimationType = props.hoveranimation;
            return hoveranimationType;
        }

        onMounted(() => {
            //
        });

        function GetCellStyle(cell) {
            var finalStyle = {
                height: cell.height + "px",
                width: "100%",
                top: cell.top + "px",
                margin: cell.marginY + "px " + cell.marginX + "px",
                padding: props.cellgap + "px",
                'flex-basis': cell.percent + "%"
            };

            return finalStyle;
        }

        function GetRowStyle() {
            var finalStyle = {
                "background-color": "none",
                'height': props.rowheight + 'px',
                'top': props.row.top + "px",
                display: 'flex'
            };

            return finalStyle;
        }

        function GetRowID() {
            return "row" + props.row.uid;
        }

        const rowSorted = computed(() => {
            var sortArray = [];
            // convert the list to array of key and value pair
            for (let i in props.row.cells) {
                sortArray.push(props.row.cells[i]);
            }

            // sort the array using index.
            sortArray.sort(function(a, b) {
                return a.index - b.index;
            });

            // now create a newList of required format.
            let newList = {};
            for (let i in sortArray) {
                newList[sortArray[i].index] = sortArray[i];
            }

            return newList;
        });

        return {
            GetCellStyle,
            GetRowStyle,
            GetHoverAnimation,
            GetRowID,
            rowSorted
        };
    }
};