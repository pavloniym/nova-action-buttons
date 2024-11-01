<template>
    <a href="#"
       v-bind="actionOptions"
       @click.stop.prevent="fireAction"
       @mouseenter="onMouseEnterTooltip"
       @mouseleave="onMouseLeaveTooltip"
    >

        <!-- Text -->
        <span v-if="text" v-text="text"></span>

        <!-- Icon -->
        <template v-if="hasIcon">
            <span v-if="htmlIcon" v-html="htmlIcon"></span>
            <Icon v-if="icon" :type="icon" />
            <img v-if="urlIcon" :alt="title" :src="urlIcon" class="w-6 h-6 inline" />
        </template>

        <!-- Tooltip -->
        <div v-if="tooltipIsVisible && tooltip" class="absolute z-10 left-1/2 -top-1 transform -translate-x-1/2 -translate-y-full px-2 py-1 bg-gray-700 text-white text-xs rounded">
            {{ tooltip }}
        </div>

    </a>
</template>

<script setup>

    // Composables
    import {computed} from 'vue'

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
    const urlIcon = computed(() => props?.field?.urlIcon || null);
    const htmlIcon = computed(() => props?.field?.htmlIcon || null);

    // Computed
    const hasIcon = computed(() => icon?.value !== null || htmlIcon?.value !== null || urlIcon?.value !== null);
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
