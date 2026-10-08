<template>
    <div class="nab-buttons" :class="alignClass">
        <action-button-runner
            v-for="(button, i) in buttons"
            :key="buttonKey(button, i)"
            :field="button"
            :resource-name="resourceName"
            :via-resource="viaResource"
            :via-resource-id="viaResourceId"
        />
    </div>
</template>

<script setup>

    // Vue
    import {computed} from 'vue';

    // Components
    import ActionButtonRunner from './_components/ActionButtonRunner.vue';

    // Props
    const props = defineProps({
        field: {type: Object, required: true},
        resourceName: {type: String, required: true},
        viaResource: {type: String, default: null},
        viaResourceId: {type: [String, Number], default: null},
    });

    // Computed
    const buttons = computed(() => (props.field?.collection || []).filter(button => button?.action?.showOnIndex !== false));
    const alignClass = computed(() => `nab-buttons--${props.field?.textAlign || 'center'}`);

    // Methods
    const buttonKey = (button, i) => `${i}-${button?.action?.uriKey}-${button?.resourceId}`;

</script>
