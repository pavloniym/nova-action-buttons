<template>
    <PanelItem v-if="field.action?.showOnDetail !== false" :index="index" :field="field">
        <template #value>
            <action-button-runner :field="field" :resource-name="resourceName" is-on-detail />
        </template>
    </PanelItem>
</template>

<script setup>

    // Components
    import ActionButtonRunner from './_components/ActionButtonRunner.vue';

    // Props
    defineProps({
        index: {type: Number, default: 0},
        field: {type: Object, required: true},
        resourceName: {type: String, required: true},
    });

</script>
