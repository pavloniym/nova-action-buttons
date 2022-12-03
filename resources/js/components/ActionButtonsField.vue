<template>
    <div class="flex items-center justify-center">
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
        .map(field => ({
            field: field,
            queryString: props?.queryString,
            resourceName: props?.resourceName,
        })))


</script>
