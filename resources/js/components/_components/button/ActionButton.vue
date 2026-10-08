<template>
    <button
        type="button"
        v-tooltip="tooltipText"
        :class="classes"
        :style="styles"
        :disabled="working"
        :aria-busy="working"
        :aria-label="label"
        @click.stop.prevent="$emit('click')"
    >
        <!-- Text -->
        <span v-if="text" v-text="text"></span>

        <!-- Icon -->
        <span v-if="hasIcon" class="nab-button__icon">
            <Icon v-if="icon" :name="icon" :type="iconType" />
            <span v-if="iconHtml" v-html="iconHtml"></span>
            <img v-if="iconUrl" :alt="label" :src="iconUrl" />
        </span>
    </button>
</template>

<script setup>

    // Composables
    import {computed} from 'vue';

    // Components
    import {Icon} from 'laravel-nova-ui';

    // Props
    const props = defineProps({
        field: {type: Object, default: null},
        working: {type: Boolean, default: false},
    });

    defineEmits(['click']);

    // Computed
    // Content
    const text = computed(() => props.field?.text || null);
    const icon = computed(() => props.field?.icon || null);
    const iconType = computed(() => props.field?.iconType || 'outline');
    const iconUrl = computed(() => props.field?.iconUrl || null);
    const iconHtml = computed(() => props.field?.iconHtml || null);
    const hasIcon = computed(() => Boolean(icon.value || iconUrl.value || iconHtml.value));

    // Computed
    // Tooltip falls back to the action name; it is shown only when tooltip() was called
    const tooltip = computed(() => props.field?.tooltip || props.field?.action?.name || null);
    const tooltipText = computed(() => (props.field?.hasTooltip === true ? tooltip.value : null));
    const label = computed(() => tooltip.value || text.value || props.field?.name || null);

    // Computed
    // Custom classes replace the default look, custom styles are merged over it
    const customClasses = computed(() => props.field?.classes || []);
    const variantClasses = computed(() => (props.field?.asToolbarButton === true ? ['nab-button--toolbar'] : ['nab-button--solid']));
    const classes = computed(() => ['nab-button', ...(customClasses.value.length > 0 ? customClasses.value : variantClasses.value)]);
    const styles = computed(() => props.field?.styles || {});

</script>
