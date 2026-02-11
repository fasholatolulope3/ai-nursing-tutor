import ScenarioController from './ScenarioController'
import SimulationController from './SimulationController'
import NotificationController from './NotificationController'
import ProfileController from './ProfileController'
import SupportController from './SupportController'
import ReferenceController from './ReferenceController'
import RecommendationsController from './RecommendationsController'
const V1 = {
    ScenarioController: Object.assign(ScenarioController, ScenarioController),
SimulationController: Object.assign(SimulationController, SimulationController),
NotificationController: Object.assign(NotificationController, NotificationController),
ProfileController: Object.assign(ProfileController, ProfileController),
SupportController: Object.assign(SupportController, SupportController),
ReferenceController: Object.assign(ReferenceController, ReferenceController),
RecommendationsController: Object.assign(RecommendationsController, RecommendationsController),
}

export default V1