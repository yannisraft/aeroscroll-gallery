<template>
    <q-btn flat :color="color" @click="ToggleFiltering()">        
        <div>{{ label }}</div>
        <q-icon size="1em" :name="icon" style="padding-left: 10px; padding-right: 10px;" />
    </q-btn>
</template>

<script>
import {
    ref,
    onMounted,
    watch
} from "vue";

export default {
    props: {
        label: {
            type: String,
            default: 'Button'
        },
        modelValue: {
            type: String,
            default: 'none'
        },
        color: {
            type: String,
            default: 'primary'
        }
    },

    emits: [
        'update:modelValue', 'UpdatedValue'
    ],

    setup(props, context) {
        let filtering = ref('none');
        let icon = ref('horizontal_rule');

        function ToggleFiltering() {
            /* console.log("filtering.value: ", filtering.value);
            console.log("icon.value: ", icon.value); */

            if(filtering.value === 'none') {
                filtering.value = 'asc';
                icon.value = 'expand_less';
            } else
            if(filtering.value === 'asc') {
                filtering.value = 'desc';
                icon.value = 'expand_more';
            } else
            if(filtering.value === 'desc') {
                filtering.value = 'none';
                icon.value = 'horizontal_rule';
            }

            context.emit('update:modelValue', filtering.value);
            context.emit('UpdatedValue', props.label);
        }

        onMounted(() => {
            //console.log("icon.value: ", icon.value);
        });

        watch(() => props.modelValue, (value) => {
            filtering.value = value;

            //console.log("NAME: "+props.label+" filtering.value: "+ filtering.value);

            if(filtering.value === 'none') {
                icon.value = 'horizontal_rule';
            } else
            if(filtering.value === 'asc') {
                icon.value = 'expand_less';
            } else
            if(filtering.value === 'desc') {
                icon.value = 'expand_more';
            }
        })

        return {
            icon,
            filtering,
            ToggleFiltering
        }
    }
}
</script>