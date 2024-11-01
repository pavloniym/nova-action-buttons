<template>
    <div class="relative">
        <action-button v-bind="{field, fireAction}" />
        <component
            v-if="confirmActionModalOpened"
            v-bind="options"
            class="text-left"
            :is="selectedAction.component"
            @close="closeConfirmationModal"
            @confirm="executeAction">
        </component>
    </div>
</template>

<script setup>

    // Components
    import ActionButton from './_components/button/ActionButton.vue';

    // Composables
    import {computed, ref} from 'vue';
    import {useHandleAction} from '../mixins/HandlesActions'

    // Props
    const props = defineProps({
        field: {type: Object, default: null},
        queryString: {type: Object, default: null},
        resourceName: {type: String, default: null},
    });


    // Computed
    // Get query string options
    const queryString = computed(() => ({
        action: selectedAction?.value?.uriKey,
        search: props?.queryString?.currentSearch,
        filters: props?.queryString?.encodedFilters,
        trashed: props?.queryString?.currentTrashed,
        viaResource: props?.queryString?.viaResource,
        viaResourceId: props?.queryString?.viaResourceId,
        viaRelationship: props?.queryString?.viaRelationship,
    }));

    // Computed
    const selectedAction = computed(() => props?.field?.action);
    const selectedResources = computed(() => [props?.field?.resourceId]);

    // Bindings
    const {
        errors,
        working,
        fireAction,
        executeAction,
        closeConfirmationModal,
        confirmActionModalOpened
    } = useHandleAction(
        {
            queryString: queryString.value,
            resourceName: props?.resourceName,
            selectedAction: selectedAction.value,
            selectedResources: selectedResources.value,
        }
    )

    // Computed
    const options = computed(() => ({
        show: true,
        errors: errors?.value,
        action: selectedAction?.value,
        working: working?.value === true,
        resourceName: props?.resourceName,
        selectedResources: selectedResources?.value,
    }))

</script>
