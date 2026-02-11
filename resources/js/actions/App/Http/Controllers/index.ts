import Api from './Api'
import GeminiController from './GeminiController'
import ScenarioController from './ScenarioController'
import HistoryController from './HistoryController'
import Settings from './Settings'
const Controllers = {
    Api: Object.assign(Api, Api),
GeminiController: Object.assign(GeminiController, GeminiController),
ScenarioController: Object.assign(ScenarioController, ScenarioController),
HistoryController: Object.assign(HistoryController, HistoryController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers