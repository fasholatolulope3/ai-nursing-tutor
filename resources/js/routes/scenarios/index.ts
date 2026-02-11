import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/scenarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\ScenarioController::index
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:86
 * @route '/api/v1/scenarios'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
export const show = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/v1/scenarios/{scenario}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
show.url = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    scenario: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        scenario: args.scenario,
                }

    return show.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
show.get = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
show.head = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
    const showForm = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
        showForm.get = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\ScenarioController::show
 * @see app/Http/Controllers/Api/V1/ScenarioController.php:94
 * @route '/api/v1/scenarios/{scenario}'
 */
        showForm.head = (args: { scenario: string | number } | [scenario: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
const scenarios = {
    index: Object.assign(index, index),
show: Object.assign(show, show),
}

export default scenarios