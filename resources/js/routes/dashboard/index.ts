import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import scenariosFa70f7 from './scenarios'
import history3c919d from './history'
/**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
export const settings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

settings.definition = {
    methods: ["get","head"],
    url: '/dashboard/settings',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
settings.url = (options?: RouteQueryOptions) => {
    return settings.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
settings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
settings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: settings.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
    const settingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: settings.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
        settingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: settings.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:18
 * @route '/dashboard/settings'
 */
        settingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: settings.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    settings.form = settingsForm
/**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
export const scenarios = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scenarios.url(options),
    method: 'get',
})

scenarios.definition = {
    methods: ["get","head"],
    url: '/dashboard/scenarios',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
scenarios.url = (options?: RouteQueryOptions) => {
    return scenarios.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
scenarios.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scenarios.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
scenarios.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scenarios.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
    const scenariosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: scenarios.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
        scenariosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: scenarios.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:22
 * @route '/dashboard/scenarios'
 */
        scenariosForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: scenarios.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    scenarios.form = scenariosForm
/**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
export const history = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

history.definition = {
    methods: ["get","head"],
    url: '/dashboard/history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
history.url = (options?: RouteQueryOptions) => {
    return history.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
history.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
history.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: history.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
    const historyForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: history.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
        historyForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: history.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\HistoryController::history
 * @see app/Http/Controllers/HistoryController.php:12
 * @route '/dashboard/history'
 */
        historyForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: history.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    history.form = historyForm
/**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
export const performance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: performance.url(options),
    method: 'get',
})

performance.definition = {
    methods: ["get","head"],
    url: '/dashboard/performance',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
performance.url = (options?: RouteQueryOptions) => {
    return performance.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
performance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: performance.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
performance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: performance.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
    const performanceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: performance.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
        performanceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: performance.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:31
 * @route '/dashboard/performance'
 */
        performanceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: performance.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    performance.form = performanceForm
/**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
export const guidelines = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: guidelines.url(options),
    method: 'get',
})

guidelines.definition = {
    methods: ["get","head"],
    url: '/dashboard/guidelines',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
guidelines.url = (options?: RouteQueryOptions) => {
    return guidelines.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
guidelines.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: guidelines.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
guidelines.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: guidelines.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
    const guidelinesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: guidelines.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
        guidelinesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: guidelines.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:35
 * @route '/dashboard/guidelines'
 */
        guidelinesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: guidelines.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    guidelines.form = guidelinesForm
/**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
export const examPrep = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: examPrep.url(options),
    method: 'get',
})

examPrep.definition = {
    methods: ["get","head"],
    url: '/dashboard/exam-prep',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
examPrep.url = (options?: RouteQueryOptions) => {
    return examPrep.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
examPrep.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: examPrep.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
examPrep.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: examPrep.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
    const examPrepForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: examPrep.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
        examPrepForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: examPrep.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:39
 * @route '/dashboard/exam-prep'
 */
        examPrepForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: examPrep.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    examPrep.form = examPrepForm
/**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
export const achievements = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: achievements.url(options),
    method: 'get',
})

achievements.definition = {
    methods: ["get","head"],
    url: '/dashboard/achievements',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
achievements.url = (options?: RouteQueryOptions) => {
    return achievements.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
achievements.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: achievements.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
achievements.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: achievements.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
    const achievementsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: achievements.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
        achievementsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: achievements.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:43
 * @route '/dashboard/achievements'
 */
        achievementsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: achievements.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    achievements.form = achievementsForm
/**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
export const help = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: help.url(options),
    method: 'get',
})

help.definition = {
    methods: ["get","head"],
    url: '/dashboard/help',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
help.url = (options?: RouteQueryOptions) => {
    return help.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
help.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: help.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
help.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: help.url(options),
    method: 'head',
})

    /**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
    const helpForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: help.url(options),
        method: 'get',
    })

            /**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
        helpForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: help.url(options),
            method: 'get',
        })
            /**
 * @see routes/web.php:47
 * @route '/dashboard/help'
 */
        helpForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: help.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    help.form = helpForm
const dashboard = {
    settings: Object.assign(settings, settings),
scenarios: Object.assign(scenarios, scenariosFa70f7),
history: Object.assign(history, history3c919d),
performance: Object.assign(performance, performance),
guidelines: Object.assign(guidelines, guidelines),
examPrep: Object.assign(examPrep, examPrep),
achievements: Object.assign(achievements, achievements),
help: Object.assign(help, help),
}

export default dashboard