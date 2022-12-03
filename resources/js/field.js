import ActionButtonField from './components/ActionButtonField'
import ActionButtonsField from './components/ActionButtonsField'

Nova.booting((app, store) => {
    app.component('index-action-button', ActionButtonField)
    app.component('index-action-buttons', ActionButtonsField)
})
