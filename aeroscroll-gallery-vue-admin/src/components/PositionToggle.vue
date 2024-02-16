<template>
    <div class="chkpos-container">
        <div class="chkpos-row" id="chkrow1">
            <div class="chkpos-col">
                <q-radio v-model="position" :val="1" />
            </div>
            <div class="chkpos-col">
                <q-radio v-model="position" :val="4" />
            </div>
            <div class="chkpos-col">
                <q-radio v-model="position" :val="7" />
            </div>
        </div>
        <div class="chkpos-row" id="chkrow2">
            <div class="chkpos-col">
                <q-radio v-model="position" :val="2" />
            </div>
            <div class="chkpos-col">
                <q-radio v-model="position" :val="5" />
            </div>
            <div class="chkpos-col">
                <q-radio v-model="position" :val="8"  />
            </div>
        </div>
        <div class="chkpos-row" id="chkrow3">
            <div class="chkpos-col">
                <q-radio v-model="position" :val="3" />
            </div>
            <div class="chkpos-col">
                <q-radio v-model="position" :val="6" />
            </div>
            <div class="chkpos-col">
                <q-radio v-model="position" :val="9" />
            </div>
        </div>
    </div>    
</template>

<script>
import { ref, onMounted, watch } from "vue";

export default {
    props: {
        label: {
            type: String,
            default: "Button"
        },
        modelValue: {
            type: Number,
            default: 7
        }
    },

    emits: ["update:modelValue", "UpdatedValue"],

    setup(props, context) {      
        let position = ref(0);  
        onMounted(() => {
            //console.log("icon.value: ", icon.value);
            position.value = props.modelValue;
        });

        watch(
            () => props.modelValue,
            (value) => {
                //
                position.value = props.modelValue;
            }
        );

        watch(
            () => position.value,
            (value) => {
                context.emit("update:modelValue", position.value);
            }
        );

        return {
            position
        };
    }
};
</script>
<style>
.chkpos-container {
    display: flex;
    flex-direction: row;
}

.chkpos-row {
    display: flex;
    flex-direction: column;
}

.chkpos-col {
    
}
</style>
