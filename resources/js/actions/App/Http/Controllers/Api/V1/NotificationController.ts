import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\NotificationController::index
 * @see app/Http/Controllers/Api/V1/NotificationController.php:14
 * @route '/api/v1/notifications'
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
* @see \App\Http\Controllers\Api\V1\NotificationController::markAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:39
 * @route '/api/v1/notifications/{id}/read'
 */
export const markAsRead = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: markAsRead.url(args, options),
    method: 'put',
})

markAsRead.definition = {
    methods: ["put"],
    url: '/api/v1/notifications/{id}/read',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:39
 * @route '/api/v1/notifications/{id}/read'
 */
markAsRead.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return markAsRead.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:39
 * @route '/api/v1/notifications/{id}/read'
 */
markAsRead.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: markAsRead.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:39
 * @route '/api/v1/notifications/{id}/read'
 */
    const markAsReadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markAsRead.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:39
 * @route '/api/v1/notifications/{id}/read'
 */
        markAsReadForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markAsRead.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    markAsRead.form = markAsReadForm
/**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:50
 * @route '/api/v1/notifications/read-all'
 */
export const markAllAsRead = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: markAllAsRead.url(options),
    method: 'put',
})

markAllAsRead.definition = {
    methods: ["put"],
    url: '/api/v1/notifications/read-all',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:50
 * @route '/api/v1/notifications/read-all'
 */
markAllAsRead.url = (options?: RouteQueryOptions) => {
    return markAllAsRead.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:50
 * @route '/api/v1/notifications/read-all'
 */
markAllAsRead.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: markAllAsRead.url(options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:50
 * @route '/api/v1/notifications/read-all'
 */
    const markAllAsReadForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: markAllAsRead.url({
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\NotificationController::markAllAsRead
 * @see app/Http/Controllers/Api/V1/NotificationController.php:50
 * @route '/api/v1/notifications/read-all'
 */
        markAllAsReadForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: markAllAsRead.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    markAllAsRead.form = markAllAsReadForm
const NotificationController = { index, markAsRead, markAllAsRead }

export default NotificationController