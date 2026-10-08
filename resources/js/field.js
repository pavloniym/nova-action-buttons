import ActionButtonField from './components/ActionButtonField.vue'
import ActionButtonsField from './components/ActionButtonsField.vue'
import DetailActionButtonField from './components/DetailActionButtonField.vue'
import DetailActionButtonsField from './components/DetailActionButtonsField.vue'

Nova.booting(app => {
    app.component('index-action-button', ActionButtonField)
    app.component('detail-action-button', DetailActionButtonField)
    app.component('index-action-buttons', ActionButtonsField)
    app.component('detail-action-buttons', DetailActionButtonsField)
})
