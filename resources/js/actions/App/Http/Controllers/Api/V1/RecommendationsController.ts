import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\RecommendationsController::index
 * @see app/Http/Controllers/Api/V1/RecommendationsController.php:18
 * @route '/api/v1/recommendations'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: index.url(options),
    method: 'post',
})

index.definition = {
    methods: ["post"],
    url: '/api/v1/recommendations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\RecommendationsController::index
 * @see app/Http/Controllers/Api/V1/RecommendationsController.php:18
 * @route '/api/v1/recommendations'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\RecommendationsController::index
 * @see app/Http/Controllers/Api/V1/RecommendationsController.php:18
 * @route '/api/v1/recommendations'
 */
index.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: index.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\RecommendationsController::index
 * @see app/Http/Controllers/Api/V1/RecommendationsController.php:18
 * @route '/api/v1/recommendations'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: index.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\RecommendationsController::index
 * @see app/Http/Controllers/Api/V1/RecommendationsController.php:18
 * @route '/api/v1/recommendations'
 */
        indexForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: index.url(options),
            method: 'post',
        })
    
    index.form = indexForm
const RecommendationsController = { index }

export default RecommendationsController