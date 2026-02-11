import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\SupportController::store
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/support/tickets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\SupportController::store
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SupportController::store
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\SupportController::store
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SupportController::store
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
const SupportController = { store }

export default SupportController