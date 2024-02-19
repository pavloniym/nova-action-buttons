import tap from 'lodash/tap'
import each from 'lodash/each'
import isNil from 'lodash/isNil'

import {ref} from "vue";
import {Errors} from 'form-backend-validation'

export const useHandleAction = ({queryString, resourceName, selectedAction, selectedResources}) => {

    // State
    const errors = ref(new Errors());
    const working = ref(false);
    const confirmActionModalOpened = ref(false);
    const reloadOnModalClose = ref(false);

    const __ = (key) => Nova?.appConfig?.translations?.[key] || key;

    /**
     * Determine whether the action should redirect or open a confirmation modal
     *
     * @return {void}
     */
    const fireAction = (e, reload = false) => {
        selectedAction?.withoutConfirmation === true
            ? executeAction(reload)
            : openConfirmationModal(reload)
    }


    /**
     * Confirm with the user that they actually want to run the selected action.
     *
     * @return {void}
     */
    const openConfirmationModal = (reload = false) => {
        confirmActionModalOpened.value = true
        reloadOnModalClose.value = reload
    }


    /**
     * Close the action confirmation modal.
     *
     * @return {void}
     */
    const closeConfirmationModal = () => {
        confirmActionModalOpened.value = false
    }


    /**
     * Execute the selected action.
     *
     * @return {void}
     */
    const executeAction = (reload = false) => {
        working.value = true
        Nova.$progress.start()

        let responseType = selectedAction.responseType ?? 'json'

        Nova.request(
            {
                url: `/nova-api/${resourceName}/action`,
                data: _actionFormData(),
                method: 'post',
                params: {
                    action: queryString?.action,
                    search: queryString?.search,
                    filters: queryString?.filters,
                    trashed: queryString?.trashed,
                    pivotAction: queryString?.pivotAction,
                    viaResource: queryString?.viaResource,
                    viaResourceId: queryString?.viaResourceId,
                    viaRelationship: queryString?.viaRelationship,
                },
                responseType,
            })
            .then(async response => {

                confirmActionModalOpened.value = false
                //await this.fetchPolicies()
                _handleActionResponse(response)

                working.value = false
                Nova.$progress.done()

                if (reload || reloadOnModalClose.value) {
                    window.location = window.location.href
                }

            })
            .catch(error => {

                working.value = false
                Nova.$progress.done()

                if (error?.response?.status === 422) {
                    if (responseType === 'blob') {

                        error?.response?.data?.text()?.then(data => {
                            errors.value = new Errors(JSON.parse(data).errors)
                        })

                    } else {

                        errors.value = new Errors(error.response.data.errors)
                    }

                    Nova.error(__('There was a problem executing the action.'));

                    (Object.values(errors.value?.all() || {}))
                        .filter(value => value)
                        .forEach(error => Nova.error(error));

                }
            })
    }


    /**
     * Gather the action FormData for the given action.
     *
     * @return {*}
     */
    const _actionFormData = () => {
        return tap(new FormData(), formData => {
            formData.append('resources', selectedResources)
            each(selectedAction?.fields, field => {
                field.fill(formData)
            })
        })
    }


    /**
     * Emit response callback
     *
     * @param callback
     * @private
     */
    const _emitResponseCallback = (callback) => {

        Nova.$emit('action-executed')
        Nova.$emit('refresh-resources')

        if (typeof callback === 'function') {
            callback()
        }
    }


    /**
     * Handle the action response. Typically either a message, download or a redirect.
     *
     * @param data
     * @param headers
     * @private
     */
    const _handleActionResponse = ({data, headers}) => {

        let contentDisposition = headers['content-disposition']

        if (data instanceof Blob && isNil(contentDisposition) && data.type === 'application/json') {
            data.text().then(jsonStringData => _handleActionResponse({data: JSON.parse(jsonStringData), headers}))
            return
        }

        if (data instanceof Blob) {
            _emitResponseCallback(() => {

                let fileName = 'unknown'
                let url = window.URL.createObjectURL(new Blob([data]))
                let link = document.createElement('a')
                link.href = url

                if (contentDisposition) {
                    let fileNameMatch = contentDisposition.match(/filename="(.+)"/)
                    if (fileNameMatch.length === 2) fileName = fileNameMatch[1]
                }

                link.setAttribute('download', fileName)
                document.body.appendChild(link)
                link.click()
                link.remove()
                window.URL.revokeObjectURL(url)
            })

        } else if (data.modal) {

            //this.actionResponseData = data
            //this.showActionResponseModal = true

        } else if (data.message) {
            _emitResponseCallback(() => Nova.success(data.message))

        } else if (data.deleted) {
            _emitResponseCallback()

        } else if (data.danger) {
            _emitResponseCallback(() => Nova.error(data.danger))

        } else if (data.download) {
            _emitResponseCallback(() => {
                let link = document.createElement('a')
                link.href = data.download
                link.download = data.name
                document.body.appendChild(link)
                link.click()
                document.body.removeChild(link)
            })

        } else if (data.redirect) {
            window.location = data.redirect

        } else if (data.visit) {
            Nova.visit({url: Nova.url(data.visit.path, data.visit.options), remote: false})

        } else if (data.openInNewTab) {
            _emitResponseCallback(() => window.open(data.openInNewTab, '_blank'))

        } else {
            _emitResponseCallback(() => Nova.success(data.message || __('The action was executed successfully.')))

        }
    }


    return {
        errors,
        working,
        fireAction,
        executeAction,
        closeConfirmationModal,
        confirmActionModalOpened
    }
}
