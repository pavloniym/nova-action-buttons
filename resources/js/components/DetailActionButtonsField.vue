<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="nab-buttons nab-buttons--left">
                <action-button-runner
                    v-for="(button, i) in buttons"
                    :key="buttonKey(button, i)"
                    :field="button"
                    :resource-name="resourceName"
                    is-on-detail
                />
            </div>
        </template>
    </PanelItem>
</template>

<script setup>

    // Vue
    import {computed} from 'vue';

    // Components
    import ActionButtonRunner from './_components/ActionButtonRunner.vue';

    // Props
    const props = defineProps({
        index: {type: Number, default: 0},
        field: {type: Object, required: true},
        resourceName: {type: String, required: true},
    });

    // Computed
    const buttons = computed(() => (props.field?.collection || []).filter(button => button?.action?.showOnDetail !== false));

    // Methods
    const buttonKey = (button, i) => `${i}-${button?.action?.uriKey}-${button?.resourceId}`;

</script>
