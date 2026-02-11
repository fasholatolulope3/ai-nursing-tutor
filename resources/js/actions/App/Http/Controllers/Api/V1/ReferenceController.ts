import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/references',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\ReferenceController::index
 * @see app/Http/Controllers/Api/V1/ReferenceController.php:14
 * @route '/api/v1/references'
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
const ReferenceController = { index }

export default ReferenceController