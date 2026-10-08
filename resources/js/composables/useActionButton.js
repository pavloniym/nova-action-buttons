import { computed, nextTick, reactive } from 'vue'
import { Errors, useLocalization } from 'laravel-nova'

const { __ } = useLocalization()

/**
 * Runs a Nova action for a single resource, mirroring Nova 5's own
 * `useActions` composable (which Nova does not expose to packages).
 *
 * The action is read from the field when the button is clicked, never captured once:
 * after Nova reloads the resources the field receives fresh action objects.
 * While the confirmation modal is open, the action it was opened with is kept,
 * because the modal's form fields attached their `fill` callbacks to that object.
 *
 * @param {() => object|null} getAction
 * @param {{ resourceName: () => string, resourceId: () => (string|number), isOnDetail: () => boolean, queryParams?: () => object }} options
 */
export function useActionButton(getAction, { resourceName, resourceId, isOnDetail, queryParams = () => ({}) }) {
    const state = reactive({
        working: false,
        errors: new Errors(),
        actionModalVisible: false,
        modalAction: null,
        responseModalVisible: false,
        responseModalData: null,
    })

    const action = computed(() => getAction() ?? null)
    const endpoint = computed(() => `/nova-api/${resourceName()}/action`)
    const selectedResources = computed(() => [resourceId()])

    function fireAction() {
        if (!action.value || state.working) {
            return
        }

        action.value.withoutConfirmation ? executeAction() : openConfirmationModal()
    }

    function openConfirmationModal() {
        state.errors = new Errors()
        state.modalAction = action.value
        state.actionModalVisible = true
    }

    function closeConfirmationModal() {
        state.actionModalVisible = false
        state.modalAction = null
    }

    function closeResponseModal() {
        state.responseModalVisible = false
        state.responseModalData = null
    }

    function actionFormData(currentAction) {
        const formData = new FormData()

        selectedResources.value.forEach(id => formData.append('resources[]', id))

        ;(currentAction.fields || []).forEach(field => {
            if (typeof field.fill === 'function') {
                return field.fill(formData)
            }

            // The field was never mounted (e.g. an action without confirmation): send its default value.
            const value = field.value

            if (value === null || value === undefined) {
                return
            }

            if (typeof value === 'boolean') {
                return formData.append(field.attribute, value ? '1' : '0')
            }

            formData.append(field.attribute, typeof value === 'object' ? JSON.stringify(value) : String(value))
        })

        return formData
    }

    function executeAction() {
        const currentAction = state.actionModalVisible ? state.modalAction : action.value

        if (!currentAction || state.working) {
            return
        }

        const responseType = currentAction.responseType ?? 'json'

        state.working = true
        Nova.$progress.start()

        return Promise.resolve()
            .then(() =>
                Nova.request({
                    method: 'post',
                    url: endpoint.value,
                    params: { ...queryParams(), action: currentAction.uriKey },
                    data: actionFormData(currentAction),
                    responseType,
                })
            )
            .then(response => {
                closeConfirmationModal()
                handleActionResponse(response.data, response.headers)
            })
            .catch(error => {
                const status = error?.response?.status

                if (status >= 400 && status < 500) {
                    if (responseType === 'blob') {
                        error.response.data.text().then(data => {
                            try {
                                state.errors = new Errors(JSON.parse(data).errors)
                            } catch {
                                state.errors = new Errors()
                            }
                        })
                    } else {
                        state.errors = new Errors(error.response.data?.errors)
                    }

                    Nova.error(__('There was a problem executing the action.'))
                } else if (!error?.response) {
                    console.error(error)
                    Nova.error(__('There was a problem executing the action.'))
                }
            })
            .finally(() => {
                state.working = false
                Nova.$progress.done()
            })
    }

    function refresh(deleted = false) {
        Nova.$emit('action-executed')
        Nova.$emit('refresh-resources')

        if (isOnDetail()) {
            Nova.visit(deleted ? `/resources/${resourceName()}` : `/resources/${resourceName()}/${resourceId()}`)
        }
    }

    function emitResponseCallback(callback, deleted = false) {
        if (typeof callback === 'function') {
            callback()
        }

        refresh(deleted)
    }

    function showActionResponseMessage(data) {
        if (data.danger) {
            return Nova.error(data.danger)
        }

        Nova.success(data.message || __('The action was executed successfully.'))
    }

    function download(href, fileName) {
        nextTick(() => {
            const link = document.createElement('a')
            link.href = href
            link.download = fileName
            document.body.appendChild(link)
            link.click()
            link.remove()
        })
    }

    function handleActionResponse(data, headers) {
        const contentDisposition = headers?.['content-disposition']

        if (data instanceof Blob && contentDisposition == null && data.type === 'application/json') {
            return data.text().then(json => handleActionResponse(JSON.parse(json), headers))
        }

        if (data instanceof Blob) {
            return emitResponseCallback(() => {
                const match = contentDisposition?.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i)
                const url = window.URL.createObjectURL(new Blob([data]))

                download(url, match ? decodeURIComponent(match[1]) : 'unknown')
                nextTick(() => window.URL.revokeObjectURL(url))
            })
        }

        if (data.event) {
            Nova.$emit(data.event.key, data.event.payload)
        }

        if (data.modal) {
            state.responseModalData = data.modal
            state.responseModalVisible = true

            return showActionResponseMessage(data)
        }

        if (data.download) {
            return emitResponseCallback(() => {
                showActionResponseMessage(data)
                download(data.download.url, data.download.name)
            })
        }

        if (data.deleted) {
            return emitResponseCallback(() => showActionResponseMessage(data), true)
        }

        if (data.redirect) {
            if (data.redirect.openInNewTab) {
                return emitResponseCallback(() => window.open(data.redirect.url, '_blank'))
            }

            window.location = data.redirect.url

            return
        }

        if (data.visit) {
            showActionResponseMessage(data)

            return Nova.visit({ url: Nova.url(data.visit.path, data.visit.options), remote: false })
        }

        emitResponseCallback(() => showActionResponseMessage(data))
    }

    return {
        action,
        selectedResources,
        modalAction: computed(() => state.modalAction),
        errors: computed(() => state.errors),
        working: computed(() => state.working),
        actionModalVisible: computed(() => state.actionModalVisible),
        responseModalVisible: computed(() => state.responseModalVisible),
        responseModalData: computed(() => state.responseModalData),
        fireAction,
        executeAction,
        closeConfirmationModal,
        closeResponseModal,
    }
}
