const {
    ref,
    watch,
    onMounted,
    onBeforeUpdate,
    computed
} = Vue;
export default {
    template: `
        <div :class="['scroller-masonrycolumn-v']" :style="GetColumnStyle()">
            <slot name="row" :data="rowData">
                <div v-for="(cell, cellkey) in colSorted" :class="[{ 'aeroscroll-cell-scale-animation': (GetHoverAnimation() === 'scale')} ,'scroller-cell-v', (theme === 'theme_a') ? 'onhoverscalecellimage' : '']" :key="cellkey" :id="'item'+cell.uid" :style="GetCellStyle(cell)">
                    <slot name="cell" :data="cell">
                        <span>{{ cell.index }}</span>
                    </slot>
                </div>
            </slot>
        </div>        
    `,
    name: "VScrollerMasonryColumn",
    props: {
        cellgap: {
            type: Number,
            default: 0
        },

        orientation: {
            type: String,
            default: "vertical"
        },

        theme: {
            type: String,
            default: ""
        },
        containerheight: {
            type: Number,
            default: 0
        },
        numcolumns: {
            type: Number,
            default: 3
        },
        col: {
            type: Object,
            default: () => {
                return {
                    cells: {},
                    backwardPosition: 0,
                    forwardPosition: 0
                }
            }
        },
        hoveranimation: {
            type: String,
            default: "none"
        },
    },
    components: {

    },
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
                'height': cell.height + 'px',
                'width': '100%',
                'top': cell.top + 'px',
                'margin': cell.marginY + 'px ' + cell.marginX + 'px',
                'padding': props.cellgap + 'px'
            };


            if (props.orientation === "horizontal") {
                finalStyle = {
                    'height': '100%',
                    'width': cell.height + 'px',
                    'left': cell.top + 'px',
                    'margin': cell.marginX + 'px ' + cell.marginY + 'px',
                    'padding': props.cellgap + 'px'
                };
            }

            return finalStyle;
        }

        function GetColumnStyle() {
            var finalStyle = {
                'background-color': 'none'
            };


            if (props.orientation === "horizontal") {
                var flexbasis = props.containerheight / props.numcolumns;
                finalStyle = {
                    'background-color': 'none',
                    'flex-basis': flexbasis + 'px'
                };
            }

            return finalStyle;
        }

        const colSorted = computed(() => {
            var sortArray = [];
            // convert the list to array of key and value pair
            for (let i in props.col.cells) {
                sortArray.push(props.col.cells[i]);
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
            GetColumnStyle,
            GetHoverAnimation,
            colSorted
        };
    }
}