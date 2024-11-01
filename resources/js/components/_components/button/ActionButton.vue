<template>
    <a href="#"
       class="d-flex align-center"
       v-bind="actionOptions"
       @click.stop.prevent="fireAction"
       @mouseenter="onMouseEnterTooltip"
       @mouseleave="onMouseLeaveTooltip"
    >

        <!-- Text -->
        <div v-if="text" v-text="text"></div>

        <!-- Icon -->
        <div v-if="hasIcon" :class="{'ml-2': text}">
            <Icon v-if="icon" :type="icon" />
            <span v-if="iconHtml" v-html="iconHtml"></span>
            <img v-if="iconUrl" :alt="title" :src="iconUrl" class="w-6 h-6 inline" />
        </div>

        <!-- Tooltip -->
        <div v-if="tooltipIsVisible && tooltip" class="absolute z-10 left-1/2 -top-1 transform -translate-x-1/2 -translate-y-full px-2 py-1 bg-gray-700 text-white text-xs rounded">
            {{ tooltip }}
        </div>

    </a>
</template>

<script setup>

    // Composables
    import {computed, ref} from 'vue';

    // Props
    const props = defineProps({
        field: {type: Object, default: null},
        fireAction: {type: Function, default: null},
    });

    // State
    const tooltipIsVisible = ref(false);

    // Computed
    const title = computed(() => props?.field?.name || props?.field?.title || null);
    const text = computed(() => props?.field?.text || null);
    const tooltip = computed(() => props?.field?.tooltip || props?.field?.action?.name);

    // Computed
    // Styles
    const customStyles = computed(() => props?.field?.styles || []);
    const customClasses = computed(() => props?.field?.classes || []);
    const asToolbarButton = computed(() => props?.field?.asToolbarButton === true);

    // Computed
    // Icon
    const icon = computed(() => props?.field?.icon || null);
    const iconUrl = computed(() => props?.field?.iconUrl || null);
    const iconHtml = computed(() => props?.field?.iconHtml || null);

    // Computed
    const hasIcon = computed(() => icon?.value !== null || iconHtml?.value !== null || iconUrl?.value !== null);
    const hasTooltip = computed(() => props?.field?.hasTooltip === true);


    // Computed
    // Get action options
    const actionOptions = computed(() => {
        return {
            title: title?.value,
            class: [
                ...(asToolbarButton?.value === true ? toolbarButtonClasses.value : actionButtonClasses.value),
                ...(customClasses.value || [])
            ],
            style: {
                ...(asToolbarButton?.value === true ? null : actionButtonStyles?.value),
                ...(customStyles.value || {}),
            }
        }
    })

    // Computed
    // Get action button classes
    const actionButtonClasses = computed(() => [
        'flex-shrink-0', 'shadow', 'rounded', 'focus:outline-none', 'ring-primary-200', 'dark:ring-gray-600',
        'focus:ring', 'bg-primary-500', 'hover:bg-primary-400', 'active:bg-primary-600',
        'text-white', 'dark:text-gray-800', 'inline-flex', 'items-center', 'font-bold', 'px-2', 'mx-1', 'h-9', 'text-sm', 'flex-shrink-0',
    ])

    // Computed
    // Get toolbar button classes
    const toolbarButtonClasses = computed(() => [
        'toolbar-button', 'hover:text-primary-500', 'px-2', 'v-popper--has-tooltip', 'w-10'
    ])

    // Computed
    // Get action button styles
    const actionButtonStyles = computed(() => {
        return {
            margin: '0 2px',
        }
    })

    // Methods
    // Fire mouse actions for tooltip
    const onMouseLeaveTooltip = () => tooltipIsVisible.value = false
    const onMouseEnterTooltip = () => tooltipIsVisible.value = hasTooltip.value;

</script>
