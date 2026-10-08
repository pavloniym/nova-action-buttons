<template>
    <div>
        <action-button-runner
            v-if="field.action?.showOnIndex !== false"
            :field="field"
            :resource-name="resourceName"
            :via-resource="viaResource"
            :via-resource-id="viaResourceId"
        />
    </div>
</template>

<script setup>

    // Components
    import ActionButtonRunner from './_components/ActionButtonRunner.vue';

    // Props
    defineProps({
        field: {type: Object, required: true},
        resourceName: {type: String, required: true},
        viaResource: {type: String, default: null},
        viaResourceId: {type: [String, Number], default: null},
    });

</script>
