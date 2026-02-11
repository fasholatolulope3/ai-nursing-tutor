import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/simulations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::index
 * @see app/Http/Controllers/Api/V1/SimulationController.php:23
 * @route '/api/v1/simulations'
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
* @see \App\Http\Controllers\Api\V1\SimulationController::start
 * @see app/Http/Controllers/Api/V1/SimulationController.php:37
 * @route '/api/v1/simulations/start'
 */
export const start = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(options),
    method: 'post',
})

start.definition = {
    methods: ["post"],
    url: '/api/v1/simulations/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::start
 * @see app/Http/Controllers/Api/V1/SimulationController.php:37
 * @route '/api/v1/simulations/start'
 */
start.url = (options?: RouteQueryOptions) => {
    return start.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::start
 * @see app/Http/Controllers/Api/V1/SimulationController.php:37
 * @route '/api/v1/simulations/start'
 */
start.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::start
 * @see app/Http/Controllers/Api/V1/SimulationController.php:37
 * @route '/api/v1/simulations/start'
 */
    const startForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: start.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::start
 * @see app/Http/Controllers/Api/V1/SimulationController.php:37
 * @route '/api/v1/simulations/start'
 */
        startForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: start.url(options),
            method: 'post',
        })
    
    start.form = startForm
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::store
 * @see app/Http/Controllers/Api/V1/SimulationController.php:67
 * @route '/api/v1/simulations'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/simulations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::store
 * @see app/Http/Controllers/Api/V1/SimulationController.php:67
 * @route '/api/v1/simulations'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::store
 * @see app/Http/Controllers/Api/V1/SimulationController.php:67
 * @route '/api/v1/simulations'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::store
 * @see app/Http/Controllers/Api/V1/SimulationController.php:67
 * @route '/api/v1/simulations'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::store
 * @see app/Http/Controllers/Api/V1/SimulationController.php:67
 * @route '/api/v1/simulations'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/v1/simulations/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
    const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
        showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::show
 * @see app/Http/Controllers/Api/V1/SimulationController.php:75
 * @route '/api/v1/simulations/{id}'
 */
        showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::chat
 * @see app/Http/Controllers/Api/V1/SimulationController.php:93
 * @route '/api/v1/simulations/{id}/chat'
 */
export const chat = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(args, options),
    method: 'post',
})

chat.definition = {
    methods: ["post"],
    url: '/api/v1/simulations/{id}/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::chat
 * @see app/Http/Controllers/Api/V1/SimulationController.php:93
 * @route '/api/v1/simulations/{id}/chat'
 */
chat.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return chat.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::chat
 * @see app/Http/Controllers/Api/V1/SimulationController.php:93
 * @route '/api/v1/simulations/{id}/chat'
 */
chat.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::chat
 * @see app/Http/Controllers/Api/V1/SimulationController.php:93
 * @route '/api/v1/simulations/{id}/chat'
 */
    const chatForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: chat.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::chat
 * @see app/Http/Controllers/Api/V1/SimulationController.php:93
 * @route '/api/v1/simulations/{id}/chat'
 */
        chatForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: chat.url(args, options),
            method: 'post',
        })
    
    chat.form = chatForm
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::handleClinicalQuery
 * @see app/Http/Controllers/Api/V1/SimulationController.php:157
 * @route '/api/v1/simulations/clinical-query'
 */
export const handleClinicalQuery = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleClinicalQuery.url(options),
    method: 'post',
})

handleClinicalQuery.definition = {
    methods: ["post"],
    url: '/api/v1/simulations/clinical-query',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::handleClinicalQuery
 * @see app/Http/Controllers/Api/V1/SimulationController.php:157
 * @route '/api/v1/simulations/clinical-query'
 */
handleClinicalQuery.url = (options?: RouteQueryOptions) => {
    return handleClinicalQuery.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::handleClinicalQuery
 * @see app/Http/Controllers/Api/V1/SimulationController.php:157
 * @route '/api/v1/simulations/clinical-query'
 */
handleClinicalQuery.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleClinicalQuery.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::handleClinicalQuery
 * @see app/Http/Controllers/Api/V1/SimulationController.php:157
 * @route '/api/v1/simulations/clinical-query'
 */
    const handleClinicalQueryForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: handleClinicalQuery.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::handleClinicalQuery
 * @see app/Http/Controllers/Api/V1/SimulationController.php:157
 * @route '/api/v1/simulations/clinical-query'
 */
        handleClinicalQueryForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: handleClinicalQuery.url(options),
            method: 'post',
        })
    
    handleClinicalQuery.form = handleClinicalQueryForm
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
export const getLastInteraction = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLastInteraction.url(options),
    method: 'get',
})

getLastInteraction.definition = {
    methods: ["get","head"],
    url: '/api/v1/simulations/clinical-query/last',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
getLastInteraction.url = (options?: RouteQueryOptions) => {
    return getLastInteraction.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
getLastInteraction.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLastInteraction.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
getLastInteraction.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLastInteraction.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
    const getLastInteractionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getLastInteraction.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
        getLastInteractionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getLastInteraction.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::getLastInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:220
 * @route '/api/v1/simulations/clinical-query/last'
 */
        getLastInteractionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getLastInteraction.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getLastInteraction.form = getLastInteractionForm
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
export const getInteraction = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInteraction.url(args, options),
    method: 'get',
})

getInteraction.definition = {
    methods: ["get","head"],
    url: '/api/v1/simulations/clinical-query/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
getInteraction.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getInteraction.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
getInteraction.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInteraction.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
getInteraction.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getInteraction.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
    const getInteractionForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: getInteraction.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
        getInteractionForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInteraction.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\V1\SimulationController::getInteraction
 * @see app/Http/Controllers/Api/V1/SimulationController.php:208
 * @route '/api/v1/simulations/clinical-query/{id}'
 */
        getInteractionForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: getInteraction.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    getInteraction.form = getInteractionForm
const SimulationController = { index, start, store, show, chat, handleClinicalQuery, getLastInteraction, getInteraction }

export default SimulationController