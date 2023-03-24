<template>
    <div class="flex align-center" :class="alignClasses">
        <template v-for="(action, k) in actions" :key="k">
            <action-button v-bind="action"/>
        </template>
    </div>
</template>

<script setup>

    // Vue
    import {computed} from 'vue';

    // Components
    import ActionButton from './ActionButtonField'

    // Props
    const props = defineProps({
        field: {type: Object, default: null},
        queryString: {type: Object, default: null},
        resourceName: {type: String, default: null},
    });

    // Computed
    const collection = computed(() => props?.field?.collection || []);
    const actions = computed(() => (collection?.value || [])
        .filter((field) => field.action.showOnIndex)
        .map(field => ({
            field: field,
            queryString: props?.queryString,
            resourceName: props?.resourceName,
        })))


    // Computed
    // Align classes
    const alignClasses = computed(() => {
        return {
            'justify-end': props?.field?.textAlign === 'right',
            'justify-start': props?.field?.textAlign === 'left',
            'justify-center': props?.field?.textAlign === 'center',
        }
    })

</script>
