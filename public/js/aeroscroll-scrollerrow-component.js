const {
    ref,
    watch,
    onMounted,
    onBeforeUpdate
} = Vue;
export default {
    template: `
        <div class="scroller-row-v" :id="rowId" :style="[{'margin-left': gap+'px'}, {'background-color': '#eaeaea'},{top: rowData.top, left: rowData.left, width: rowData.width, height: rowData.height, 'flex-direction': flexDirection}]">
            <slot name="row" :data="rowData">
                <div v-for="(cell, cellkey) in rowData.cells" :class="[{ 'aeroscroll-cell-scale-animation': (GetHoverAnimation() === 'scale')} ,'scroller-cell-v']" :key="cellkey" :id="'item'+cell.id" :style="{ 'flex-basis': cell.flexbasis+'%', 'margin': cell.marginY+'px ' + cell.marginX+'px'}">
                    <slot name="cell" :data="cell">
                        <span>{{ cell.index }}</span>
                    </slot>
                </div>
            </slot>
        </div>        
    `,
    name: "VScrollerRow",
    props: {
        gap: {
            type: Number,
            default: 0
        },
        calculatedRowSize: {
            type: Number,
            default: 0
        },

        layout: {
            type: String,
            default: "grid"
        },
        row: {
            type: Object,
            default: () => {
                return {
                    cells: {},
                    index: 0,
                    isforward: true,
                    animate: false,
                    top: "0px",
                    left: "0px",
                    width: "0px"
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
        let rowData = ref(props.row);
        let rowId = ref("row_" + IDGenerated);
        let flexDirection = ref("row");

        if (props.layout === "justified") {
            var percentLeft = 100;

            let index = 0;
            for (var key in rowData.value.cells) {
                if (index < Object.keys(rowData.value.cells).length) {
                    if (index === (Object.keys(rowData.value.cells).length - 1)) {
                        if (rowData.value.cells[key]) rowData.value.cells[key].flexbasis = percentLeft;
                    } else {
                        var newPercent = Math.floor(Math.random() * 40) + 30; /// FIXED 28-11-2022 Bug 101 
                        percentLeft = percentLeft - newPercent;
                        if (rowData.value.cells[key]) rowData.value.cells[key].flexbasis = newPercent;
                    }

                    index++;
                }
            }
        }

        if (props.row.isforward !== null && typeof props.row.isforward !== 'undefined') {
            if (!props.row.isforward && props.row.index < 0) {
                flexDirection.value = "row-reverse";
            }
        }



        function GetHoverAnimation() {
            let hoveranimationType = "none";
            hoveranimationType = props.hoveranimation;
            return hoveranimationType;
        }



        onMounted(() => {
            let rowelement = document.getElementById(rowId.value);
            context.emit("on-rendered", rowelement.clientHeight);
        });

        return {
            rowId,
            rowData,
            flexDirection,

            GetHoverAnimation,
        };
    }
}