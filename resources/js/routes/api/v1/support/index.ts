import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\SupportController::tickets
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
export const tickets = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tickets.url(options),
    method: 'post',
})

tickets.definition = {
    methods: ["post"],
    url: '/api/v1/support/tickets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\SupportController::tickets
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
tickets.url = (options?: RouteQueryOptions) => {
    return tickets.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SupportController::tickets
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
tickets.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tickets.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\SupportController::tickets
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
    const ticketsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: tickets.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SupportController::tickets
 * @see app/Http/Controllers/Api/V1/SupportController.php:10
 * @route '/api/v1/support/tickets'
 */
        ticketsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: tickets.url(options),
            method: 'post',
        })
    
    tickets.form = ticketsForm
const support = {
    tickets: Object.assign(tickets, tickets),
}

export default support