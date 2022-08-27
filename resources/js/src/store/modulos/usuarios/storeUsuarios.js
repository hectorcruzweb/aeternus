import state from './storeUsuariosState'
import mutations from './storeUsuariosMutations'
import actions from './storeUsuariosActions'
import getters from './storeUsuariosGetters'

export default {
    isRegistered: false,
    namespaced: true,
    state: state,
    mutations: mutations,
    actions: actions,
    getters: getters
}
