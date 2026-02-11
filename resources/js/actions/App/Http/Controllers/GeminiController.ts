import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GeminiController::generate
 * @see app/Http/Controllers/GeminiController.php:22
 * @route '/api/v1/gemini'
 */
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/v1/gemini',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GeminiController::generate
 * @see app/Http/Controllers/GeminiController.php:22
 * @route '/api/v1/gemini'
 */
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GeminiController::generate
 * @see app/Http/Controllers/GeminiController.php:22
 * @route '/api/v1/gemini'
 */
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\GeminiController::generate
 * @see app/Http/Controllers/GeminiController.php:22
 * @route '/api/v1/gemini'
 */
    const generateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: generate.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\GeminiController::generate
 * @see app/Http/Controllers/GeminiController.php:22
 * @route '/api/v1/gemini'
 */
        generateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: generate.url(options),
            method: 'post',
        })
    
    generate.form = generateForm
const GeminiController = { generate }

export default GeminiController