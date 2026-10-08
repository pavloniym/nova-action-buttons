<template>
    <action-button :field="field" :working="working" @click="fireAction" />

    <!-- Confirm Action Modal -->
    <component
        v-if="actionModalVisible && modalAction"
        class="text-left"
        :is="modalAction.component"
        :show="actionModalVisible"
        :working="working"
        :action="modalAction"
        :errors="errors"
        :resource-name="resourceName"
        :selected-resources="selectedResources"
        @confirm="executeAction"
        @close="closeConfirmationModal"
    />

    <!-- Action Response Modal -->
    <component
        v-if="responseModalVisible"
        :is="responseModalData?.component"
        :show="responseModalVisible"
        :data="responseModalData?.payload ?? {}"
        @confirm="closeResponseModal"
        @close="closeResponseModal"
    />
</template>

<script setup>

    // Components
    import ActionButton from './button/ActionButton.vue';

    // Composables
    import {useActionButton} from '../../composables/useActionButton';

    // Props
    const props = defineProps({
        field: {type: Object, required: true},
        resourceName: {type: String, required: true},
        isOnDetail: {type: Boolean, default: false},
        viaResource: {type: String, default: null},
        viaResourceId: {type: [String, Number], default: null},
    });

    // Bindings
    const {
        modalAction,
        errors,
        working,
        selectedResources,
        actionModalVisible,
        responseModalVisible,
        responseModalData,
        fireAction,
        executeAction,
        closeConfirmationModal,
        closeResponseModal,
    } = useActionButton(() => props.field?.action, {
        resourceName: () => props.resourceName,
        resourceId: () => props.field?.resourceId,
        isOnDetail: () => props.isOnDetail,
        queryParams: () => ({
            viaResource: props.viaResource,
            viaResourceId: props.viaResourceId,
        }),
    });

</script>
